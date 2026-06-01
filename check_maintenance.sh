#!/bin/bash
cd "$(dirname "$0")"
docker compose exec -T laravel.test php artisan down --message="檢查維護模式狀態"
docker compose exec -T laravel.test php artisan up
