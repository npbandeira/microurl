<?php
declare(strict_types=1);

namespace MicroUrl\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MicroUrl\Model\Url;
use MicroUrl\View\JsonResponse;

class UrlController
{
    private Url $urlModel;

    public function __construct(Url $urlModel)
    {
        $this->urlModel = $urlModel;
    }

    public function createShortUrl(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();

        if (!isset($input['url'])) {
            return JsonResponse::validationError($response, ['url' => 'A URL é obrigatória']);
        }

        $url = $input['url'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return JsonResponse::validationError($response, ['url' => 'URL inválida']);
        }

        // Verifica se a URL já existe
        $existingUrl = $this->urlModel->findByOriginalUrl($url);
        if ($existingUrl) {
            return JsonResponse::success($response, 'URL já encurtada', [
                'original_url' => $existingUrl['original_url'],
                'short_code' => $existingUrl['short_code'],
                'short_url' => $this->getShortUrl($existingUrl['short_code']),
                'visits' => $existingUrl['visits'],
                'created_at' => date('Y-m-d H:i:s', $existingUrl['created_at']),
                'expires_at' => isset($existingUrl['expires_at']) ? date('Y-m-d H:i:s', $existingUrl['expires_at']) : null
            ]);
        }

        // Cria nova URL encurtada
        $data = [
            'original_url' => $url
        ];

        // Adiciona expiração se fornecida
        if (isset($input['expires_at'])) {
            $expiresAt = strtotime($input['expires_at']);
            if ($expiresAt === false || $expiresAt < time()) {
                return JsonResponse::validationError($response, ['expires_at' => 'Data de expiração inválida']);
            }
            $data['expires_at'] = $expiresAt;
        }

        $url = $this->urlModel->create($data);

        return JsonResponse::created($response, 'URL encurtada com sucesso', [
            'original_url' => $url['original_url'],
            'short_code' => $url['short_code'],
            'short_url' => $this->getShortUrl($url['short_code']),
            'visits' => $url['visits'],
            'created_at' => date('Y-m-d H:i:s', $url['created_at']),
            'expires_at' => isset($url['expires_at']) ? date('Y-m-d H:i:s', $url['expires_at']) : null
        ]);
    }

    public function getUrlInfo(Request $request, Response $response, array $args): Response
    {
        $shortCode = $args['shortCode'] ?? '';

        if (empty($shortCode)) {
            return JsonResponse::error($response, 'Código da URL não fornecido', 400);
        }

        $url = $this->urlModel->findByShortCode($shortCode);

        if (!$url) {
            return JsonResponse::notFound($response, 'URL não encontrada');
        }

        return JsonResponse::success($response, 'URL encontrada', [
            'original_url' => $url['original_url'],
            'short_code' => $url['short_code'],
            'short_url' => $this->getShortUrl($url['short_code']),
            'visits' => $url['visits'],
            'created_at' => date('Y-m-d H:i:s', $url['created_at']),
            'expires_at' => isset($url['expires_at']) ? date('Y-m-d H:i:s', $url['expires_at']) : null
        ]);
    }

    private function getShortUrl(string $code): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return sprintf('%s://%s/%s', $protocol, $host, $code);
    }
}