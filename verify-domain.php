<?php

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/middleware/tenant.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/models/Domain.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

csrf_verify_or_fail();

$domain = strtolower(trim((string) ($_POST['domain'] ?? '')));
$domain = preg_replace('/^https?:\/\//', '', $domain);
$domain = rtrim($domain, '/');
$domain = preg_replace('/:\d+$/', '', $domain);

if ($domain === '') {
    echo json_encode(['success' => false, 'message' => 'Domain is required']);
    exit;
}

$tenantId = current_tenant_id();
$domainModel = new Domain();

if (!$domainModel->belongsToTenant($tenantId, $domain)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Domain does not belong to current tenant']);
    exit;
}

$resolvedIp = gethostbyname($domain);
$allowedIps = array_map('trim', explode(',', SERVER_IPS));
$isVerified = in_array($resolvedIp, $allowedIps, true);

if ($isVerified) {
    $domainModel->markVerifiedForTenant($tenantId, $domain);
}

echo json_encode([
    'success' => $isVerified,
    'domain' => $domain,
    'resolved_ip' => $resolvedIp,
    'expected_ip' => SERVER_IP,
    'message' => $isVerified
        ? 'Domain is correctly pointing to your server.'
        : 'Domain DNS does not match the configured server IP.',
]);
