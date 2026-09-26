<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    redirect('../dashboard.php');
}

$error = null;
$email = '';
$errorMessage = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $statement = $pdo->prepare('SELECT id, username, email, password FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Email or password is incorrect.';
        } else {
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $update = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
                $update->execute([
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'id' => $user['id'],
                ]);
            }

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
            ];
            redirect('../dashboard.php');
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | Pulse Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
<nav class="navbar site-nav">
    <div class="container">
        <a class="navbar-brand brand-mark" href="../index.php"><span class="brand-icon"><i class="bi bi-activity"></i></span> pulse<span>.</span></a>
        <a class="btn btn-outline-primary btn-sm" href="register.php">Create account</a>
    </div>
</nav>
<main class="auth-layout container">
    <section class="auth-panel">
        <p class="eyebrow">Welcome back</p>
        <h1>Log in to Pulse</h1>
        <p class="text-secondary mb-4">Your activity history is right where you left it.</p>

        <?php if ($errorMessage): ?><div class="alert alert-warning" role="alert"><?= e($errorMessage) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>

        <form method="post" action="login.php" data-validate novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input class="form-control" id="email" name="email" type="email" maxlength="255" value="<?= e($email) ?>" autocomplete="email" required>
                <div class="invalid-feedback">Enter a valid email address.</div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                <div class="invalid-feedback">Enter your password.</div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Log in <i class="bi bi-arrow-right ms-1"></i></button>
        </form>
        <p class="small text-secondary text-center mt-4 mb-0">New to Pulse? <a href="register.php">Create an account</a></p>
    </section>
</main>
<script src="../js/app.js" defer></script>
</body>
</html>
