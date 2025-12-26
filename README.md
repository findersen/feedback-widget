# Feedback Widget (Laravel 12)

A small demo project: **public feedback widget** + **API** + **admin panel** for managing tickets.

## Tech stack

-   PHP 8.4, Laravel 12
-   PostgreSQL (Docker)
-   Spatie: `laravel-permission`, `laravel-medialibrary`
-   TailwindCSS + Vite (frontend assets)

---

## Features

### Public widget

-   Page: `GET /widget`
-   Submit form via AJAX to: `POST /api/tickets`
-   Supports optional file attachments (stored via Spatie MediaLibrary)

### API

-   `POST /api/tickets` — create ticket (rate-limited: max 1 ticket / day per phone/email)
-   `GET /api/tickets/statistics` — `{ day, week, month }` counts

### Admin

-   Page: `GET /admin/tickets` (requires auth + `manager` role)
-   Ticket list with filters/search + ticket details page
-   Status update (`new` → `in_progress` → `done`)

### Tests

Feature tests included for:

-   Ticket creation
-   Rate limit (per email/phone per day)
-   Statistics endpoint

---

## Local development (Docker)

### 1) Clone + env

```bash
git clone https://github.com/findersen/feedback-widget.git
cd feedback-widget

cp .env.example .env
# ensure DB points to docker service:
# DB_CONNECTION=pgsql
# DB_HOST=db
# DB_PORT=5432
# DB_DATABASE=crm
# DB_USERNAME=crm
# DB_PASSWORD=crm
```

### 2) Start containers

```bash
docker compose up -d --build
```

### 3) Install dependencies (inside container)

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 4) Migrations + seed

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

### 5) Storage (attachments)

```bash
docker compose exec app php artisan storage:link
```

### 6) Frontend assets (Tailwind)

Development (watch):

```bash
docker compose exec app npm install
docker compose exec app npm run dev
```

Production build:

```bash
docker compose exec app npm run build
```

## Default credentials (seeded)

After seeding, a demo manager user is created.
Check your Database\\Seeders\\DemoDataSeeder for the exact email/password used.

Login:
GET /login

Admin:
GET /admin/tickets

## API usage examples

### Create ticket

```bash
curl -X POST http://localhost:8080/api/tickets \
  -H "Accept: application/json" \
  -F "customer[name]=John Doe" \
  -F "customer[email]=test@example.com" \
  -F "customer[phone]=+380501112233" \
  -F "subject=Hello" \
  -F "message=Test message" \
  -F "files[]=@/path/to/file1.png"
```

### Statistics

```bash
curl http://localhost:8080/api/tickets/statistics \
  -H "Accept: application/json"
```

### Expected response:

```javascript
{ "day": 0, "week": 0, "month": 0 }
```

## Running tests

```bash
docker compose exec app php artisan test
```

If you want a clean DB run:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
```

## Documentation

Swagger/OpenAPI spec: docs/swagger.yaml
Architecture / decisions: docs/decisions.md

## Notes

-   Rate limit is enforced per day for a given email or phone (E.164 format).
-   Attachments are stored via MediaLibrary collection attachments.
-   Tailwind classes require running npm run dev (or npm run build) so Vite generates assets.
