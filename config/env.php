<?php

use Dotenv\Dotenv;

// Carrega as variáveis de ambiente do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Define valores padrão para variáveis não definidas
$dotenv->required([
    'REDIS_HOST',
    'REDIS_PORT',
    'REDIS_DATABASE',
    'REDIS_PREFIX',
    'REDIS_DEFAULT_TTL'
])->notEmpty();

// Retorna as configurações do ambiente
return [
    'redis' => [
        'host' => $_ENV['REDIS_HOST'],
        'port' => (int) $_ENV['REDIS_PORT'],
        'database' => (int) $_ENV['REDIS_DATABASE'],
        'prefix' => $_ENV['REDIS_PREFIX'],
        'default_ttl' => (int) $_ENV['REDIS_DEFAULT_TTL']
    ],
];