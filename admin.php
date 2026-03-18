<?php

declare(strict_types=1);

require_once __DIR__ . '/middleware/tenant.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Store.php';
require_once __DIR__ . '/models/Domain.php';

$tenantId = current_tenant_id();
$userId = current_user_id();

$userModel = new User();
$storeModel = new Store();
$domainModel = new Domain();

$message = '';
$messageType = 'success';
$runtimeServerIp = $_SERVER['SERVER_ADDR'] ?? SERVER_IP;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_fail();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_store') {
        $storeName = trim((string) ($_POST['store_name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $themeColor = trim((string) ($_POST['theme_color'] ?? '#4F46E5'));

        if ($storeName === '') {
            $message = 'Store name is required.';
            $messageType = 'error';
        } else {
            $storeModel->upsertForTenant($tenantId, $storeName, $description, $themeColor);
            $message = 'Store settings updated.';
        }
    }

    if ($action === 'add_domain') {
        $domainName = strtolower(trim((string) ($_POST['domain_name'] ?? '')));
        $domainName = preg_replace('/^https?:\/\//', '', $domainName);
        $domainName = rtrim((string) $domainName, '/');
        $domainName = preg_replace('/:\d+$/', '', (string) $domainName);

        if ($domainName === '') {
            $message = 'Domain is required.';
            $messageType = 'error';
        } else {
            try {
                $domainModel->addDomain($tenantId, $domainName);
                $message = 'Domain added with pending status.';
            } catch (Throwable $e) {
                $message = 'Failed to add domain. It may already exist.';
                $messageType = 'error';
            }
        }
    }

    if ($action === 'delete_domain') {
        $domainName = strtolower(trim((string) ($_POST['domain_name'] ?? '')));
        $domainName = preg_replace('/^https?:\/\//', '', $domainName);
        $domainName = rtrim((string) $domainName, '/');
        $domainName = preg_replace('/:\d+$/', '', (string) $domainName);

        if ($domainName === '') {
            $message = 'Domain is required.';
            $messageType = 'error';
        } else {
            $deleted = $domainModel->deleteForTenant($tenantId, $domainName);
            if ($deleted) {
                $message = 'Domain deleted successfully.';
            } else {
                $message = 'Domain not found or already deleted.';
                $messageType = 'error';
            }
        }
    }
}

$user = $userModel->findById($userId);
$store = $storeModel->getByTenantId($tenantId);
$domains = $domainModel->allForTenant($tenantId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StoreHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f7f7fb; color: #222; }
        .topbar { background: #111827; color: #fff; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 980px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 10px; padding: 16px; margin-bottom: 16px; border: 1px solid #e5e7eb; }
        .grid { display: grid; gap: 10px; }
        .two { grid-template-columns: 1fr 1fr; }
        label { font-size: 13px; font-weight: 600; display: block; margin-bottom: 4px; }
        input, textarea, button, select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
        textarea { min-height: 90px; resize: vertical; }
        button { background: #2563eb; color: #fff; border: 0; cursor: pointer; }
        button.secondary { background: #374151; }
        button.danger { background: #dc2626; }
        .msg { padding: 10px; border-radius: 8px; margin-bottom: 12px; }
        .msg.success { background: #dcfce7; color: #166534; }
        .msg.error { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .pending { background: #fef3c7; color: #92400e; }
        .verified { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
        .inline { display: inline-flex; gap: 8px; align-items: center; }
    </style>
</head>
<body>
    <div class="topbar">
        <div>StoreHub Dashboard</div>
        <div class="inline">
            <span><?= e((string) ($user['email'] ?? '')) ?> | Tenant: <?= e($tenantId) ?></span>
            <a href="/logout.php" style="color:#fff;">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="msg <?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Store Settings</h2>
            <form method="POST">
                <?= csrf_input() ?>
                <input type="hidden" name="action" value="save_store">
                <div class="grid two">
                    <div>
                        <label>Store Name</label>
                        <input type="text" name="store_name" value="<?= e((string) ($store['store_name'] ?? '')) ?>" required>
                    </div>
                    <div>
                        <label>Theme Color</label>
                        <input type="text" name="theme_color" value="<?= e((string) ($store['theme_color'] ?? '#4F46E5')) ?>">
                    </div>
                </div>
                <div style="margin-top:10px;">
                    <label>Description</label>
                    <textarea name="description"><?= e((string) ($store['description'] ?? '')) ?></textarea>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit">Save Store</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Add Custom Domain</h2>
            <form method="POST">
                <?= csrf_input() ?>
                <input type="hidden" name="action" value="add_domain">
                <div class="grid two">
                    <div>
                        <label>Domain Name</label>
                        <input type="text" name="domain_name" placeholder="example.com" required>
                    </div>
                    <div style="display:flex;align-items:end;">
                        <button type="submit">Add Domain</button>
                    </div>
                </div>
            </form>
            <p>DNS setup: Add an <strong>A</strong> record for your domain and point it to this server IP: <strong><?= e((string) $runtimeServerIp) ?></strong> (configured target: <strong><?= e(SERVER_IP) ?></strong>).</p>
            <p>DNS setup: Add an <strong>WWW</strong> record for your domain and point it to this server IP: <strong><?= e((string) $runtimeServerIp) ?></strong> (configured target: <strong><?= e(SERVER_IP) ?></strong>).</p>
        </div>

        <div class="card">
            <h2>Your Domains</h2>
            <table>
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$domains): ?>
                    <tr><td colspan="4">No domains added yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($domains as $domain): ?>
                        <tr>
                            <td><?= e((string) $domain['domain_name']) ?></td>
                            <td><span class="pill <?= e((string) $domain['status']) ?>"><?= e((string) $domain['status']) ?></span></td>
                            <td><?= e((string) $domain['created_at']) ?></td>
                            <td>
                                <div class="inline">
                                    <form class="verify-form" method="POST" action="/verify-domain.php">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="domain" value="<?= e((string) $domain['domain_name']) ?>">
                                        <button type="submit" class="secondary">Verify DNS</button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('Delete this domain?');">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="action" value="delete_domain">
                                        <input type="hidden" name="domain_name" value="<?= e((string) $domain['domain_name']) ?>">
                                        <button type="submit" class="danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.querySelectorAll('.verify-form').forEach(function(form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const data = new FormData(form);
                const response = await fetch('/verify-domain.php', {
                    method: 'POST',
                    body: data
                });
                const result = await response.json();
                alert(result.message || 'Verification completed');
                window.location.reload();
            });
        });
    </script>
</body>
</html>
