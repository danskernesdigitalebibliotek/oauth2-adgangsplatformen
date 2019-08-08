<?php

namespace Adgangsplatformen\Support\Illuminate;

use Illuminate\Contracts\Foundation\Application;
use League\OAuth2\Client\Provider\AbstractProvider;
use PHPUnit\Framework\TestCase;

class AdgangsplatformenServiceProviderTest extends TestCase
{

    public function testRegister()
    {
        $methods = get_class_methods(Application::class);
        $app = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            // The service provider determines whether to add middleware based
            // on the availability of a method. This method is not defined by
            // any interface so we have to add it to our mock manually along
            // with the rest of the methods. This uses a deprecated method
            // but there seems to be no real substitute so we have to go with
            // this approach for now.
            ->setMethods(array_merge($methods, ['routeMiddleware']))
            ->getMock();

        $app->expects($this->at(0))
            ->method('singleton')
            ->with(
                $this->equalTo(AbstractProvider::class),
                $this->callback(function ($concrete) {
                    return is_callable($concrete);
                })
            );

        $app->expects($this->once())
            ->method('resolving')
            ->with(
                $this->equalTo('request'),
                $this->callback(function ($concrete) {
                    return is_callable($concrete);
                })
            );

        $provider = new AdgangsplatformenServiceProvider($app);
        $provider->register();
    }
}
