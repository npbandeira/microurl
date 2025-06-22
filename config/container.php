<?php

declare(strict_types=1);

use MicroUrl\Model\Url;
use MicroUrl\Repository\Url\RedisUrlRepository;
use MicroUrl\Repository\Url\UrlRepositoryInterface;
use Predis\Client as RedisClient;
use Psr\Container\ContainerInterface;

return [
    'settings' => [
        // ... outros settings
        'redis' => require __DIR__ . '/env.php',
    ],

    RedisClient::class => function (ContainerInterface $c) {
        $settings = $c->get('settings')['redis'];
        return new RedisClient([
            'scheme' => 'tcp',
            'host' => $settings['host'],
            'port' => $settings['port'],
            'password' => $settings['password'],
            'database' => $settings['database'],
        ]);
    },

    UrlRepositoryInterface::class => function (ContainerInterface $c) {
        $settings = $c->get('settings')['redis'];
        return new RedisUrlRepository(
            $c->get(RedisClient::class),
            $settings['prefix'] ?? 'micro-url:',
            $settings['default_ttl'] ?? 86400
        );
    },

    Url::class => function (ContainerInterface $c) {
        return new Url(
            $c->get(UrlRepositoryInterface::class)
        );
    }
];