# StoreHub SaaS (Multi-tenant, MySQL)

StoreHub now runs as a SaaS multi-tenant app with:

- MySQL only (no SQLite)
- Tenant isolation by `tenant_id`
- Session-based dashboard auth
- Custom domains mapped to `tenant_id`

## Architecture

1. User registers, system creates unique `tenant_id`.
2. User logs in, session stores:
   - `$_SESSION['user_id']`
   - `$_SESSION['tenant_id']`
3. Dashboard (`admin.php`) is protected by middleware.
4. User store data is saved in `stores` by `tenant_id`.
5. User domains are saved in `custom_domains` by `tenant_id`.
6. `verify-domain.php` checks DNS A record and marks domain `verified`.
7. `index.php` resolves incoming host -> verified domain -> `tenant_id` -> store.

## Project Structure

```text
config/config.php
database/schema.sql
middleware/auth.php
middleware/tenant.php
models/Database.php
models/User.php
models/Store.php
models/Domain.php
includes/security.php
index.php
admin.php
register.php
login.php
logout.php
verify-domain.php
templates/storefront.php
templates/not-found.php
```

## Environment

- PHP 8.0+
- Extensions: `pdo_mysql`
- MySQL/MariaDB

## Config

Update `config/config.php`:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_PORT`
- `SERVER_IP`
- `APP_ENV`

## Database Setup

Import `database/schema.sql` in phpMyAdmin.

Tables:

- `users`
- `stores`
- `custom_domains`

## Auth Endpoints

- `/register.php`
- `/login.php`
- `/logout.php`
- `/admin.php` (requires session auth)

## Domain Verification

`verify-domain.php`:

- requires authenticated session
- requires CSRF token
- verifies DNS A record against `SERVER_IP`
- updates `custom_domains.status` to `verified` for current tenant

## Security

- Passwords use `password_hash()` and `password_verify()`
- Session ID regenerated on login
- CSRF token on forms and verification endpoint
- Prepared statements everywhere
- Tenant scope enforced in dashboard and domain verification
