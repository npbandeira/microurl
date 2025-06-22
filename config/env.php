<?php
declare(strict_types=1);

use Dotenv\Dotenv;

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurações padrão
if (!isset($_ENV['APP_ENV'])) {
    $_ENV['APP_ENV'] = 'development';
}

if (!isset($_ENV['APP_DEBUG'])) {
    $_ENV['APP_DEBUG'] = 'true';
}

// Configurações do Redis
if (!isset($_ENV['REDIS_HOST'])) {
    $_ENV['REDIS_HOST'] = 'localhost';
}

if (!isset($_ENV['REDIS_PORT'])) {
    $_ENV['REDIS_PORT'] = '6379';
}

if (!isset($_ENV['REDIS_PASSWORD'])) {
    $_ENV['REDIS_PASSWORD'] = null;
}

if (!isset($_ENV['REDIS_DATABASE'])) {
    $_ENV['REDIS_DATABASE'] = '0';
}

if (!isset($_ENV['REDIS_PREFIX'])) {
    $_ENV['REDIS_PREFIX'] = 'microurl:';
}


// Retorna as configurações do ambiente
return [
    'host' => $_ENV['REDIS_HOST'],
    'port' => (int) $_ENV['REDIS_PORT'],
    'password' => $_ENV['REDIS_PASSWORD'] ?? null,
    'database' => (int) $_ENV['REDIS_DATABASE'],
    'prefix' => $_ENV['REDIS_PREFIX'],
];