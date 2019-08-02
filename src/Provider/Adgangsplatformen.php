<?php

declare(strict_types=1);

namespace Adgangsplatformen\Provider;

use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Adgangsplatformen extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public function __construct(array $options = [], array $collaborators = [])
    {
        if (empty($collaborators['optionProvider'])) {
            $collaborators['optionProvider'] = new HttpBasicAuthOptionProvider();
        }
        parent::__construct($options, $collaborators);
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return 'https://login.bib.dk/oauth/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://login.bib.dk/oauth/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://login.bib.dk/userinfo';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string $data Parsed response data
     *
     * @return void
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() == 200) {
            return;
        }

        $message = $response->getReasonPhrase();
        if (is_array($data) && isset($data['error_description'])) {
            $message = $data['error_description'];
        }
        throw new IdentityProviderException(
            $message,
            $response->getStatusCode(),
            $response->getBody()->getContents()
        );
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        // TODO: Implement this.
        $resourceOwnerId = $token->getResourceOwnerId() ?: '';
        return new GenericResourceOwner($response, $resourceOwnerId);
    }

    /**
     * Revoke an access token created with a password grant.
     *
     * @param \League\OAuth2\Client\Token\AccessTokenInterface $token
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function revokeAccessToken(AccessTokenInterface $token): void
    {
        $url = $this->appendQuery(
            'https://login.bib.dk/revoke/',
            $this->buildQueryString(['access_token' => $token->getToken()])
        );
        $request = $this->createRequest('DELETE', $url, $token, []);
        $this->getParsedResponse($request);
    }
}
