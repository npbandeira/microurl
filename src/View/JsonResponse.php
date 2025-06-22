<?php
declare(strict_types=1);

namespace MicroUrl\View;

use Psr\Http\Message\ResponseInterface as Response;

class JsonResponse
{
    public static function send(Response $response, array $data, int $statusCode = 200, array $headers = []): Response
    {
        $response = $response->withStatus($statusCode);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response = $response->withHeader('Content-Type', 'application/json; charset=UTF-8');

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $response;
    }

    public static function success(Response $response, string $message, array $data = [], int $statusCode = 200): Response
    {
        $responseData = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($data)) {
            $responseData['data'] = $data;
        }

        return self::send($response, $responseData, $statusCode);
    }

    public static function error(Response $response, string $message, int $statusCode = 400, array $errors = []): Response
    {
        $responseData = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $responseData['errors'] = $errors;
        }

        return self::send($response, $responseData, $statusCode);
    }

    public static function redirect(string $url, int $statusCode = 302): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            self::error('URL inválida para redirecionamento', 400);
        }

        $response = [
            'success' => true,
            'message' => 'Redirecionando',
            'redirect_url' => $url,

        ];

        self::setHeaders(['Location' => $url]);
        self::send($response, $statusCode);
    }

    public static function notFound(Response $response, string $message = 'Recurso não encontrado'): Response
    {
        return self::error($response, $message, 404);
    }

    public static function unauthorized(Response $response, string $message = 'Não autorizado'): Response
    {
        return self::error($response, $message, 401);
    }

    public static function forbidden(Response $response, string $message = 'Acesso negado'): Response
    {
        return self::error($response, $message, 403);
    }

    public static function methodNotAllowed(Response $response, string $message = 'Método não permitido'): Response
    {
        return self::error($response, $message, 405);
    }

    public static function tooManyRequests(Response $response, string $message = 'Muitas requisições', int $retryAfter = 60): Response
    {
        return self::error($response, $message, 429, ['retry_after' => $retryAfter]);
    }

    public static function serverError(Response $response, string $message = 'Erro interno do servidor'): Response
    {
        return self::error($response, $message, 500);
    }

    public static function validationError(Response $response, array $errors): Response
    {
        return self::error($response, 'Erro de validação', 422, $errors);
    }

    public static function created(Response $response, string $message = 'Recurso criado com sucesso', array $data = []): Response
    {
        return self::success($response, $message, $data, 201);
    }

    public static function noContent(Response $response): Response
    {
        return $response->withStatus(204);
    }

    public static function accepted(Response $response, string $message = 'Requisição aceita para processamento'): Response
    {
        return self::success($response, $message, [], 202);
    }

    private static function setHeaders(array $headers = []): void
    {
        $headers = array_merge($headers);

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        header('Content-Type: application/json; charset=UTF-8');
    }
}