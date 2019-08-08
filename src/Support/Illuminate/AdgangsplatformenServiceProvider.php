<?php

declare(strict_types=1);

namespace Adgangsplatformen\Support\Illuminate;

use Adgangsplatformen\Middleware\TokenResourceOwnerMapper;
use Adgangsplatformen\Provider\Adgangsplatformen;
use Adgangsplatformen\Support\PSR15\TokenResourceOwnerValidator;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use Softonic\Laravel\Middleware\Psr15Bridge\Psr15MiddlewareAdapter;

class AdgangsplatformenServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(
            AbstractProvider::class,
            function () {
                return new Adgangsplatformen([
                    'clientId' => env('APP_ADGANGSPLATFORMEN_CLIENT_ID'),
                    'clientSecret' => env('APP_ADGANGSPLATFORMEN_CLIENT_SECRET')
                ]);
            }
        );
    }
}
