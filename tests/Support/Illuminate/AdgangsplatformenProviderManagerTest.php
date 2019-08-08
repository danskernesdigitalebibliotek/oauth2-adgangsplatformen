<?php

namespace Adgangsplatformen\Support\Illuminate;

use Adgangsplatformen\Provider\Adgangsplatformen;
use Illuminate\Contracts\Foundation\Application;
use League\OAuth2\Client\Provider\AbstractProvider;
use PHPUnit\Framework\TestCase;

class AdgangsplatformenProviderManagerTest extends TestCase
{

    public function testGetDefaultDriver()
    {
        putenv('ADGANGSPLATFORMEN_CLIENT_ID=client-id');
        putenv('ADGANGSPLATFORMEN_CLIENT_SECRET=client-secret');

        $app = $this->createMock(Application::class);
        $manager = new AdgangsplatformenManager($app);
        $provider = $manager->driver();
        $this->assertInstanceOf(Adgangsplatformen::class, $provider);
    }

    public function testTestingDriver()
    {
        $app = $this->createMock(Application::class);
        $manager = new AdgangsplatformenManager($app);
        $provider = $manager->driver('testing');
        $this->assertInstanceOf(Adgangsplatformen::class, $provider);
    }

    public function testProductionDriver()
    {
        putenv('ADGANGSPLATFORMEN_CLIENT_ID=client-id');
        putenv('ADGANGSPLATFORMEN_CLIENT_SECRET=client-secret');

        $app = $this->createMock(Application::class);
        $manager = new AdgangsplatformenManager($app);
        $provider = $manager->driver('production');
        $this->assertInstanceOf(Adgangsplatformen::class, $provider);
    }
}
