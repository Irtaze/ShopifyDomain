<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/models/User.php';

if (!empty($_SESSION['user_id']) && !empty($_SESSION['tenant_id'])) {
    header('Location: /admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_fail();

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Invalid email or password.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['tenant_id'] = (string) $user['tenant_id'];
        header('Location: /admin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StoreHub</title>
</head>
<body>
    <h1>Login</h1>
    <?php if ($error): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <?= csrf_input() ?>
        <label>Email</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p><a href="/register.php">Create account</a></p>
</body>
</html>
