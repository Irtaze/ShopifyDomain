<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Store.php';

if (!empty($_SESSION['user_id']) && !empty($_SESSION['tenant_id'])) {
    header('Location: /admin.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_fail();

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    $knowAboutUs = trim((string) ($_POST['know_about_us'] ?? ''));
    $referralName = trim((string) ($_POST['referral_name'] ?? ''));
    $termsAccepted = !empty($_POST['terms_accepted']);

    if ($fullName === '' || $email === '' || $password === '') {
        $error = 'Full name, email, and password are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!$termsAccepted) {
        $error = 'You must accept terms.';
    } else {
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $error = 'Email already exists.';
        } else {
            $userId = $userModel->create([
                'full_name' => $fullName,
                'email' => $email,
                'password' => $password,
                'know_about_us' => $knowAboutUs ?: null,
                'referral_name' => $referralName ?: null,
                'terms_accepted' => $termsAccepted,
            ]);

            $user = $userModel->findById($userId);
            if ($user) {
                $storeModel = new Store();
                $storeModel->upsertForTenant(
                    (string) $user['tenant_id'],
                    $fullName . ' Store',
                    'Welcome to our store!',
                    '#4F46E5'
                );

                $success = 'Account created successfully. Please login.';
            } else {
                $error = 'Failed to create account.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StoreHub</title>
</head>
<body>
    <h1>Create account</h1>
    <?php if ($error): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p><?= e($success) ?></p>
    <?php endif; ?>
    <form method="POST">
        <?= csrf_input() ?>
        <label>Full name</label><br>
        <input type="text" name="full_name" required><br><br>
        <label>Email</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password</label><br>
        <input type="password" name="password" required><br><br>
        <label>Confirm password</label><br>
        <input type="password" name="confirm_password" required><br><br>
        <label>How did you hear about us?</label><br>
        <input type="text" name="know_about_us"><br><br>
        <label>Referral name</label><br>
        <input type="text" name="referral_name"><br><br>
        <label>
            <input type="checkbox" name="terms_accepted" value="1"> I accept terms
        </label><br><br>
        <button type="submit">Register</button>
    </form>
    <p><a href="/login.php">Already have an account? Login</a></p>
</body>
</html>
