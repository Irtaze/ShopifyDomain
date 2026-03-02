<?php

declare(strict_types=1);

$storeName = htmlspecialchars((string) ($store['store_name'] ?? 'Store'), ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars((string) ($store['description'] ?? 'Welcome to our store!'), ENT_QUOTES, 'UTF-8');
$themeColor = htmlspecialchars((string) ($store['theme_color'] ?? '#4F46E5'), ENT_QUOTES, 'UTF-8');
$domain = htmlspecialchars((string) ($store['domain_name'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $storeName ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #fafafa; color: #111; }
        .hero { background: linear-gradient(135deg, <?= $themeColor ?>, #111827); color: #fff; padding: 72px 20px; text-align: center; }
        .hero h1 { margin: 0 0 10px; font-size: 42px; }
        .hero p { max-width: 680px; margin: 0 auto; line-height: 1.6; }
        .wrap { max-width: 980px; margin: 24px auto; padding: 0 16px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
        .footer { text-align: center; color: #666; padding: 24px 12px; }
    </style>
</head>
<body>
    <section class="hero">
        <h1><?= $storeName ?></h1>
        <p><?= $description ?></p>
    </section>

    <div class="wrap">
        <h2>Featured Products</h2>
        <div class="grid">
            <div class="card">Product A</div>
            <div class="card">Product B</div>
            <div class="card">Product C</div>
            <div class="card">Product D</div>
        </div>
    </div>

    <div class="footer">
        Domain: <?= $domain ?> | Powered by <?= htmlspecialchars(PLATFORM_NAME, ENT_QUOTES, 'UTF-8') ?>
    </div>
</body>
</html>
