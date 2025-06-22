<?php

declare(strict_types=1);

use MicroUrl\View\JsonResponse;
use Slim\App;
use MicroUrl\Controller\UrlController;
use MicroUrl\Controller\RedirectController;
use MicroUrl\Controller\NotFoundController;

return function (App $app) {

    // Rota para redirecionamento (código curto)
    $app->get('/{shortCode:[a-zA-Z0-9]{8}}', [RedirectController::class, 'handle']);

    // Grupo de rotas da API
    $app->group('/api', function ($group) {
        // Rotas para micro-url
        $group->group('/micro-url', function ($group) {
            // POST - Criar nova URL encurtada
            $group->post('', [UrlController::class, 'createShortUrl']);

            // GET - Obter informações da URL encurtada
            $group->get('/{shortCode:[a-zA-Z0-9]{8}}', [UrlController::class, 'getUrlInfo']);
        });
    });

    // Rota de fallback para 404 - captura todas as outras rotas
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], '/{routes:.+}', function ($request, $response) {
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    });
};

