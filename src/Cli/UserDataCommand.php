<?php

namespace Adgangsplatformen\Cli;

use Adgangsplatformen\Provider\Adgangsplatformen;
use Concat\Http\Middleware\Logger;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class UserDataCommand
{

    public function __invoke(array $params, OutputInterface $output, Logger $logger, ClientInterface $client)
    {
        $logger->setLogger(new ConsoleLogger($output));

        $provider = new Adgangsplatformen([
            'clientId' => $params['CLIENT_ID'] ,
            'clientSecret' => $params['CLIENT_SECRET']
        ], [
            'httpClient' => $client,
        ]);

        $token = $provider->getAccessToken('password', [
            'username' => sprintf('%s@%s', $params['USERNAME'], $params['AGENCY']),
            'password' => $params['PASSWORD']
        ]);

        try {
            $user = $provider->getResourceOwner($token);
            $output->write(var_export($user, true));
        } finally {
            $provider->revokeAccessToken($token);
        }
    }
}
