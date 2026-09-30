# Document Catalog

MVP document metadata catalog: XML ingest → SQLite → authenticated JSON API → React table with filter/sort.

## Stack


| Layer    | Choice                                                         |
| -------- | -------------------------------------------------------------- |
| Backend  | Laravel 12 (PHP 8.2) + Laravel Sanctum (SPA cookie session)    |
| Frontend | Vite + React + TypeScript + SCSS                               |
| Edge     | Nginx reverse proxy (API + SPA on one origin)                  |
| Runtime  | Docker Compose (`nginx`, `backend` php-fpm, `frontend` static) |
| Storage  | SQLite on a Docker volume at `/data/database.sqlite`           |




## Why SQLite

This is a small project, so i decided to keep it simple.

SQLite keeps setup to a single file on a named volume, which is enough for demo data and Feature tests. In a larger production system with multiple app instances, heavier write traffic, or stricter operational needs, PostgreSQL (or similar) would be the better default.

## Quick start (Docker)

**Requirements:** Docker + Docker Compose.

1. Copy env

```bash
cp .env.example .env
```

1. Build and start:

```bash
docker compose up --build
```

1. Open [http://localhost:8080](http://localhost:8080).

On first boot the backend migrates, seeds the demo user, generates sample XML if missing, and syncs documents into SQLite.

### Demo login


| Field    | Value                  |
| -------- | ---------------------- |
| Email    | `reviewer@example.com` |
| Password | `password`             |




## Artisan commands (demo data)

Run these against the backend container:

```bash
# Regenerate sample remote XML (default 25 documents)
docker compose exec backend php artisan documents:generate-sample --count=25

# Sync XML → SQLite via DOCUMENTS_REMOTE_URL (http://nginx/remote/documents.xml)
docker compose exec backend php artisan documents:sync

# Sync from the local XML file (no HTTP)
docker compose exec backend php artisan documents:sync --local

# Delete all document rows (keeps users)
docker compose exec backend php artisan documents:wipe
```



## Tests

```bash
cd backend
composer install
cp .env.example .env   # if needed; phpunit uses its own sqlite setup
php artisan key:generate
php artisan test
```



## Project layout

```
backend/     Laravel API, XML import, Artisan commands, Feature tests
frontend/    React SPA (login + documents table)
docker/      Nginx config
```

