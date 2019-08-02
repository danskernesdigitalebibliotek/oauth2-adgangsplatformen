<?php

namespace Adgangsplatformen\Cli;

use Concat\Http\Middleware\Logger as LoggerMiddleware;
use Concat\Http\Middleware\Logger;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use Psr\Log\NullLogger;
use Silly\Edition\PhpDi\Application as SillyPhpDi;

class Application extends SillyPhpDi
{

    protected function createContainer()
    {
        $container = ContainerBuilder::buildDevContainer();

        $logger = new LoggerMiddleware(
            new NullLogger(),
            new MessageFormatter(MessageFormatter::DEBUG)
        );
        $logger->setRequestLoggingEnabled();
        $container->set(Logger::class, $logger);

        $stack = HandlerStack::create();
        $stack->push($logger);

        $client = new Client([
            'handler' => $stack
        ]);
        $container->set(ClientInterface::class, $client);

        return $container;
    }
}
