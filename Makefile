.PHONY: up down build restart shell artisan composer npm test pest logs

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

restart:
	docker compose restart

shell:
	docker compose exec laravel.test bash

artisan:
	docker compose exec laravel.test php artisan $(filter-out $@,$(MAKECMDGOALS))

composer:
	docker compose exec laravel.test composer $(filter-out $@,$(MAKECMDGOALS))

npm:
	docker compose exec laravel.test npm $(filter-out $@,$(MAKECMDGOALS))

test:
	docker compose exec laravel.test php artisan test

pest:
	docker compose exec laravel.test ./vendor/bin/pest

logs:
	docker compose logs -f

%:
	@:
