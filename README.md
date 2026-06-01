# Laravel 13 + React Starter Kit (Docker)

Laravel 13 React Starter Kit（Inertia + React + TypeScript + Vite 6 + Tailwind 4），以 **Docker Compose** 為主要開發環境，內建 Xdebug 與 Laravel Boost MCP。

## 需求

- Docker Desktop（或 Docker Engine + Compose v2）
- Node.js 22+（僅 host 端建置資產時需要；容器內已含 Node 22）

## 快速開始

```bash
cp .env.example .env
# 編輯 .env，設定 APP_KEY（或啟動後執行 key:generate）

docker compose up -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run dev
```

應用程式：<http://localhost>（`APP_PORT` 可調整）

### 便利指令

| 指令 | 說明 |
|------|------|
| `make up` / `make down` | 啟動 / 停止容器 |
| `make shell` | 進入 app 容器 bash |
| `make artisan migrate` | 執行 artisan |
| `make test` / `make pest` | 執行測試 |
| `bin/dev artisan ...` | Sail 替代腳本 |
| `bin/dev up -d` | 等同 `docker compose up -d` |

## Docker 服務

| 服務 | 說明 | 預設 Port |
|------|------|-----------|
| `laravel.test` | PHP 8.4 + Nginx/Supervisor + Node | 80, 5173 |
| `mysql` | MySQL 8.0（含 `testing` DB init） | 3306 |
| `redis` | Redis Alpine | 6379 |

映像檔由 `./docker` 建置（自 Laravel Sail 8.4 runtime 複製並獨立維護），不再依賴 `vendor/laravel/sail`。

## Xdebug

`.env` 設定：

```env
SAIL_XDEBUG_MODE=develop,debug
SAIL_XDEBUG_CONFIG=client_host=host.docker.internal client_port=9003
```

VS Code / Cursor：使用 `.vscode/launch.json` 的 **Listen for Xdebug (Docker)**，port **9003**。

除錯路由：`GET /debug-test`（可在該路由設斷點驗證）。

## Laravel Boost MCP

已安裝 `laravel/boost` ^2.0。Cursor MCP 設定（`.cursor/mcp.json`）：

```json
{
    "mcpServers": {
        "laravel-boost": {
            "command": "docker",
            "args": ["compose", "exec", "-T", "laravel.test", "php", "artisan", "boost:mcp"]
        }
    }
}
```

**注意：** 需先 `docker compose up -d` 讓 `laravel.test` 容器運行，MCP 才能連線。

更新 Boost 指南：`php artisan boost:update`（已加入 `composer post-update-cmd`）。

## 測試

Host（需 PHP 8.3+）：

```bash
composer install
cp .env.example .env
php artisan key:generate
./vendor/bin/pest
```

Docker：

```bash
make pest
```

CI（GitHub Actions）使用 host PHP 8.4 + Node 22，測試強制 SQLite in-memory（見 `phpunit.xml`），不受 `.env.example` MySQL 設定影響。

## 升級摘要（L12 → L13）

- PHP `^8.3`，Laravel Framework `^13.0`，Tinker `^3.0`
- Pest `^4.0`，PHPUnit `^12.0`，Collision `^8.9`（^9 尚未發布）
- CSRF middleware 更名：`VerifyCsrfToken` → `PreventRequestForgery`
- `config/cache.php` 新增 `serializable_classes`
- 移除 Laravel Sail；改用 `./docker` + `docker-compose.yml`
- 建議在升級前固定：`CACHE_PREFIX`、`REDIS_PREFIX`、`SESSION_COOKIE`（已寫入 `.env.example`）

## 環境變數前綴（L13 安全）

升級後若未固定前綴，session / cache key 可能變更導致使用者登出。請在 `.env` 明確設定：

```env
CACHE_PREFIX=laravel_cache_
REDIS_PREFIX=laravel_database_
SESSION_COOKIE=laravel_session
```

## 自訂功能

- Image CRUD API：`/api/images`（部分路由排除 CSRF，供 API 測試）
- Xdebug 測試路由：`/debug-test`
