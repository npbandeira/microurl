<?php

namespace MicroUrl\Repository;

use Predis\Client;

abstract class RedisRepository
{
    protected $redis;
    protected $prefix;

    public function __construct(Client $redis, string $prefix)
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    protected function getKey(string $id): string
    {
        return "{$this->prefix}:{$id}";
    }
}