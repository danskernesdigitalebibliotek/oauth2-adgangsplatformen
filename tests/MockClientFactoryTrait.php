<?php

namespace Adgangsplatformen;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

trait MockClientFactoryTrait
{

    /* @var \GuzzleHttp\Handler\MockHandler */
    private $mockHandler;

    protected function buildMockClient(array $responses = []): Client
    {
        $this->mockHandler = new MockHandler($responses);
        $stack = HandlerStack::create($this->mockHandler);
        $client = new Client([
            'handler' => $stack
        ]);
        return $client;
    }
}
