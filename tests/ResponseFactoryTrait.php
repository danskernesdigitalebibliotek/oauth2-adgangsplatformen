<?php

namespace Adgangsplatformen;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait ResponseFactoryTrait
{

    protected function buildJsonResponse(int $statusCode, array $data): ResponseInterface
    {
        $json = json_encode($data) ?: null;
        return new Response($statusCode, ['Content-Type' => 'application/json'], $json);
    }

    protected function buildAccessTokenResponse($accessToken): ResponseInterface
    {
        return $this->buildJsonResponse(200, [
            'token_type' => 'bearer',
            'access_token' => $accessToken,
            'expires_in' => 2592000
        ]);
    }
}
