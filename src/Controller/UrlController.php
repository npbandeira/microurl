<?php
declare(strict_types=1);

namespace MicroUrl\Controller;

use MicroUrl\Model\Url;
use MicroUrl\View\JsonResponse;

class UrlController
{
    private $urlModel;

    public function __construct()
    {
        $this->urlModel = new Url();
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($path, '/');
        // Remove o prefixo 'api' da URL
        if (strpos($path, 'api/') === 0) {
            $path = substr($path, 4);
        }
        switch ($method) {
            case 'POST':
                $this->createShortUrl();
                break;
            case 'GET':
                if (empty($path)) {
                    JsonResponse::error('Código da URL não fornecido', 400);
                }
                $this->getUrlInfo($path);
                break;
            default:
                JsonResponse::methodNotAllowed();
        }
    }

    private function createShortUrl(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['url'])) {
            JsonResponse::validationError(['url' => 'A URL é obrigatória']);
        }

        $url = $input['url'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            JsonResponse::validationError(['url' => 'URL inválida']);
        }

        // Verifica se a URL já existe
        $existingUrl = $this->urlModel->findByOriginalUrl($url);
        if ($existingUrl) {
            JsonResponse::success('URL já encurtada', [
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
                JsonResponse::validationError(['expires_at' => 'Data de expiração inválida']);
            }
            $data['expires_at'] = $expiresAt;
        }

        $url = $this->urlModel->create($data);

        JsonResponse::created('URL encurtada com sucesso', [
            'original_url' => $url['original_url'],
            'short_code' => $url['short_code'],
            'short_url' => $this->getShortUrl($url['short_code']),
            'visits' => $url['visits'],
            'created_at' => date('Y-m-d H:i:s', $url['created_at']),
            'expires_at' => isset($url['expires_at']) ? date('Y-m-d H:i:s', $url['expires_at']) : null
        ]);
    }

    private function getUrlInfo(string $shortCode): void
    {
        $url = $this->urlModel->findByShortCode($shortCode);

        if (!$url) {
            JsonResponse::notFound('URL não encontrada');
        }

        JsonResponse::success('URL encontrada', [
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