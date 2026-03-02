<?php

declare(strict_types=1);

/**
 * Multi-tenant storefront resolver.
 */

require_once __DIR__ . '/models/Domain.php';
require_once __DIR__ . '/models/Store.php';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$host = strtolower(preg_replace('/:\d+$/', '', $host));

$domainModel = new Domain();
$storeModel = new Store();

$tenantId = $domainModel->findTenantIdByVerifiedDomain($host);
if (!$tenantId) {
    http_response_code(404);
    require_once __DIR__ . '/templates/not-found.php';
    exit;
}

$store = $storeModel->getByTenantId($tenantId);
if (!$store) {
    http_response_code(404);
    require_once __DIR__ . '/templates/not-found.php';
    exit;
}

$store['domain_name'] = $host;
require_once __DIR__ . '/templates/storefront.php';
