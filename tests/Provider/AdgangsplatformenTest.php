<?php

namespace Adgangsplatformen\Provider;

use Adgangsplatformen\MockClientFactoryTrait;
use Adgangsplatformen\ResponseFactoryTrait;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PHPUnit\Framework\TestCase;

class AdgangsplatformenTest extends TestCase
{
    use ResponseFactoryTrait, MockClientFactoryTrait;

    /* @var \Adgangsplatformen\Provider\Adgangsplatformen */
    private $adgangsplatformen;

    public function setUp(): void
    {
        $this->adgangsplatformen = new Adgangsplatformen([
            'clientId' => 'a-client-id' ,
            'clientSecret' => 'a-client-secret',
        ], [
            'httpClient' => $this->buildMockClient()
        ]);
    }

    public function testAccessToken(): AccessTokenInterface
    {
        $accessToken = 'access-token';
        $this->mockHandler->append(
            $this->buildAccessTokenResponse($accessToken)
        );

        $token = $this->adgangsplatformen->getAccessToken('password', [
            'username' => 'username',
            'password' => 'password'
        ]);

        $this->assertEquals($accessToken, $token->getToken());

        return $token;
    }

    public function testErrorResponse()
    {
        $errorCode = 401;
        $errorMessage = 'Invalid token: client id is invalid';
        $errorResponse = [
            'error' => 'invalid_client',
            'error_description' => $errorMessage
        ];

        $this->mockHandler->append(
            $this->buildJsonResponse($errorCode, $errorResponse)
        );

        $this->expectExceptionObject(new IdentityProviderException($errorMessage, $errorCode, $errorResponse));

        $this->adgangsplatformen->getAccessToken('password', [
            'username' => 'username',
            'password' => 'password'
        ]);
    }

    /**
     * @depends testAccessToken
     * @doesNotPerformAssertions
     */
    public function testRevokeAccessToken(AccessTokenInterface $accessToken)
    {
        $this->mockHandler->append(new Response());

        $this->adgangsplatformen->revokeAccessToken($accessToken);
    }
}
