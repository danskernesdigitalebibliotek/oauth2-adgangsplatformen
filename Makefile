.PHONEY: lint test

default: lint test

help: ## List available targets
	@grep -E -h "^\w.*:.*##" $(MAKEFILE_LIST) | sed -e 's/\(.*\):.*## *\(.*\)/\1|\2/' | column -s '|' -t | sort

lint: ## Run linting
	./vendor/bin/phpcs
	./vendor/bin/phpstan

test: ## Run tests
	./vendor/bin/phpunit
