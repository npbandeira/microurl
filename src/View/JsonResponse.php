<?php

namespace MicroUrl\View;

class JsonResponse
{
    public static function send(array $data, int $statusCode = 200, array $headers = []): void
    {
        self::setHeaders($headers);
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(string $message, array $data = [], int $statusCode = 200): void
    {
        $response = [
            'success' => true,
            'message' => $message,

        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        self::send($response, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message,

        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        self::send($response, $statusCode);
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

    public static function notFound(string $message = 'Recurso não encontrado'): void
    {
        self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Não autorizado'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Acesso negado'): void
    {
        self::error($message, 403);
    }

    public static function methodNotAllowed(string $message = 'Método não permitido'): void
    {
        self::error($message, 405);
    }

    public static function tooManyRequests(string $message = 'Muitas requisições', int $retryAfter = 60): void
    {
        self::setHeaders(['Retry-After' => $retryAfter]);
        self::error($message, 429);
    }

    public static function serverError(string $message = 'Erro interno do servidor'): void
    {
        self::error($message, 500);
    }

    public static function validationError(array $errors): void
    {
        self::error('Erro de validação', 422, $errors);
    }

    public static function created(string $message = 'Recurso criado com sucesso', array $data = []): void
    {
        self::success($message, $data, 201);
    }

    public static function noContent(): void
    {
        self::send([], 204);
    }

    public static function accepted(string $message = 'Requisição aceita para processamento'): void
    {
        self::success($message, [], 202);
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