# StoreHub — Custom Domain Store Platform

A PHP platform where multiple clients connect their own custom domains to individual stores — all served from a single server. When someone visits `clientstore.com`, the app detects the domain, looks it up in the database, and loads the correct store automatically.

---

## How It Works

```
Client owns "clientstore.com"
        │
        ▼
Client sets DNS A record → clientstore.com → YOUR_SERVER_IP
        │
        ▼
Visitor types "clientstore.com" in browser
        │
        ▼
DNS routes request to YOUR server
        │
        ▼
PHP reads HTTP_HOST header → "clientstore.com"
        │
        ▼
Database: SELECT * FROM stores WHERE custom_domain = 'clientstore.com'
        │
        ▼
Found → Render that client's store (their name, theme, products)
Not found → Show 404 page with setup instructions
```

**Your domain is never visible.** Visitors only see the client's domain in the URL bar.

---

## Project Structure

```
├── config/
│   └── config.php          ← ⚠️ UPDATE THIS BEFORE DEPLOYING
├── database/
│   └── store.db            ← Auto-created on first run (SQLite)
├── includes/
│   ├── Database.php         ← SQLite connection + auto-init
│   └── Store.php            ← Store model (CRUD + domain verification)
├── templates/
│   ├── storefront.php       ← What visitors see (themed per store)
│   └── not-found.php        ← 404 page for unmapped domains
├── index.php                ← Main entry point (domain detection)
├── admin.php                ← Admin panel (protected by secret key)
├── verify-domain.php        ← JSON API for domain verification
├── .htaccess                ← Apache URL rewriting
└── README.md
```

---

## Requirements

| Component | Version | Notes |
|-----------|---------|-------|
| PHP | 8.0+ | With `pdo_sqlite` extension |
| SQLite | 3.x | Ships with PHP — no install needed |
| Apache | 2.4+ | With `mod_rewrite` enabled |

**No MySQL needed.** Uses SQLite — zero configuration database.

---

# Deploying on Hostinger — Complete Guide

## Step 1: Get Your Hostinger Server IP

