<?php
/**
 * Custom Domain Store Platform - Main Entry Point
 * Detects the incoming domain, looks up the store, renders the page.
 */

require_once __DIR__ . '/includes/Store.php';

// ─── Step 1: Detect the incoming domain ─────────────────────
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Remove port number if present (e.g., localhost:8080 → localhost)
$host = strtolower(preg_replace('/:\d+$/', '', $host));

// ─── Step 2: Look up the store by domain ────────────────────
$storeModel = new Store();
$store = $storeModel->findByDomain($host);

// ─── Step 3: Handle result ──────────────────────────────────
if (!$store) {
    http_response_code(404);
    require_once __DIR__ . '/templates/not-found.php';
    exit;
}

// ─── Step 4: Render the store page ──────────────────────────
require_once __DIR__ . '/templates/storefront.php';
