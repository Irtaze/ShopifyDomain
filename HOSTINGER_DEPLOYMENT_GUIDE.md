# Hostinger Deployment Guide (Shared Hosting, No VPS)

This project is a MySQL-only multi-tenant SaaS.

## 1) Prepare Hostinger

1. Add domain: `danzi.shop`.
2. Enable SSL.
3. Set PHP 8.0+ and enable `pdo_mysql`.

## 2) Create MySQL DB

1. hPanel -> Databases -> MySQL Databases.
2. Create database + user.
3. Save:
   - DB name
   - DB user
   - DB password
   - DB host (`localhost`)
   - DB port (`3306`)

## 3) Import Schema

Import `database/schema.sql` using phpMyAdmin.

## 4) Configure App

Edit `config/config.php`:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_PORT`
- `SERVER_IP` (from hPanel server details)
- `PLATFORM_DOMAIN` (`danzi.shop`)
- `SUPPORT_EMAIL`

## 5) Deploy Directly on Hostinger (Simple, No VPS, No Git)

1. On your computer, zip the full project folder.
2. In hPanel, open **Files -> File Manager**.
3. Go to your domain web root:
   - usually `public_html` (or `domains/danzi.shop/public_html`)
4. Upload the zip file there.
5. Extract it.
6. If extracted into a subfolder, move all project files so `index.php` is directly inside `public_html`.
7. Confirm these paths exist in web root:
   - `index.php`
   - `admin.php`
   - `register.php`
   - `.htaccess`
   - `config/config.php`
8. Open `config/config.php` in File Manager and set production DB credentials.

## 6) Optional: Deploy with Git

Use Hostinger terminal in web root (for the domain):

```bash
cd ~/domains/danzi.shop/public_html
git clone https://github.com/<your-org>/<your-repo>.git .
```

For updates:

```bash
cd ~/domains/danzi.shop/public_html
git pull origin main
```

## 7) Point Domains in Hostinger (Required for Custom Domains)

1. In hPanel, add each customer domain as a parked/addon domain so it serves the same web root.
2. For each domain DNS zone, set `A` record to your Hostinger server IP (`SERVER_IP`).
3. Wait for DNS propagation.
4. In dashboard, add the domain and click `Verify DNS`.

## 8) Test Auth + Tenant Flow

1. Open `/register.php`, create account.
2. Open `/login.php`, sign in.
3. Open `/admin.php`, configure store.
4. Add custom domain in dashboard.
5. Set domain A record to `SERVER_IP`.
6. Click `Verify DNS` in dashboard.
7. Open verified domain, storefront should resolve by `tenant_id`.
