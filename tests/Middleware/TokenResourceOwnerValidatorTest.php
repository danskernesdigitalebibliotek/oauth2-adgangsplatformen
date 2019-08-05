<?php

namespace Adgangsplatformen\Middleware;

use Adgangsplatformen\Provider\Adgangsplatformen;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenResourceOwnerValidatorTest extends TestCase
{

    public function testProcess()
    {
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);

        $client = $this->createMock(Adgangsplatformen::class);
        $client->method('getResourceOwner')
            ->willReturn($resourceOwner);

        $attributeName = 'attribute-name';
        $middleware = new TokenResourceOwnerValidator($client, $attributeName);

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

    /** @dataProvider invalidRequests */
    public function testInvalidRequests(ServerRequestInterface $request, string $expectedText)
    {
        $middleware = new TokenResourceOwnerValidator(
            $client = $this->createMock(AbstractProvider::class),
            'attribute-name'
        );

        $response = $middleware->process(
            $request,
            $this->createMock(RequestHandlerInterface::class)
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($expectedText, $response->getBody()->getContents());
    }

    public function invalidRequests()
    {
        $request = new ServerRequest('GET', 'https://host/path');
        return [
          [
              $request->withAddedHeader('Authorizatio', 'Header missing'),
              'missing'
          ],
          [
              $request->withAddedHeader('Authorization', 'BearerInvalid'),
              'invalid',
          ],
          [
              $request->withAddedHeader('Authorization', ' Bearer DoesNotExist'),
              'no resource owner'
          ],
        ];
    }
}
