<?php

namespace Adgangsplatformen\Middleware;

use Adgangsplatformen\Provider\Adgangsplatformen;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenResourceOwnerMapperTest extends TestCase
{

    public function testProcess()
    {
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);

        $client = $this->createMock(Adgangsplatformen::class);
        $client->method('getResourceOwner')
            ->willReturn($resourceOwner);

        $attributeName = 'attribute-name';
        $middleware = new TokenResourceOwnerMapper($client, $attributeName);

        $request = (new ServerRequest('GET', 'https://host/path'))
            ->withHeader('Authorization', 'Bearer access-token');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request
            ) use ($resourceOwner, $attributeName) {
                $this->assertEquals($resourceOwner, $request->getAttribute($attributeName));
                return true;
            }));

        $middleware->process($request, $handler);
    }
}
