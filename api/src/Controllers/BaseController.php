<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BaseController
{
    /**
     * Retorna resposta JSON
     */
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    /**
     * Retorna resposta de sucesso
     */
    protected function success(Response $response, $data = null, string $message = 'Success'): Response
    {
        return $this->jsonResponse($response, [
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Retorna resposta de erro
     */
    protected function error(Response $response, string $message = 'Error', int $status = 400): Response
    {
        return $this->jsonResponse($response, [
            'success' => false,
            'message' => $message
        ], $status);
    }

    /**
     * Retorna resposta de validação
     */
    protected function validationError(Response $response, array $errors): Response
    {
        return $this->jsonResponse($response, [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }

    /**
     * Obtém dados do request
     */
    protected function getRequestData(Request $request): array
    {
        $parsedBody = $request->getParsedBody();
        return is_array($parsedBody) ? $parsedBody : [];
    }

    /**
     * Obtém parâmetros da query string
     */
    protected function getQueryParams(Request $request): array
    {
        return $request->getQueryParams();
    }
}
