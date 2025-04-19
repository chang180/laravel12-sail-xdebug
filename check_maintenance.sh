#!/bin/bash
cd /home/chang180/projects/laravel12-docker
./vendor/bin/sail artisan down --message="檢查維護模式狀態"
./vendor/bin/sail artisan up