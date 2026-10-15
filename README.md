# PhoneNotify Backend (Laravel 11)

Laravel API for collecting Android notification streams and managing them via a dashboard.

## Public API base

`https://phonenotify.hi-team.ir/api/v1`

See `API_CONTRACT.md` (sibling docs) for the full endpoint contract.

## Local / VPS setup

1. Copy `.env.example` → `.env`, set `APP_KEY`, DB creds, `ADMIN_PASSWORD`.
2. Docker (VPS pattern at `/opt/apps/phonenotify`):

```bash
docker compose build
docker compose up -d
docker compose exec phonenotify-backend php artisan migrate --force
docker compose exec phonenotify-backend php artisan db:seed --force
docker compose exec phonenotify-backend php artisan storage:link
```

3. Host Apache proxies `https://phonenotify.hi-team.ir/api` → `http://127.0.0.1:3211/api`.

## Auth

- Admin: `POST /api/v1/auth/login` with email/password (Sanctum token).
- Device: `POST /api/v1/devices/register` returns `device_token`; send as `Authorization: Bearer …`.

## Notes

- APKs stored in Laravel `storage/app/apks` (Docker volume `phonenotify-storage`).
- Shared MySQL container hostname inside Docker: `mysql` (shared-network).
