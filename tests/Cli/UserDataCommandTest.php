<?php

namespace Adgangsplatformen\Cli;

use Adgangsplatformen\ResponseFactoryTrait;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class UserDataCommandTest extends TestCase
{
    use ResponseFactoryTrait;

    public function testInvoke()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->atLeastOnce())->method('send')->willReturn(
            $this->buildAccessTokenResponse('access-token')
        );

        call_user_func(
            new UserDataCommand(),
            [
                'CLIENT_ID' => 'client-id',
                'CLIENT_SECRET' => 'client-secret',
                'AGENCY' => 'agency',
                'USERNAME' => 'username',
                'PASSWORD' => 'password',
            ],
            $this->createMock(OutputInterface::class),
            $this->createMock(Logger::class),
            $client
        );
    }
}
