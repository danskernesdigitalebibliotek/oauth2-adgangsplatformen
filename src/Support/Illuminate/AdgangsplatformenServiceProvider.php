<?php

declare(strict_types=1);

namespace Adgangsplatformen\Support\Illuminate;

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
            function ($app) {
                $manager = new AdgangsplatformenManager($app);
                return $manager->driver();
            }
        );

        // Only register middleware if we are working with a web-like
        // application. There is no interface we can test against so instead we
        // check for methods.
        if (method_exists($this->app, 'routeMiddleware')) {
            $this->app->singleton(
                TokenResourceOwnerValidator::class,
                function () {
                    return Psr15MiddlewareAdapter::adapt(new TokenResourceOwnerValidator(
                        $this->app->get(AbstractProvider::class),
                        'resourceOwner'
                    ));
                }
            );

            $this->app->routeMiddleware([
                'auth' => TokenResourceOwnerValidator::class
            ]);

            // Console handlers and tests making HTTP requests mock around with the
            // request service left and right. This seems to be the only way to
            // ensure that the request has the required user resolver at the right
            // time.
            $this->app->resolving('request', function (Request $request) {
                $request->setUserResolver(function () use ($request) {
                    return $request->attributes->get('resourceOwner');
                });
            });
        };
    }
}
