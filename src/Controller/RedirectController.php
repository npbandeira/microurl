<?php

namespace MicroUrl\Controller;

use MicroUrl\Model\Url;
use MicroUrl\View\JsonResponse;

class RedirectController
{
    private $urlModel;

    public function __construct()
    {
        $this->urlModel = new Url();
    }

    public function handle(string $shortCode): void
    {
        try {
            // Valida o código
            if (!$this->isValidShortCode($shortCode)) {
                JsonResponse::error('Código inválido', 400);
            }

            // Busca a URL
            $url = $this->urlModel->findByShortCode($shortCode);
            if (!$url) {
                JsonResponse::notFound('URL não encontrada');
            }

            // Verifica se a URL expirou
            if (isset($url['expires_at']) && $url['expires_at'] < time()) {
                JsonResponse::error('URL expirada', 410);
            }

            // Incrementa o contador de visitas
            $this->urlModel->incrementVisits($shortCode);
            // Retorna os dados da URL
            header('Location: ' . $url['original_url'], true, 301);
            exit;
        } catch (\Exception $e) {
            JsonResponse::serverError('Erro ao processar a requisição');
        }
    }

    private function isValidShortCode(string $code): bool
    {
        return preg_match('/^[a-zA-Z0-9]{8}$/', $code) === 1;
    }
}