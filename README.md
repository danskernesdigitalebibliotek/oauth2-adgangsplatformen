# Adgangsplatformen Provider for OAuth 2.0 Client

[![codecov](https://codecov.io/gh/reload/oauth2-adgangsplatformen/branch/master/graph/badge.svg)](https://codecov.io/gh/reload/oauth2-adgangsplatformen)

This package provides OAuth 2.0 for accessing [Adgangsplatformen](https://github.com/DBCDK/hejmdal) currently running at https://login.bib.dk/ - a single sign-on solution for public libraries in Denmark. It is based on the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Usage

To use the provider first of all you need to obtain a client id and secret from your library partner or directly from DBC, the company responsible for running Adgangsplatfomen.

To install this package use Composer:

```shell
composer require danskernesdigitalebibliotek/oauth2-adgangsplatformen
```

The package contains several elements:

1. [OAuth 2.0 provider](#oauth-20-provider)
2. [Middleware for mapping server requests to users](#psr-15-middleware)
3. [CLI application for demonstration and debugging](#cli-application)

### OAuth 2.0 provider

You can use the provider to obtain an access token and information about a library patron:

```php
$provider = new Adgangsplatformen\Provider\Adgangsplatformen([
    'clientId'     => '{client-id}',
    'clientSecret' => '{client-secret}',
]);
$accessToken = $provider->getAccessToken('password', [
    'username' => '{patron-id}@{library-id}'
    'password' => '{patron-password}'
]);
$patron = $provider->getResourceOwner($accessToken);
```

**Note: The provider has currently only been tested with [password grants](https://oauth2.thephpleague.com/authorization-server/resource-owner-password-credentials-grant/). Other types of grant may or may not work.**

### PSR-15 middleware

The package also includes [PSR-15 compliant]((https://www.php-fig.org/psr/psr-15/)) middleware which can be used to map requests containing an OAuth 2.0 compliant `Authentication` header containing the value "Bearer {access-token}" to the corresponding patron. Otherwise an error response is returned.

Consuming the middleware depends on the framework used.

In [Zend Expressive](https://docs.zendframework.com/zend-expressive/) the following would work:

```php
$app->post('/api/foo', [
    new TokenResourceOwnerMapper($provider)
]);
```

### CLI application

The package includes a CLI application which can be used to demo the provider and inspect client-server communication.

To use the application you have to have the suggested dependencies installed as well:

```shell
composer require mnapoli/silly-php-di rtheunissen/guzzle-log-middleware vlucas/phpdotenv
``` 

Run the application to show the list of available commands:

```shell
vendor/bin/adgangsplatformen list
```

As an example show an access token for a user:

```shell
CLIENT_ID={client-id} CLIENT_SECRET={client-secret} AGENCY={library-agency} USERNAME={patron-username} PASSWORD={patron-password} vendor/bin/adgangsplatformen token
```

Instead of including them in the command environment variables can also be set in the environment or defined in a `.env` file containing one or more of the values:

```ini
CLIENT_ID={client-id}
CLIENT_SECRET={client-secret}
AGENCY={library-agency}
USERNAME={patron-username}
PASSWORD={patron-password}
```

To see the actual requests and responses sent between the application and Adgangsplatformen add the -v option:

```shell
vendor/bin/adgangsplatformen token -v
````

## Development

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

The library uses the [Composer package manager](https://getcomposer.org/) and all requirements to setup a development environment are defined in the `composer.json` package specification.

Key requirements are:

1. PHP 7.2 or newer compiled with JSON support
2. [Composer](https://getcomposer.org/download/)

### Installing

To get a depeloment environment up and running follow these steps:

1: Clone the repository

```shell
git clone https://github.com/danskernesdigitalebibliotek/oauth2-adgangsplatformen
```

2: Install third-party dependencies including developer dependencies

```shell
composer install
```

If you have [obtained a client id and secret](#usage) and have authentication information for a patron you can test the provider by using the CLI application:

```shell
CLIENT_ID={client-id} CLIENT_SECRET={client-secret} AGENCY={library-agency} USERNAME={patron-username} PASSWORD={patron-password} bin/adgangsplatformen token
```

Note that the binary is located in `bin` and not in `vendor/bin` when you are working directly with the package.

## Code quality

To maintain a consistent functional codebase the project uses unit tests and static code analysis.

The codebase is automatically tested using [GitHub Actions](https://developer.github.com/actions/). Developers can run the same actions locally using Docker and [`act`](https://github.com/nektos/act).

### Unit tests

Unit tests are located under `tests` and developed using [PHPUnit](https://phpunit.de/).

Developers can run the test suite locally:

```shell
vendor/bin/phpunit
```

The project aims to maintain a high level of code coverage.

### Code consistency

The code is analysed using static code analysis tools to maintain a high level of consistency.

The project follows the [PSR-2 code style](https://www.php-fig.org/psr/psr-2/) and the most strict rule level set by [PHPStan](https://github.com/phpstan/phpstan#rule-levels).

Code standard compliance is checked using [PHP_Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer). Developer can run the tool locally:

```shell
vendor/bin/phpcs
```

Developers can run PHPStan locally:

```shell
vendor/bin/phpstan
```

## License

Copyright (C) 2019 Danskernes Digitale Bibliotek (DDB)

This project is licensed under the GNU Affero General Public License - see the [LICENSE.md](LICENSE.md) file for details
