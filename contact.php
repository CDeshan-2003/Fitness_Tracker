<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$name = is_logged_in() ? ($_SESSION['user']['username'] ?? '') : '';
$email = is_logged_in() ? ($_SESSION['user']['email'] ?? '') : '';
$message = '';
$successMessage = flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please submit the form again.';
    }
    if (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = 'Name must be between 2 and 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($message) < 10 || strlen($message) > 5000) {
        $errors[] = 'Message must be between 10 and 5,000 characters.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)'
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ]);
        flash('success', 'Thanks for reaching out. Your message has been received.');
        redirect('contact.php');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact | Pulse Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
        <a class="navbar-brand brand-mark" href="index.php"><span class="brand-icon"><i class="bi bi-activity"></i></span> pulse<span>.</span></a>
        <div class="d-flex align-items-center gap-2">
            <a class="nav-link px-2" href="index.php">Home</a>
            <a class="nav-link px-2" href="about.php">About</a>
            <?php if (is_logged_in()): ?>
                <a class="btn btn-primary btn-sm" href="dashboard.php">Dashboard</a>
            <?php else: ?>
                <a class="btn btn-outline-primary btn-sm" href="auth/login.php">Log in</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container content-page">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <p class="eyebrow">We are here to help</p>
            <h1 class="page-title">Get in touch</h1>
            <p class="text-secondary mb-4">Send the Pulse team a note and we will get back to you.</p>

            <?php if ($successMessage): ?><div class="alert alert-success" role="status"><?= e($successMessage) ?></div><?php endif; ?>
            <?php if ($errors): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="contact.php" data-validate novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input class="form-control" id="name" name="name" type="text" minlength="2" maxlength="100" value="<?= e($name) ?>" autocomplete="name" required>
                    <div class="invalid-feedback">Enter your name.</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" maxlength="255" value="<?= e($email) ?>" autocomplete="email" required>
                    <div class="invalid-feedback">Enter a valid email address.</div>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="6" minlength="10" maxlength="5000" data-count-target="message-count" required><?= e($message) ?></textarea>
                    <div class="invalid-feedback">Write a message of at least 10 characters.</div>
                    <div class="d-flex justify-content-end mt-1">
                        <small class="text-secondary"><span id="message-count">0</span>/5000</small>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Send message <i class="bi bi-send ms-1"></i></button>
            </form>
        </div>
    </div>
</main>
<footer class="site-footer"><div class="container d-flex justify-content-between flex-wrap gap-2"><span>Pulse Fitness Tracker</span><div class="d-flex gap-3"><a href="about.php">About</a><a href="contact.php">Contact</a></div><span>Move at your own pace.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="js/app.js" defer></script>
</body>
</html>
