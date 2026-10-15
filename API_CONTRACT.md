# PhoneNotify — Shared API Contract (v1)

Single source of truth for Backend (Laravel), Dashboard (Next.js), Android app.

## Deployment
- Public domain: https://phonenotify.hi-team.ir
- Dashboard (Next.js) served at `/`
- Laravel API served at `/api` (host Apache routes /api -> backend-nginx container; / -> dash container)
- Mirror the /opt/apps/poolak docker pattern; use shared-mysql + shared-network.
- DB: dedicated database `phonenotify` on shared-mysql.

## Auth
- Dashboard admin auth: Laravel Sanctum, email+password. Seeded admin.
- Device (Android) auth: on first consent the app calls POST /api/v1/devices/register and receives a device_token (Sanctum personal access token or opaque token). All device calls send `Authorization: Bearer <device_token>`.

## Data model (MySQL)
- users: id, name, email, password, role(admin), timestamps
- devices: id, device_uuid (unique, app-generated), token, brand, model, manufacturer, os_version, sdk_int, app_version, fcm_token(nullable), label(nullable), notification_access(bool), battery_optimization_ignored(bool), last_seen_at, created_at
- device_states: id, device_id, has_notification_permission(bool), battery_level(int), is_charging(bool), network_type, latitude(nullable), longitude(nullable), location_accuracy(nullable), reported_at
- notifications: id, device_id, package_name, app_label(nullable), title(nullable), text(nullable longtext), subtext(nullable), posted_at(device time), received_at(server time), status enum[received,processed], extras(json nullable), unique(device_id, device_notif_key)
- app_errors: id, device_id(nullable), level enum[error,warn,fatal], message, stacktrace(longtext), app_version, os_version, context(json), created_at
- app_versions: id, version_name, version_code(int, unique), file_path, file_size, sha256, release_notes(text), is_active(bool), downloads(int default 0), created_at

## API endpoints (prefix /api/v1)
### Public
- GET  /app/latest            -> latest active app_version metadata {version_name,version_code,release_notes,download_url,file_size}
- GET  /app/download          -> streams latest active APK (increments downloads)
- GET  /app/download/{code}   -> streams specific version APK

### Device (Bearer device_token)
- POST /devices/register      -> body: device_uuid,brand,model,manufacturer,os_version,sdk_int,app_version -> {device_token, device_id}
- POST /devices/heartbeat     -> body: state fields (permission,battery,network,lat,lng) -> updates last_seen + device_states
- POST /notifications         -> body: array or single {device_notif_key,package_name,app_label,title,text,subtext,posted_at,extras} -> returns accepted ids + status
- POST /errors                -> body: {level,message,stacktrace,app_version,os_version,context}

### Admin (Sanctum session/token, role=admin)
- POST /auth/login, POST /auth/logout, GET /auth/me
- GET  /admin/users (CRUD)        -> users management
- GET  /admin/devices             -> list w/ filters (online/offline, brand), paginated
- GET  /admin/devices/{id}        -> full detail incl latest state, counts
- GET  /admin/devices/{id}/states -> location history (for map)
- GET  /admin/devices/map         -> all devices latest lat/lng for map
- GET  /admin/notifications       -> filter by device, package, date, status; paginated
- GET  /admin/errors              -> filter by device, level; paginated
- GET  /admin/app-versions (CRUD) + POST /admin/app-versions (multipart APK upload) + PATCH activate
- GET  /admin/stats               -> dashboard summary counts

## Conventions
- JSON responses: {data, meta} for lists; {data} for single; errors {message, errors}.
- Timestamps ISO-8601 UTC.
- Online = last_seen_at within 5 min.
- CORS: allow dashboard origin + device (any) for /api/v1/devices,notifications,errors,app.
