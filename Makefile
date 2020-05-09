# Help

TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Docker

docker-start: ## Start dev env with Docker Compose
	docker-compose up

docker-pull-rebuild: ## Rebuild docker images
	docker-compose build --pull

docker-php: ## Open bash in the php image
	docker-compose exec php bash

# Gilded Rose

run: ## Run Gilded Rose example app
	docker-compose exec php php bin/app.php run

test: cs phpstan test-unit test-mutation ## Run all tests

test-unit: ## Run unit tests with coverage
	docker-compose exec php php vendor/bin/phpunit --testdox --coverage-text

test-mutation: ## Run mutation tests
	docker-compose exec php php vendor/bin/infection --threads=8

# Coding Style

.PHONY: cs cs-fix
cs: vendor ## Check code style
	docker-compose exec php php vendor/bin/phpcs --ignore=tests/GildedRoseGoldenMaster.php,src/Item.php,bin/app.php

cs-fix: vendor ## Fix code style
	docker-compose exec php php vendor/bin/phpcbf

# Static Analysis

.PHONY: phpstan
phpstan: vendor ## Check static analysis
	docker-compose exec php php vendor/bin/phpstan analyse src tests --level=max
