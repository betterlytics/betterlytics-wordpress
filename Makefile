.PHONY: start stop restart logs shell test lint lint-fix build clean help

# Development
start: ## Start the development environment
	docker compose up -d

stop: ## Stop the development environment
	docker compose down

restart: ## Restart the development environment
	docker compose restart

logs: ## View logs from all containers
	docker compose logs -f

shell: ## Open a shell in the WordPress container
	docker compose exec wordpress bash

# Testing
test: ## Run PHPUnit tests
	docker compose --profile test run --rm phpunit

test-verbose: ## Run PHPUnit tests with verbose output
	docker compose --profile test run --rm phpunit vendor/bin/phpunit --verbose

test-filter: ## Run specific test (usage: make test-filter FILTER=test_name)
	docker compose --profile test run --rm phpunit vendor/bin/phpunit --filter $(FILTER)

# Linting
lint: ## Run PHP CodeSniffer
	docker compose exec wordpress bash -c "cd /var/www/html/wp-content/plugins/betterlytics && vendor/bin/phpcs"

lint-fix: ## Auto-fix PHP CodeSniffer issues
	docker compose exec wordpress bash -c "cd /var/www/html/wp-content/plugins/betterlytics && vendor/bin/phpcbf"

# Alternative: Run lint in a standalone container if WordPress isn't running
lint-standalone: ## Run PHP CodeSniffer (standalone container)
	docker run --rm -v "$(PWD):/app" -w /app composer:latest bash -c "composer install --quiet && vendor/bin/phpcs"

# Build
build: ## Build the plugin zip file
	mkdir -p build
	zip -r build/betterlytics.zip . \
		-x "*.git*" \
		-x "node_modules/*" \
		-x "vendor/*" \
		-x "tests/*" \
		-x "bin/*" \
		-x "build/*" \
		-x "docker-compose.yml" \
		-x "Dockerfile*" \
		-x "Makefile" \
		-x "phpunit.xml*" \
		-x "phpcs.xml*" \
		-x "composer.*" \
		-x ".editorconfig" \
		-x ".github/*"

# Cleanup
clean: ## Remove all containers, volumes, and build artifacts
	docker compose --profile test down -v
	rm -rf build/
	rm -rf vendor/

clean-all: clean ## Remove everything including WordPress data
	docker volume rm betterlytics-wordpress_wordpress_data betterlytics-wordpress_db_data 2>/dev/null || true

# Composer (run in container)
composer-install: ## Install Composer dependencies
	docker compose exec wordpress bash -c "cd /var/www/html/wp-content/plugins/betterlytics && composer install"

composer-update: ## Update Composer dependencies
	docker compose exec wordpress bash -c "cd /var/www/html/wp-content/plugins/betterlytics && composer update"

# Help
help: ## Show this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Default target
.DEFAULT_GOAL := help
