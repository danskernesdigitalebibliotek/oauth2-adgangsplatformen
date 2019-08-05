<?php

declare(strict_types=1);

namespace Adgangsplatformen\Middleware;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenResourceOwnerMapper implements MiddlewareInterface
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
        $accessTokens = array_filter(array_map(function (string $header) {
            $matches = [];
            preg_match('/Bearer (.*)/', $header, $matches);
            return $matches[1];
        }, $headers));

        $resourceOwners = array_filter(array_map(function (string $token) {
            try {
                $accessToken = new AccessToken(['access_token' => $token]);
                return $this->client->getResourceOwner($accessToken);
            } catch (\Exception $e) {
                return false;
            }
        }, $accessTokens));

        $request = array_reduce(
            $resourceOwners,
            function (ServerRequestInterface $request, $resourceOwner) {
                return $request->withAttribute($this->attributeName, $resourceOwner);
            },
            $request
        );

        return $handler->handle($request);
    }
}
