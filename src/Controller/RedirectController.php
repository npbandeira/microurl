<?php
declare(strict_types=1);

namespace MicroUrl\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MicroUrl\Model\Url;
use MicroUrl\View\JsonResponse;

class RedirectController
{
    private Url $urlModel;

    public function __construct(Url $urlModel)
    {
        $this->urlModel = $urlModel;
    }

    public function handle(Request $request, Response $response, array $args): Response
    {
        $shortCode = $args['shortCode'] ?? '';

        try {
            // Valida o código
            if (!$this->isValidShortCode($shortCode)) {
                return JsonResponse::error($response, 'Código inválido', 400);
            }

            // Busca a URL
            $url = $this->urlModel->findByShortCode($shortCode);
            if (!$url) {
                return JsonResponse::notFound($response, 'URL não encontrada');
            }

            // Verifica se a URL expirou
            if (isset($url['expires_at']) && $url['expires_at'] < time()) {
                return JsonResponse::error($response, 'URL expirada', 410);
            }

            // Incrementa o contador de visitas
            $this->urlModel->incrementVisits($shortCode);

            // Retorna redirecionamento
            return $response
                ->withStatus(302)
                ->withHeader('Location', $url['original_url']);
        } catch (\Exception $e) {
            return JsonResponse::serverError($response, 'Erro ao processar a requisição');
        }
    }

    private function isValidShortCode(string $code): bool
    {
        return preg_match('/^[a-zA-Z0-9]{8}$/', $code) === 1;
    }
}