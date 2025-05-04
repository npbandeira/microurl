<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

use MicroUrl\Controller\UrlController;
use MicroUrl\Controller\RedirectController;
use MicroUrl\View\JsonResponse;

// Configuração de timezone
date_default_timezone_set('America/Manaus');

// Tratamento de requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($path, '/');

    // Rota para redirecionamento
    if (preg_match('/^[a-zA-Z0-9]{8}$/', $path)) {
        $controller = new RedirectController();
        $controller->handle($path);
        exit;
    }

    // Rota para API
    if (strpos($path, 'api/micro-url') === 0) {
        $controller = new UrlController();
        $controller->handleRequest();
        exit;
    }

    // Rota não encontrada
    JsonResponse::notFound('Endpoint não encontrado');

} catch (Exception $e) {
    JsonResponse::serverError('Erro interno do servidor');
}