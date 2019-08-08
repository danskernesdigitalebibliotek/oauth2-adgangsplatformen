<?php

namespace Adgangsplatformen\Support\Illuminate;

use Adgangsplatformen\Middleware\TokenResourceOwnerMapper;
use Adgangsplatformen\Provider\Adgangsplatformen;
use Illuminate\Contracts\Foundation\Application;
use League\OAuth2\Client\Provider\AbstractProvider;
use PHPUnit\Framework\TestCase;
use Softonic\Laravel\Middleware\Psr15Bridge\Psr15MiddlewareAdapter;

class AdgangsplatformenServiceProviderTest extends TestCase
{

    public function testRegister()
    {
        $app = $this->createMock(Application::class);
        $app->expects($this->at(0))
            ->method('singleton')
            ->with(
                $this->equalTo(AbstractProvider::class),
                $this->callback(function ($concrete) {
                    return is_callable($concrete);
                })
            );

        $provider = new AdgangsplatformenServiceProvider($app);
        $provider->register();
    }
}
