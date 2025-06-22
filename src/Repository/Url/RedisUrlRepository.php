<?php

namespace MicroUrl\Repository\Url;

use Predis\Client;


class RedisUrlRepository implements UrlRepositoryInterface
{


    private Client $redis;
    private string $prefix;
    private int $defaultTtl;

    public function __construct(Client $redis, string $prefix, int $defaultTtl = 86400)
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->defaultTtl = $defaultTtl;
    }

    public function findByOriginalUrl(string $originalUrl): ?array
    {
        $key = $this->prefix . 'url:' . md5($originalUrl);
        $data = $this->redis->get($key);

        return $data ? json_decode($data, true) : null;
    }

    public function findByShortCode(string $shortCode): ?array
    {
        $key = $this->prefix . 'code:' . $shortCode;
        $data = $this->redis->get($key);

        if (!$data) {
            return null;
        }

        $data = json_decode($data, true);

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
            $data['expires_at'] = time() + $this->defaultTtl;
        }

        $ttl = $data['expires_at'] - time();
        $jsonData = json_encode($data);

        // Salva URL original
        $urlKey = $this->prefix . 'url:' . md5($data['original_url']);
        $this->redis->setex($urlKey, $ttl, $jsonData);

        // Salva código curto
        $codeKey = $this->prefix . 'code:' . $shortCode;
        $this->redis->setex($codeKey, $ttl, $jsonData);

        return $data;
    }

    public function incrementVisits(string $shortCode): bool
    {
        $key = $this->prefix . 'code:' . $shortCode;
        $data = $this->findByShortCode($shortCode);

        if (!$data) {
            return false;
        }

        $data['visits']++;
        $jsonData = json_encode($data);
        $ttl = $data['expires_at'] - time();

        // Atualiza ambas as chaves
        $this->redis->setex($key, $ttl, $jsonData);

        $urlKey = $this->prefix . 'url:' . md5($data['original_url']);
        $this->redis->setex($urlKey, $ttl, $jsonData);

        return true;
    }

    public function generateUniqueCode(): string
    {
        do {
            $code = $this->generateCode();
            $exists = $this->redis->exists($this->prefix . 'code:' . $code);
        } while ($exists);

        return $code;
    }

    private function generateCode(int $length = 8): string
    {
        $bytes = random_bytes(4); // 4 bytes = 8 caracteres hexadecimais
        return bin2hex($bytes);

    }
}