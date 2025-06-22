<?php

declare(strict_types=1);

use DI\Definition\Source\SourceCache;
use Slim\Factory\AppFactory;
use DI\Container;

return function (Container $container) {
    // Configuração de timezone
    date_default_timezone_set('America/Manaus');


    // Create App from container
    $app = AppFactory::createFromContainer($container);

    // Add Twig-View Middleware
    // Cria a aplicação Slim
    $app = AppFactory::createFromContainer($container);

    $app->addRoutingMiddleware();
    // Adiciona middleware de parsing JSON
    $app->addBodyParsingMiddleware();

    // Adiciona middleware de erro
    $errorMiddleware = $app->addErrorMiddleware(
        true,
        true,
        true
    );

    return $app;
};