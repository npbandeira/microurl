<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;

// Configuração do container de dependências
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// Cria a aplicação Slim
$app = (require __DIR__ . '/../config/app.php')($container);

// Carrega as rotas
(require __DIR__ . '/../route/routes.php')($app);

$app->run();