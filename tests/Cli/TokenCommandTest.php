<?php

namespace Adgangsplatformen\Cli;

use Adgangsplatformen\MockClientFactoryTrait;
use Adgangsplatformen\ResponseFactoryTrait;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class TokenCommandTest extends TestCase
{
    use ResponseFactoryTrait, MockClientFactoryTrait;

    public function testInvoke()
    {
        $logger = $this->createMock(Logger::class);

        $client = $this->buildMockClient([
            $this->buildAccessTokenResponse('access-token'),
            new Response(200)
        ]);

        $output = $this->createMock(OutputInterface::class);
        $output->expects($this->atLeastOnce())->method('write');

        call_user_func(
            new TokenCommand(),
            [
                'CLIENT_ID' => 'client-id',
                'CLIENT_SECRET' => 'client-secret',
                'AGENCY' => 'agency',
                'USERNAME' => 'username',
                'PASSWORD' => 'password',
            ],
            $output,
            $logger,
            $client
        );
    }
}
