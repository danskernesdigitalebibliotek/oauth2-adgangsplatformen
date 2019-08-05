workflow "Run tests" {
  on = "push"
  resolves = ["Unit tests", "Check codestyle", "Static code analysis", "Send coverage to Codecov"]
}

action "Composer install" {
  uses = "MilesChou/composer-action@master"
  args = "install"
}

action "Unit tests" {
  needs = ["Composer install"]
  uses = "docker://php:7.2-alpine"
  runs = "phpdbg -qrr vendor/bin/phpunit"
}

action "Check codestyle" {
  needs = ["Composer install"]
  uses = "docker://php:7.2-alpine"
  runs = "vendor/bin/phpcs"
}

action "Static code analysis" {
  needs = ["Composer install"]
  uses = "docker://php:7.2-alpine"
  runs = "vendor/bin/phpstan analyse"
}

action "Send coverage to Codecov" {
  needs = ["Unit tests"]
  uses = "Atrox/codecov-action@v0.1.3"
  secrets = ["CODECOV_TOKEN"]
}
