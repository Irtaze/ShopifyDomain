<?php
/**
 * Application Configuration
 * Multi-tenant SaaS - MySQL only
 */

define('DB_PASSWORD', '');
define('SERVER_IP', '127.0.0.1');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_NAME', 'storehub_local');
define('DB_PORT', 3306);
define('DB_ENGINE', 'mysql');
define('APP_ENV', 'local');

define('SERVER_IPS', '127.0.0.1,' . SERVER_IP);

define('PLATFORM_NAME', 'StoreHub');
define('PLATFORM_DOMAIN', 'danzi.shop');
define('SUPPORT_EMAIL', 'support@danzi.shop');

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('ASSETS_PATH', BASE_PATH . '/assets');
