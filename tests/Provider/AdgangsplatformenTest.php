<?php

namespace Adgangsplatformen\Provider;

use Adgangsplatformen\ResponseFactoryTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use PHPUnit\Framework\TestCase;

class AdgangsplatformenTest extends TestCase
{
    use ResponseFactoryTrait;

    private function getMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $stack
        ]);
        return $client;
    }

    public function testAccessToken()
    {
        $accessToken = 'access-token';
        $client = $this->getMockClient([
            $this->buildAccessTokenResponse($accessToken)
        ]);

        $adgangsplatformen = new Adgangsplatformen([
            'clientId' => 'a-client-id' ,
            'clientSecret' => 'a-client-secret',
        ], [
            'httpClient' => $client
        ]);
        $token = $adgangsplatformen->getAccessToken('password', [
            'username' => 'username',
            'password' => 'password'
        ]);

        $this->assertEquals($accessToken, $token->getToken());
    }

    public function testErrorResponse()
    {
        $errorCode = 401;
        $errorMessage = 'Invalid token: client id is invalid';
        $errorResponse = [
            'error' => 'invalid_client',
            'error_description' => $errorMessage
        ];
        $client = $this->getMockClient([
            $this->buildJsonResponse($errorCode, $errorResponse),
        ]);

        $adgangsplatformen = new Adgangsplatformen([
            'clientId' => 'invalid-client-id' ,
            'clientSecret' => 'invalid-client-secret',
        ], [
            'httpClient' => $client
        ]);

        $this->expectExceptionObject(new IdentityProviderException($errorMessage, $errorCode, $errorResponse));

        $adgangsplatformen->getAccessToken('password', [
            'username' => 'username',
            'password' => 'password'
        ]);
    }
}
