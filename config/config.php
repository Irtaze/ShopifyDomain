<?php
/**
 * Application Configuration
 * Custom Domain Store Platform
 * 
 * ⚠️  UPDATE THESE VALUES BEFORE DEPLOYING TO HOSTINGER
 */

// ─── Database Settings ──────────────────────────────────────────
define('DB_PATH', dirname(__DIR__) . '/database/store.db');

// ─── Server Settings ────────────────────────────────────────────
// Replace with your Hostinger server IP address.
// Find it in: hPanel → Hosting → Server Details → IP Address
define('SERVER_IP', 'YOUR_HOSTINGER_IP');  // e.g., '2.57.91.91'
define('SERVER_IPS', '127.0.0.1,' . SERVER_IP);

// ─── Platform Settings ──────────────────────────────────────────
define('PLATFORM_NAME', 'StoreHub');
define('PLATFORM_DOMAIN', 'yourdomain.com');  // Your main Hostinger domain

// ─── Admin Security ─────────────────────────────────────────────
// Change this to a secret key. Admin panel requires ?key=THIS_VALUE
define('ADMIN_KEY', 'change-this-to-a-secret-key');

// ─── Path Settings ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('ASSETS_PATH', BASE_PATH . '/assets');
