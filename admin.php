<?php
/**
 * Admin Panel - Store Management
 * Full CRUD for managing stores and verifying domains
 */

require_once __DIR__ . '/includes/Store.php';

// ─── Admin Access Protection ────────────────────────────────
// Access: admin.php?key=YOUR_ADMIN_KEY (set in config.php)
$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';
if ($providedKey !== ADMIN_KEY) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>Access Denied</title>
    <style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#1a1a2e;color:#fff;text-align:center;}
    .box{max-width:400px;}.icon{font-size:4rem;margin-bottom:1rem;}h1{margin-bottom:0.5rem;}p{opacity:0.7;font-size:0.95rem;}</style></head>
    <body><div class="box"><div class="icon">🔒</div><h1>Access Denied</h1><p>Admin panel requires a valid key.<br><code>admin.php?key=your-secret-key</code></p></div></body></html>';
    exit;
}
$adminKey = htmlspecialchars($providedKey);

$storeModel = new Store();
$message = '';
$messageType = '';

// ─── Handle Actions ─────────────────────────────────────────
$action = $_GET['action'] ?? 'list';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    switch ($postAction) {
        case 'create':
            $id = $storeModel->create([
                'store_name'    => trim($_POST['store_name'] ?? ''),
                'custom_domain' => strtolower(trim($_POST['custom_domain'] ?? '')),
                'owner_email'   => trim($_POST['owner_email'] ?? ''),
                'description'   => trim($_POST['description'] ?? ''),
                'theme_color'   => $_POST['theme_color'] ?? '#4F46E5',
            ]);
            if ($id) {
                $message = "Store created successfully! (ID: {$id})";
                $messageType = 'success';
            } else {
                $message = "Failed to create store. Domain may already exist.";
                $messageType = 'error';
            }
            $action = 'list';
            break;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $result = $storeModel->update($id, [
                'store_name'    => trim($_POST['store_name'] ?? ''),
                'custom_domain' => strtolower(trim($_POST['custom_domain'] ?? '')),
                'owner_email'   => trim($_POST['owner_email'] ?? ''),
                'description'   => trim($_POST['description'] ?? ''),
                'theme_color'   => $_POST['theme_color'] ?? '#4F46E5',
            ]);
            $message = $result ? "Store updated successfully!" : "Failed to update store.";
            $messageType = $result ? 'success' : 'error';
            $action = 'list';
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            $result = $storeModel->delete($id);
            $message = $result ? "Store deleted." : "Failed to delete store.";
            $messageType = $result ? 'success' : 'error';
            $action = 'list';
            break;
    }
}

// Handle GET actions
if ($action === 'verify' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $store = $storeModel->findById($id);
    if ($store) {
        $verification = $storeModel->verifyDomain($store['custom_domain']);
        if ($verification['is_verified']) {
            $storeModel->markVerified($id, true);
            $message = $verification['message'];
            $messageType = 'success';
        } else {
            $storeModel->markVerified($id, false);
            $message = $verification['message'];
            $messageType = 'error';
        }
    }
    $action = 'list';
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $storeModel->toggleActive($id);
    $message = "Store status toggled.";
    $messageType = 'success';
    $action = 'list';
}

