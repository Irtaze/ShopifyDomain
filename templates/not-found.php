<?php
/**
 * 404 - Store Not Found Template
 * Shown when the incoming domain doesn't match any store.
 */
$requestedDomain = htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'unknown');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Not Found - <?= PLATFORM_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .container { max-width: 500px; }
        .icon { font-size: 5rem; margin-bottom: 1rem; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        p { font-size: 1.1rem; opacity: 0.9; line-height: 1.6; margin-bottom: 1.5rem; }
        .domain-box {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 0.8rem 1.2rem;
            font-family: monospace;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        .help-text {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-top: 1rem;
        }
        .steps {
            text-align: left;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 1.2rem 1.5rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            line-height: 2;
        }
        .steps strong { opacity: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔍</div>
        <h1>Store Not Found</h1>
        <p>The domain you're visiting is not connected to any store on our platform.</p>
        
        <div class="domain-box"><?= $requestedDomain ?></div>
        
        <div class="steps">
            <strong>To connect your domain:</strong><br>
            1️⃣ Contact us to register your store on <?= PLATFORM_NAME ?><br>
            2️⃣ We'll set up your store with your custom domain<br>
            3️⃣ Go to your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.)<br>
            4️⃣ Add an <strong>A record</strong> pointing to: <code style="background:rgba(255,255,255,0.2); padding:0.1rem 0.4rem; border-radius:3px;"><?= SERVER_IP ?></code><br>
            5️⃣ Wait 5–30 minutes for DNS to propagate<br>
            6️⃣ Your store will be live automatically! 🎉
        </div>

        <div class="steps" style="margin-top: 1rem; opacity: 0.8;">
            <strong>DNS Settings:</strong><br>
            <span style="font-family: monospace; font-size: 0.85rem;">
            Type: A &nbsp;|&nbsp; Host: @ &nbsp;|&nbsp; Value: <?= SERVER_IP ?> &nbsp;|&nbsp; TTL: 3600
            </span>
        </div>
        
        <p class="help-text">
            Need help? Contact us at <strong>support@<?= PLATFORM_DOMAIN ?></strong>
        </p>
    </div>
</body>
</html>
        </div>
        
        <p class="help-text">
            Need help? Contact us at <strong>support@storehub.com</strong>
        </p>
    </div>
</body>
</html>
