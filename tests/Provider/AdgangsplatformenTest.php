<?php

namespace Adgangsplatformen\Provider;

use Adgangsplatformen\MockClientFactoryTrait;
use Adgangsplatformen\ResponseFactoryTrait;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
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

    public function testUrlMapping(): void
    {
        $token = $this->createStub(AccessToken::class);

        $adgangsplatformen = new Adgangsplatformen([
            'clientId' => 'a-client-id' ,
            'clientSecret' => 'a-client-secret',
        ], [
            'httpClient' => $this->buildMockClient()
        ]);

        $this->assertEquals(
            'https://login.bib.dk/',
            $adgangsplatformen->getBaseUrl(),
        );
        $this->assertEquals(
            'https://login.bib.dk/oauth/authorize',
            $adgangsplatformen->getBaseAuthorizationUrl(),
        );
        $this->assertEquals(
            'https://login.bib.dk/oauth/token',
            $adgangsplatformen->getBaseAccessTokenUrl([]),
        );
        $this->assertEquals(
            'https://login.bib.dk/oauth/authorize',
            $adgangsplatformen->getBaseAuthorizationUrl(),
        );
        $this->assertEquals(
            'https://login.bib.dk/userinfo',
            $adgangsplatformen->getResourceOwnerDetailsUrl($token),
        );

        $adgangsplatformen = new Adgangsplatformen([
            'clientId' => 'a-client-id' ,
            'clientSecret' => 'a-client-secret',
        ], [
            'httpClient' => $this->buildMockClient()
        ], true);

        $this->assertEquals(
            'https://stg.login.bib.dk/',
            $adgangsplatformen->getBaseUrl(),
        );
        $this->assertEquals(
            'https://stg.login.bib.dk/oauth/authorize',
            $adgangsplatformen->getBaseAuthorizationUrl(),
        );
        $this->assertEquals(
            'https://stg.login.bib.dk/oauth/token',
            $adgangsplatformen->getBaseAccessTokenUrl([]),
        );
        $this->assertEquals(
            'https://stg.login.bib.dk/oauth/authorize',
            $adgangsplatformen->getBaseAuthorizationUrl(),
        );
        $this->assertEquals(
            'https://stg.login.bib.dk/userinfo',
            $adgangsplatformen->getResourceOwnerDetailsUrl($token),
        )
            ;
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

    public function testErrorResponse(): void
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
    public function testRevokeAccessToken(AccessTokenInterface $accessToken): void
    {
        $this->mockHandler->append(new Response());

        $this->adgangsplatformen->revokeAccessToken($accessToken);
    }

    /**
     * @depends testAccessToken
     */
    public function testResourceOwner(AccessTokenInterface $accessToken): void
    {
        $id = 'abcd1234';
        $municipalityNumber = 123;
        $this->mockHandler->append(
            $this->buildJsonResponse(
                200,
                [ 'attributes' => [ 'uniqueId' => $id, 'municipality' => $municipalityNumber]]
            )
        );

        /* @var \Adgangsplatformen\Provider\AdgangsplatformenUser $resourceOwner */
        $resourceOwner = $this->adgangsplatformen->getResourceOwner($accessToken);
        $this->assertInstanceOf(AdgangsplatformenUser::class, $resourceOwner);
        $this->assertEquals($id, $resourceOwner->getId());
        $this->assertEquals($municipalityNumber, $resourceOwner->getMunicipalityNumber());
    }
}
