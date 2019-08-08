<?php

declare(strict_types=1);

namespace Adgangsplatformen\Support\PSR15;

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
        // Even though a header can contain multiple values we only want to deal
        // with the first one. Supporting multiple values can result in weird
        // situations like multiple resource owners attached to the same
        // request.
        $header = array_shift($headers) ?? '';

        $matches = [];
        preg_match('/^(?:\s+)?Bearer (.+)$/', $header, $matches);
        $token = $matches[1] ?? false;

        if (empty($token)) {
            return $this->errorResponse(401, 'Invalid "Authorization" header');
        }

        try {
            $accessToken = new AccessToken(['access_token' => $token]);
            $resourceOwner = $this->client->getResourceOwner($accessToken);
            $request = $request->withAttribute($this->attributeName, $resourceOwner);
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'No resource owner for access token');
        }

        return $handler->handle($request);
    }

    protected function errorResponse(int $status, string $body): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'text/plain'], $body);
    }
}
