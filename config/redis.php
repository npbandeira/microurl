<?php

return [
    'host' => getenv('REDIS_HOST') ?: 'localhost',
    'port' => (int) (getenv('REDIS_PORT') ?: 6379),
    'database' => (int) (getenv('REDIS_DATABASE') ?: 0),
    'prefix' => getenv('REDIS_PREFIX') ?: 'url:',
    'default_ttl' => (int) (getenv('REDIS_DEFAULT_TTL') ?: 2592000), // 30 dias em segundos
    'read_write_timeout' => 0
];