1. Log in to [Hostinger hPanel](https://hpanel.hostinger.com)
2. Go to **Hosting** → select your plan
3. Look for **Server Details** or **IP Address**
4. Copy the IP address (e.g., `2.57.91.91`)

## Step 2: Update `config/config.php`

Open `config/config.php` and update these values:

```php
// Replace with your Hostinger server IP
define('SERVER_IP', '2.57.91.91');        // ← Your actual IP
define('SERVER_IPS', '127.0.0.1,2.57.91.91');

// Your main domain on Hostinger
define('PLATFORM_DOMAIN', 'yourdomain.com');

// Change this to a random secret string
define('ADMIN_KEY', 'my-super-secret-key-2026');
```

## Step 3: Upload Files to Hostinger

### Option A: File Manager (Easiest)
1. In hPanel → **File Manager**
2. Navigate to `public_html/` (this is your web root)
3. **Delete** everything inside `public_html/` (default Hostinger files)
4. Upload all project files into `public_html/`:

```
public_html/
├── config/
│   └── config.php
├── database/           ← Create this empty folder
├── includes/
│   ├── Database.php
│   └── Store.php
├── templates/
│   ├── storefront.php
│   └── not-found.php
├── index.php
├── admin.php
├── verify-domain.php
├── .htaccess
└── README.md
```

### Option B: FTP Upload
1. In hPanel → **FTP Accounts** → get FTP credentials
2. Use FileZilla or WinSCP
3. Connect and upload files to `public_html/`

### Option C: Git (If VPS)
```bash
cd /var/www/html
git clone https://github.com/your-repo/storehub.git .
```

## Step 4: Set Folder Permissions

In File Manager or SSH:
- `database/` folder needs **write permission** (chmod 775)
- The `store.db` file will be auto-created on first visit

If using SSH:
```bash
chmod 775 /home/YOUR_USER/public_html/database/
```

## Step 5: Verify PHP Extensions

In hPanel → **PHP Configuration**:
- Make sure **PHP 8.0+** is selected
- Enable extensions: `pdo_sqlite`, `sqlite3`

## Step 6: Test Your Setup

Visit your Hostinger domain:
- `https://yourdomain.com` → Should show "Store Not Found" (no stores yet)
- `https://yourdomain.com/admin.php?key=my-super-secret-key-2026` → Admin panel

**If you see the admin panel, you're ready!**

## Step 7: Add Your First Real Store

1. Go to admin panel: `https://yourdomain.com/admin.php?key=YOUR_KEY`
2. Click **"+ Add Store"**
3. Fill in:
   - **Store Name:** Client's store name
   - **Custom Domain:** `clientstore.com` (their domain)
   - **Email:** Client's email
   - **Theme Color:** Pick a color
4. Click **"Create Store"**

---

# How Clients Connect Their Domain

## What You Tell Your Client

Send them this message:

> **To connect your domain to your store:**
> 
> 1. Log in to your domain registrar (GoDaddy, Namecheap, Cloudflare, Hostinger, etc.)
> 2. Go to **DNS Settings** for your domain
> 3. Add an **A Record** with these settings:
> 
> | Type | Host/Name | Value | TTL |
> |------|-----------|-------|-----|
> | A    | @         | `YOUR_SERVER_IP` | 3600 |
> 
> 4. Wait 5–30 minutes for DNS to propagate
> 5. Your store will be live at your domain!
> 
> **That's it!** Once set up, visitors to your domain will see your store.

## What Happens Behind the Scenes

```
Client sets: clientstore.com → A Record → 2.57.91.91 (your server)
                                              │
Visitor types clientstore.com ───────────────►│
                                              │
Apache accepts (ServerAlias * or addon domain)│
                                              │
PHP: $_SERVER['HTTP_HOST'] = 'clientstore.com'│
                                              │
DB: WHERE custom_domain = 'clientstore.com'   │
                                              │
✅ Store found → Render storefront             │
URL bar: clientstore.com (your domain hidden) │
```

## After Client Sets DNS

1. Go to admin panel
2. Find their store in the list
3. Click **"Verify"** button
4. If DNS is correct → turns **green** (Verified ✓)
5. Store is now live!

---

# DNS Setup Examples (For Different Registrars)

## GoDaddy
1. My Products → Domains → DNS → Manage
2. Add Record → Type: **A** → Name: **@** → Value: **YOUR_IP** → TTL: **1 Hour**

## Namecheap
1. Domain List → Manage → Advanced DNS
2. Add New Record → Type: **A** → Host: **@** → Value: **YOUR_IP** → TTL: **Automatic**

## Cloudflare
1. Select domain → DNS → Records
2. Add Record → Type: **A** → Name: **@** → IPv4: **YOUR_IP** → Proxy: **OFF** (DNS only)

## Hostinger
1. hPanel → Domains → DNS Zone
2. Add Record → Type: **A** → Name: **@** → Points to: **YOUR_IP** → TTL: **3600**

---

# Hostinger-Specific: Handling Multiple Domains

## Shared Hosting Plan

On shared hosting, you need to add each client's domain:

1. hPanel → **Domains** → **Add Domain**
2. Enter client's domain (e.g., `clientstore.com`)
3. Choose **Parked/Addon Domain** (points to same public_html)
4. Client sets their DNS to your Hostinger IP

## VPS Plan (Best for This App)

On a VPS, set up Apache to accept **any domain** automatically:

```bash
sudo nano /etc/apache2/sites-available/storehub.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias *

    DocumentRoot /var/www/html

    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/storehub-error.log
    CustomLog ${APACHE_LOG_DIR}/storehub-access.log combined
</VirtualHost>
```

```bash
sudo a2enmod rewrite
sudo a2ensite storehub.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

With `ServerAlias *`, **any domain** that points to your IP works automatically — no need to add each one manually.

## SSL Certificates (HTTPS)

For each client domain, issue an SSL certificate:

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d clientstore.com
```

For automated cert issuance when a new store is verified, create a script:

```bash
#!/bin/bash
# /var/www/html/scripts/issue-cert.sh
sudo certbot --apache -d "$1" --non-interactive --agree-tos -m admin@yourdomain.com
```

---

# Admin Panel

Access: `https://yourdomain.com/admin.php?key=YOUR_ADMIN_KEY`

| Feature | Description |
|---------|-------------|
| **Dashboard** | Total stores, verified count, active count |
| **Create Store** | Add store name, domain, email, theme color |
| **Edit Store** | Update any store's details |
| **Delete Store** | Remove a store permanently |
| **Verify Domain** | One-click DNS check (A record verification) |
| **Toggle Active** | Enable/disable a store without deleting |

**The admin panel is protected by a secret key.** Set it in `config.php` → `ADMIN_KEY`.

---

# API

### `GET /verify-domain.php?domain=clientstore.com`

Checks if a domain's DNS A record points to your server.

```json
{
  "success": true,
  "domain": "clientstore.com",
  "resolved_ip": "2.57.91.91",
  "expected_ip": "2.57.91.91",
  "message": "✅ Domain is correctly pointing to your server"
}
```

---

# Things to Update Before Deploying

| File | Setting | What to Change |
|------|---------|----------------|
| `config/config.php` | `SERVER_IP` | Your Hostinger server IP |
| `config/config.php` | `PLATFORM_DOMAIN` | Your main domain |
| `config/config.php` | `ADMIN_KEY` | A random secret key for admin access |
| `templates/not-found.php` | Contact email | Update support email if needed |

---

# FAQ

### Can multiple domains point to one server?
Yes! That's the entire point. Every client domain points to YOUR server IP. PHP determines which store to show based on the domain name.

### Will my Hostinger domain show in the URL?
No. When someone visits `clientstore.com`, they see `clientstore.com` in the URL bar. Your hosting domain is completely hidden.

### What's the difference between Shared and VPS hosting?
- **Shared:** Must add each client domain manually in hPanel as an addon/parked domain
- **VPS:** Use `ServerAlias *` in Apache to auto-accept ALL domains. No manual setup per domain.

### How many stores can it handle?
SQLite handles hundreds of thousands of rows easily. The domain lookup is indexed (UNIQUE constraint). For millions of requests/day, consider upgrading to MySQL + Redis caching.

### How do I reset and start fresh?
Delete `database/store.db` and refresh the page. A new empty database will be created automatically.

### How do I change the admin key?
Edit `config/config.php` → change `ADMIN_KEY` value. All old admin links will stop working immediately.

---

# Deployment Checklist

```
[ ] Update config/config.php with your Hostinger IP
[ ] Set ADMIN_KEY to a random secret string
[ ] Set PLATFORM_DOMAIN to your main domain
[ ] Upload all files to public_html/
[ ] Create empty database/ folder with write permissions
[ ] Verify PHP 8.0+ with pdo_sqlite enabled
[ ] Test: visit your domain → should see "Store Not Found"
[ ] Test: visit admin.php?key=YOUR_KEY → should see admin panel
[ ] Create first client store in admin
[ ] Have client set DNS A record
[ ] Click Verify → should turn green
[ ] Visit client's domain → should show their store
[ ] Set up SSL with Certbot (VPS) or Hostinger SSL (shared)
[ ] Done! 🚀
```
