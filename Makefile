.PHONY: *

OPTS=

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

check-cs: ## check coding standards
	vendor/bin/phpcs -n

fix-cs: ## auto-fix coding standards
	vendor/bin/phpcbf -n

static-analysis: ## runs static analysis
	 vendor/bin/phpstan analyse -c phpstan.neon

lint-php: ## linting php files
	 if find src -name "*.php" -exec php -l {} \; | grep -v "No syntax errors detected"; then exit 1; fi
	 if find tests -name "*.php" -exec php -l {} \; | grep -v "No syntax errors detected"; then exit 1; fi

phpunit: ## executing all test files
	vendor/bin/phpunit --bootstrap=vendor/autoload.php --colors tests

build: lint-php check-cs static-analysis phpunit