// Fetch data for views
$stores = $storeModel->getAll();
$editStore = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editStore = $storeModel->findById((int)$_GET['id']);
    if (!$editStore) {
        $action = 'list';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - StoreHub</title>
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .navbar {
            background: var(--gray-900);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 { font-size: 1.3rem; }
        .navbar h1 span { color: var(--primary); }
        .navbar a { color: var(--gray-300); text-decoration: none; font-size: 0.9rem; }
        .navbar a:hover { color: white; }

        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* ─── Alert Messages ─── */
        .alert {
            padding: 0.9rem 1.2rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-left: 4px solid;
        }
        .alert-success { background: #D1FAE5; border-color: var(--success); color: #065F46; }
        .alert-error { background: #FEE2E2; border-color: var(--danger); color: #991B1B; }

        /* ─── Card ─── */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-header h2 {
            font-size: 1.2rem;
            color: var(--gray-700);
        }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #DC2626; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-warning:hover { background: #D97706; }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.8rem; }
        .btn-outline {
            background: transparent; 
            border: 1px solid var(--gray-300); 
            color: var(--gray-700);
        }
        .btn-outline:hover { background: var(--gray-100); }

        /* ─── Table ─── */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.9rem;
        }

        th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        tr:hover td { background: var(--gray-50); }

        /* ─── Badges ─── */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-warning { background: #FEF3C7; color: #92400E; }

        .color-dot {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            vertical-align: middle;
            border: 1px solid var(--gray-300);
        }

        /* ─── Forms ─── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.15s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input[type="color"] { height: 42px; padding: 4px; cursor: pointer; }

        .form-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .actions { display: flex; gap: 0.3rem; flex-wrap: wrap; }

        /* ─── Stats ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-top: 0.2rem;
        }

        .domain-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .domain-link:hover { text-decoration: underline; }

        /* ─── DNS Info Box ─── */
        .dns-info {
            background: var(--gray-100);
            border: 1px dashed var(--gray-300);
            border-radius: 8px;
            padding: 1rem 1.2rem;
            margin-top: 1.5rem;
            font-size: 0.88rem;
        }
        .dns-info h3 { font-size: 0.95rem; margin-bottom: 0.5rem; color: var(--gray-700); }
        .dns-info code {
            background: var(--gray-200);
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- ─── Navbar ─── -->
<nav class="navbar">
    <h1>🏪 <span>Store</span>Hub Admin</h1>
    <div>
        <a href="admin.php?key=<?= $adminKey ?>">Dashboard</a> &nbsp;|&nbsp;
        <a href="admin.php?key=<?= $adminKey ?>&action=add">+ Add Store</a> &nbsp;|&nbsp;
        <a href="index.php" target="_blank">View Site →</a>
    </div>
</nav>

<div class="container">

    <!-- ─── Alert ─── -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- ─── Stats ─── -->
    <?php if ($action === 'list'): ?>
        <?php
            $totalStores = count($stores);
            $activeStores = count(array_filter($stores, fn($s) => $s['is_active']));
            $verifiedStores = count(array_filter($stores, fn($s) => $s['is_verified']));
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Stores</div>
                <div class="stat-value"><?= $totalStores ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active</div>
                <div class="stat-value" style="color: var(--success)"><?= $activeStores ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Verified Domains</div>
                <div class="stat-value" style="color: var(--primary)"><?= $verifiedStores ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Verification</div>
                <div class="stat-value" style="color: var(--warning)"><?= $totalStores - $verifiedStores ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- LIST VIEW -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <?php if ($action === 'list'): ?>
        <div class="card">
            <div class="card-header">
                <h2>All Stores</h2>
                <a href="admin.php?key=<?= $adminKey ?>&action=add" class="btn btn-primary">+ Add Store</a>
            </div>
            
            <?php if (empty($stores)): ?>
                <p style="color: var(--gray-500); text-align: center; padding: 2rem;">
                    No stores found. <a href="admin.php?key=<?= $adminKey ?>&action=add">Create your first store</a>.
                </p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Store Name</th>
                                <th>Custom Domain</th>
                                <th>Email</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th>Domain</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stores as $s): ?>
                                <tr>
                                    <td><?= $s['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($s['store_name']) ?></strong></td>
                                    <td>
                                        <a href="http://<?= htmlspecialchars($s['custom_domain']) ?>" 
                                           class="domain-link" target="_blank">
                                            <?= htmlspecialchars($s['custom_domain']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($s['owner_email']) ?></td>
                                    <td><span class="color-dot" style="background: <?= htmlspecialchars($s['theme_color']) ?>"></span></td>
                                    <td>
                                        <?php if ($s['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($s['is_verified']): ?>
                                            <span class="badge badge-success">Verified ✓</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="admin.php?key=<?= $adminKey ?>&action=edit&id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                                            <a href="admin.php?key=<?= $adminKey ?>&action=verify&id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">Verify</a>
                                            <a href="admin.php?key=<?= $adminKey ?>&action=toggle&id=<?= $s['id'] ?>" class="btn btn-sm <?= $s['is_active'] ? 'btn-outline' : 'btn-success' ?>">
                                                <?= $s['is_active'] ? 'Disable' : 'Enable' ?>
                                            </a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this store?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="key" value="<?= $adminKey ?>">
                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- DNS Info -->
        <div class="dns-info">
            <h3>📌 How Clients Connect Their Custom Domain</h3>

            <p style="margin-top: 0.8rem; font-weight: 600; color: #374151;">Send these instructions to your client:</p>
            
            <div style="background: white; border: 1px solid #E5E7EB; border-radius: 8px; padding: 1.2rem; margin-top: 0.8rem; line-height: 2;">
                <strong>DNS A Record Setup</strong><br>
                <table style="width: 100%; margin: 0.5rem 0; border-collapse: collapse; font-size: 0.85rem;">
                    <tr style="background: #F3F4F6;">
                        <th style="padding: 0.4rem 0.8rem; text-align: left; border: 1px solid #E5E7EB;">Type</th>
                        <th style="padding: 0.4rem 0.8rem; text-align: left; border: 1px solid #E5E7EB;">Host / Name</th>
                        <th style="padding: 0.4rem 0.8rem; text-align: left; border: 1px solid #E5E7EB;">Value / Points To</th>
                        <th style="padding: 0.4rem 0.8rem; text-align: left; border: 1px solid #E5E7EB;">TTL</th>
                    </tr>
                    <tr>
                        <td style="padding: 0.4rem 0.8rem; border: 1px solid #E5E7EB;"><code>A</code></td>
                        <td style="padding: 0.4rem 0.8rem; border: 1px solid #E5E7EB;"><code>@</code></td>
                        <td style="padding: 0.4rem 0.8rem; border: 1px solid #E5E7EB;"><code><?= SERVER_IP ?></code></td>
                        <td style="padding: 0.4rem 0.8rem; border: 1px solid #E5E7EB;"><code>3600</code></td>
                    </tr>
                </table>
                <small style="color: #6B7280;">Client goes to their domain registrar (GoDaddy, Namecheap, Hostinger, Cloudflare, etc.) → DNS Settings → Add the above A record.</small>
            </div>

            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; padding: 1rem 1.2rem; margin-top: 0.8rem; font-size: 0.85rem; color: #065F46; line-height: 1.8;">
                <strong>✅ Steps for clients:</strong><br>
                1. Log in to domain registrar → DNS settings<br>
                2. Add A record: <code style="background:#D1FAE5;padding:0.1rem 0.4rem;border-radius:3px;">@ → <?= SERVER_IP ?></code><br>
                3. Wait 5–30 minutes for DNS propagation<br>
                4. Click <strong>"Verify"</strong> in admin panel → should turn green
            </div>

            <p style="margin-top: 0.8rem; color: var(--gray-500); font-size: 0.82rem;">
                After the DNS record is set (allow 5–30 min for propagation), click <strong>"Verify"</strong> to confirm the domain is connected.
            </p>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <div class="card">
            <div class="card-header">
                <h2><?= $action === 'edit' ? 'Edit Store' : 'Add New Store' ?></h2>
                <a href="admin.php?key=<?= $adminKey ?>" class="btn btn-outline">← Back</a>
            </div>

            <form method="POST" action="admin.php?key=<?= $adminKey ?>">
                <input type="hidden" name="key" value="<?= $adminKey ?>">
                <input type="hidden" name="action" value="<?= $action === 'edit' ? 'update' : 'create' ?>">
                <?php if ($editStore): ?>
                    <input type="hidden" name="id" value="<?= $editStore['id'] ?>">
                <?php endif; ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="store_name">Store Name *</label>
                        <input type="text" id="store_name" name="store_name" required
                               value="<?= htmlspecialchars($editStore['store_name'] ?? '') ?>"
                               placeholder="e.g., Tech Store">
                    </div>

                    <div class="form-group">
                        <label for="custom_domain">Custom Domain *</label>
                        <input type="text" id="custom_domain" name="custom_domain" required
                               value="<?= htmlspecialchars($editStore['custom_domain'] ?? '') ?>"
                               placeholder="e.g., mystore.com">
                    </div>

                    <div class="form-group">
                        <label for="owner_email">Owner Email *</label>
                        <input type="email" id="owner_email" name="owner_email" required
                               value="<?= htmlspecialchars($editStore['owner_email'] ?? '') ?>"
                               placeholder="e.g., admin@mystore.com">
                    </div>

                    <div class="form-group">
                        <label for="theme_color">Theme Color</label>
                        <input type="color" id="theme_color" name="theme_color"
                               value="<?= htmlspecialchars($editStore['theme_color'] ?? '#4F46E5') ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" 
                                  placeholder="Brief description of the store..."><?= htmlspecialchars($editStore['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= $action === 'edit' ? 'Update Store' : 'Create Store' ?>
                    </button>
                    <a href="admin.php?key=<?= $adminKey ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
