<?php

declare(strict_types=1);

namespace Adgangsplatformen\Support\Illuminate;

use Illuminate\Contracts\Foundation\Application;

/**
 * Mock application interface.
 *
 * Our service provider relies on the existence of some methods that's not
 * part of the Application interface in order to determine if it's running in
 * a web app.
 *
 * In order to properly mock the $app, we extend the interface as Prophecy
 * won't allow stubbing unknown methods.
 */
interface TestApplication extends Application
{
    public function routeMiddleware(array $middleware);
    public function rebinding($abstract, \Closure $callback);
}
