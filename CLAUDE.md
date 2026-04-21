# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a TODO application specification. The project is in early stage — only the requirements document exists. The goal is to implement a full-stack TODO app based on `Requirements.md`.

## Architecture

Loosely coupled frontend/backend connected via REST API, all running in Docker:

- **Frontend** (`frontend/`): Vue 3 SPA + Vite dev server (port 5173)
- **Backend** (`backend/`): Laravel 11 REST API via PHP-FPM
- **DB**: MySQL 8.0 (port 3306)
- **Proxy**: nginx (port 80) — routes `/api/*` to PHP-FPM, everything else to Vite

## Expected Directory Structure

```
todo-app/
├── docker-compose.yml
├── docker/nginx/default.conf
├── docker/php/Dockerfile
├── backend/               # Laravel project
│   ├── app/Http/Controllers/TodoController.php
│   ├── database/migrations/
│   └── routes/api.php
└── frontend/              # Vue + Vite project
    ├── src/
    │   ├── components/TodoItem.vue
    │   ├── App.vue
    │   └── main.js
    ├── index.html
    └── vite.config.js
```

## API Endpoints

| Method | Endpoint          | Action              |
|--------|-------------------|---------------------|
| GET    | `/api/todos`      | List all todos      |
| POST   | `/api/todos`      | Create todo         |
| PATCH  | `/api/todos/{id}` | Toggle completed    |
| DELETE | `/api/todos/{id}` | Delete todo         |

## Data Model (`todos` table)

| Column       | Type         | Notes                  |
|--------------|--------------|------------------------|
| id           | BIGINT PK    |                        |
| title        | VARCHAR(255) |                        |
| is_completed | BOOLEAN      | Default: false         |
| created_at   | TIMESTAMP    |                        |
| updated_at   | TIMESTAMP    |                        |

## Development Commands (once implemented)

```bash
# Start all containers
docker compose up -d

# Backend (Laravel)
docker compose exec php php artisan migrate
docker compose exec php php artisan test
docker compose exec php ./vendor/bin/phpstan analyse

# Frontend (Vue/Vite)
docker compose exec frontend npm run dev
docker compose exec frontend npm run build
docker compose exec frontend npm run lint
```

## Non-functional Requirements

- Local development must run entirely in Docker (no host-machine installations)
- No user authentication, categories/tags, or due dates in scope
