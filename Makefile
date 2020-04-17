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

test: ## Run tests with coverage
	docker-compose exec php php vendor/bin/phpunit --testdox --coverage-text
