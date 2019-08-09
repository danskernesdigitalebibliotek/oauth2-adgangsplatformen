<?php

declare(strict_types=1);

namespace Adgangsplatformen\Support\Illuminate;

use Adgangsplatformen\Provider\Adgangsplatformen;
use Adgangsplatformen\Provider\AdgangsplatformenUser;
use Illuminate\Support\Manager;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

class AdgangsplatformenManager extends Manager
{

    public function createTestingDriver() : AbstractProvider
    {
        // Use an instance of an anonymous class which will return a
        // resource owner with the same id as the provided access token.
        return new class extends Adgangsplatformen {

            public function getResourceOwner(AccessToken $token)
            {
                if (empty($token->getToken())) {
                    throw new IdentityProviderException('Unknown user', 404, []);
                }
                return new AdgangsplatformenUser([
                    'attributes' => [
                        'uniqueId' => $token->getToken(),
                        'municipality' => 101
                    ]
                ]);
            }
        };
    }

    public function createProductionDriver() : AbstractProvider
    {
        return new Adgangsplatformen([
            'clientId' => env('ADGANGSPLATFORMEN_CLIENT_ID'),
            'clientSecret' => env('ADGANGSPLATFORMEN_CLIENT_SECRET')
        ]);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() : string
    {
        return env('ADGANGSPLATFORMEN_DRIVER', env('APP_ENV', 'production'));
    }
}
