.PHONY: help up down restart logs shell test lint analyze fresh migrate seed setup

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

setup: ## Initial project setup
	docker compose build
	docker compose up -d
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	@echo "✅ Setup complete! API at http://localhost:8000"

up: ## Start containers
	docker compose up -d

down: ## Stop containers
	docker compose down

restart: ## Restart containers
	docker compose restart

logs: ## Tail container logs
	docker compose logs -f

shell: ## Open shell in app container
	docker compose exec app sh

test: ## Run Pest tests
	docker compose exec app php artisan test

lint: ## Run Laravel Pint
	docker compose exec app ./vendor/bin/pint

analyze: ## Run PHPStan
	docker compose exec app ./vendor/bin/phpstan analyse

fresh: ## Fresh migrate with seeders
	docker compose exec app php artisan migrate:fresh --seed

migrate: ## Run migrations
	docker compose exec app php artisan migrate

seed: ## Run seeders
	docker compose exec app php artisan db:seed