# Decisions

## Laravel 12 + PHP 8.4

Chosen for modern baseline, typed code, and current ecosystem.

## PostgreSQL

Used for realistic production-like setup in Docker and robust relational data.

## Spatie packages

-   `spatie/laravel-permission`: roles for admin access (manager role).
-   `spatie/laravel-medialibrary`: ticket attachments with convenient storage + URLs.

## Rate limiting approach

Requirement: max 1 ticket/day per phone/email.
Implemented via named limiter `ticket-submit` and middleware `throttle:ticket-submit`.
Limiter keys are built from provided email and/or phone (can apply both).

## API design

-   `POST /api/tickets` accepts JSON or multipart (attachments).
-   `GET /api/tickets/statistics` returns aggregated counts for day/week/month.

## Admin UI

Server-rendered Blade + Tailwind via Vite for simple, modern UI with minimal dependencies.

## Testing

Feature tests cover:

-   ticket creation + validation
-   rate limiting (429)
-   statistics aggregation
    Recommended separate test DB (`crm_test`).
