<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    redirect('../dashboard.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please submit the form again.';
    }
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Username must be between 3 and 50 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = 'The passwords do not match.';
    }

    if (!$errors) {
        $statement = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
        $statement->execute([
            'username' => $username,
            'email' => $email,
        ]);

        if ($statement->fetch()) {
            $errors[] = 'That username or email is already registered.';
        } else {
            try {
                $statement = $pdo->prepare(
                    'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
                );
                $statement->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $pdo->lastInsertId(),
                    'username' => $username,
                    'email' => $email,
                ];
                redirect('../dashboard.php');
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $errors[] = 'That username or email is already registered.';
                } else {
                    throw $exception;
                }
            }
        }
    }
}

$errorMessage = flash('error');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account | Pulse Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
<nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
        <a class="navbar-brand brand-mark" href="../index.php"><span class="brand-icon"><i class="bi bi-activity"></i></span> pulse<span>.</span></a>
        <a class="btn btn-outline-primary btn-sm" href="login.php">Log in</a>
    </div>
</nav>
<main class="auth-layout container">
    <section class="auth-panel">
        <p class="eyebrow">A better rhythm, one session at a time</p>
        <h1>Create your account</h1>
        <p class="text-secondary mb-4">Build a clear picture of your activity and keep your momentum going.</p>

        <?php if ($errorMessage): ?>
            <div class="alert alert-warning" role="alert"><?= e($errorMessage) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" data-validate novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input class="form-control" id="username" name="username" type="text" minlength="3" maxlength="50" value="<?= e($username) ?>" autocomplete="username" required>
                <div class="invalid-feedback">Enter a username between 3 and 50 characters.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input class="form-control" id="email" name="email" type="email" maxlength="255" value="<?= e($email) ?>" autocomplete="email" required>
                <div class="invalid-feedback">Enter a valid email address.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                <div class="invalid-feedback">Use at least 8 characters.</div>
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                <div class="invalid-feedback">Confirm your password.</div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Create account <i class="bi bi-arrow-right ms-1"></i></button>
        </form>
        <p class="small text-secondary text-center mt-4 mb-0">Already have an account? <a href="login.php">Log in</a></p>
    </section>
</main>
<script src="../js/app.js" defer></script>
</body>
</html>
