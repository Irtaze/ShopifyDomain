<?php

declare(strict_types=1);

$requestedDomain = htmlspecialchars((string) ($_SERVER['HTTP_HOST'] ?? 'unknown'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Not Found</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; min-height: 100vh; display: grid; place-items: center; background: #111827; color: #fff; }
        .box { max-width: 680px; text-align: center; padding: 24px; }
        .domain { background: #1f2937; border: 1px solid #374151; border-radius: 10px; padding: 12px; margin: 16px 0; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Store Not Found</h1>
        <p>This domain is not linked to any verified tenant storefront.</p>
        <div class="domain"><?= $requestedDomain ?></div>
        <p>Set DNS A record to: <strong><?= htmlspecialchars(SERVER_IP, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p>Need help: <?= htmlspecialchars(SUPPORT_EMAIL, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</body>
</html>
