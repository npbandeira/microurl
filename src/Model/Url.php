<?php

namespace MicroUrl\Model;

use MicroUrl\Database;
use Exception;

class Url
{
    private $redis;
    private $prefix;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->redis = $db->getRedis();
        $this->prefix = $db->getPrefix();
    }

    public function findByOriginalUrl(string $originalUrl): ?array
    {
        $key = 'url:' . md5($originalUrl);
        $data = $this->redis->get($key);
        return $data ?: null;
    }

    public function findByShortCode(string $shortCode): ?array
    {
        $key = 'code:' . $shortCode;
        $data = $this->redis->get($key);

        if (!$data) {
            return null;
        }

        // Verifica expiração
        if (isset($data['expires_at']) && $data['expires_at'] < time()) {
            $this->redis->del($key);
            return null;
        }

        return $data;
    }

    public function create(array $data): array
    {
        $shortCode = $this->generateUniqueCode();
        $data['short_code'] = $shortCode;
        $data['created_at'] = time();
        $data['visits'] = 0;

        // Define expiração padrão se não especificada
        if (!isset($data['expires_at'])) {
            $data['expires_at'] = time() + Database::getInstance()->getDefaultTtl();
        }

        $ttl = $data['expires_at'] - time();

        // Salva URL original
        $urlKey = 'url:' . md5($data['original_url']);
        $this->redis->set($urlKey, $data);
        $this->redis->expire($urlKey, $ttl);

        // Salva código curto
        $codeKey = 'code:' . $shortCode;
        $this->redis->set($codeKey, $data);
        $this->redis->expire($codeKey, $ttl);

        // Retorna os dados salvos no Redis
        return $this->findByShortCode($shortCode);
    }

    public function incrementVisits(string $shortCode): bool
    {
        $key = 'code:' . $shortCode;

        // Busca os dados atuais
        $data = $this->redis->get($key);
        if (!$data) {
            return false;
        }

        // Incrementa o contador de visitas
        $data['visits']++;

        // Atualiza os dados no Redis para ambas as chaves
        $this->redis->set($key, $data);

        // Atualiza também a chave da URL original
        $urlKey = 'url:' . md5($data['original_url']);
        $this->redis->set($urlKey, $data);

        return true;
    }

    public function generateUniqueCode(): string
    {
        do {
            $code = $this->generateCode();
            $exists = $this->redis->exists('code:' . $code);
        } while ($exists);

        return $code;
    }

    private function generateCode(int $length = 8): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }
}