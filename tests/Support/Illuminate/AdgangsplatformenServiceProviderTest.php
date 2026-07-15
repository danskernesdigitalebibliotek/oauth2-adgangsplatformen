<?php

namespace Adgangsplatformen\Support\Illuminate;

use Adgangsplatformen\Provider\Adgangsplatformen;
use Adgangsplatformen\Support\PSR15\TokenResourceOwnerValidator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AdgangsplatformenServiceProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testRegister(): void
    {
        // See TestApplication class comment.
        $app = $this->prophesize(TestApplication::class);

        $app->singleton(
            Adgangsplatformen::class,
            Argument::type('callable')
        )->shouldBeCalled();

        $app->singleton(
            TokenResourceOwnerValidator::class,
            Argument::type('callable')
        )->shouldBeCalled();

        $app->routeMiddleware([
            'auth' => TokenResourceOwnerValidator::class
        ])->shouldBeCalled();

        $app->resolving(
            'request',
            Argument::type('callable')
        )->shouldBeCalled();

        $app->rebinding(
            'request',
            Argument::type('callable')
        )->shouldBeCalled();

        $provider = new AdgangsplatformenServiceProvider($app->reveal());
        $provider->register();
    }
}
