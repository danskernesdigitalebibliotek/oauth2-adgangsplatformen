<?php

declare(strict_types=1);

namespace Adgangsplatformen\Middleware;

use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenResourceOwnerValidator implements MiddlewareInterface
{

    /* @var \Adgangsplatformen\Provider\Adgangsplatformen */
    private $client;

    /* @var string */
    private $attributeName;

    public function __construct(AbstractProvider $client, string $resourceOwnerRequestAttributeName = 'resource_owner')
    {
        $this->client = $client;
        $this->attributeName = $resourceOwnerRequestAttributeName;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $headers = $request->getHeader('Authorization');
        if (empty($headers)) {
            return $this->errorResponse(401, 'Missing "Authorization" header');
        }

        $accessTokens = array_filter(array_map(function (string $header) {
            $matches = [];
            preg_match('/^(?:\s+)?Bearer (.+)$/', $header, $matches);
            return $matches[1] ?? false;
        }, $headers));
        if (empty($accessTokens)) {
            return $this->errorResponse(401, 'Invalid "Authorization" header');
        }

        $resourceOwners = array_filter(array_map(function (string $token) {
            try {
                $accessToken = new AccessToken(['access_token' => $token]);
                return $this->client->getResourceOwner($accessToken);
            } catch (\Exception $e) {
                return false;
            }
        }, $accessTokens));
        if (empty($resourceOwners)) {
            return $this->errorResponse(401, 'No resource owner(s) for access token(s)');
        }

        $request = array_reduce(
            $resourceOwners,
            function (ServerRequestInterface $request, $resourceOwner) {
                return $request->withAttribute($this->attributeName, $resourceOwner);
            },
            $request
        );

        return $handler->handle($request);
    }

    protected function errorResponse(int $status, string $body): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'text/plain'], $body);
    }
}
