<?php
/**
 * Domain Verification API
 * Quick AJAX endpoint to check if a domain points to the server.
 * 
 * Usage: verify-domain.php?domain=mystore.com
 */

require_once __DIR__ . '/includes/Store.php';

header('Content-Type: application/json');

$domain = trim($_GET['domain'] ?? '');

if (empty($domain)) {
    echo json_encode([
        'success' => false,
        'message' => 'No domain provided'
    ]);
    exit;
}

// Clean the domain
$domain = strtolower(preg_replace('/^https?:\/\//', '', $domain));
$domain = rtrim($domain, '/');

$storeModel = new Store();
$result = $storeModel->verifyDomain($domain);

echo json_encode([
    'success'     => $result['is_verified'],
    'domain'      => $result['domain'],
    'resolved_ip' => $result['resolved_ip'],
    'expected_ip' => $result['server_ip'],
    'message'     => $result['message']
]);
