<?php

namespace MicroUrl;

use Redis;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private Redis $redis;
    private string $prefix;
    private int $defaultTtl;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/env.php';
        $redisConfig = $config['redis'];

        $this->redis = new Redis();
        $this->redis->connect(
            $redisConfig['host'],
            $redisConfig['port']
        );

        if ($redisConfig['database'] > 0) {
            $this->redis->select($redisConfig['database']);
        }

        // Configura o Redis para serializar/deserializar automaticamente em JSON
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_JSON);

        // Configura o prefixo para as chaves
        $this->redis->setOption(Redis::OPT_PREFIX, $redisConfig['prefix']);

        $this->prefix = $redisConfig['prefix'];
        $this->defaultTtl = $redisConfig['default_ttl'];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getDefaultTtl(): int
    {
        return $this->defaultTtl;
    }

    private function __clone()
    {
    }
    private function __wakeup()
    {
    }
}