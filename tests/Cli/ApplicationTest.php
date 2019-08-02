<?php

namespace Adgangsplatformen\Cli;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ApplicationTest extends TestCase
{

    public function testBootstrap()
    {
        $app = new Application();
        // We basicly just test that the Application can build a proper
        // container. Asserting the contents does not make sense.
        $this->assertInstanceOf(ContainerInterface::class, $app->getContainer());
    }
}
