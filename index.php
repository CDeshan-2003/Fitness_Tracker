<?php

require_once __DIR__ . '/includes/functions.php';

$user = $_SESSION['user'] ?? null;
$errorMessage = flash('error');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A clear, simple place to track your workouts and celebrate steady progress.">
    <title>Pulse Fitness Tracker</title>
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="siteNav">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <a class="nav-link" href="about.php">About</a>
                <a class="nav-link" href="contact.php">Contact</a>
                <?php if ($user): ?>
                    <a class="btn btn-primary btn-sm" href="dashboard.php">My dashboard <i class="bi bi-arrow-right ms-1"></i></a>
                <?php else: ?>
                    <a class="nav-link" href="auth/login.php">Log in</a>
                    <a class="btn btn-primary btn-sm" href="auth/register.php">Get started <i class="bi bi-arrow-right ms-1"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if ($errorMessage): ?><div class="container pt-3"><div class="alert alert-warning mb-0" role="alert"><?= e($errorMessage) ?></div></div><?php endif; ?>

<main>
    <section class="hero-section">
        <img class="hero-image" src="images/fitness-hero.jpg" alt="Runner training outdoors on a road">
        <div class="hero-shade"></div>
        <div class="container hero-content">
            <p class="hero-kicker"><span class="hero-kicker-dot"></span> YOUR MOVEMENT, MADE MEANINGFUL</p>
            <h1><?= $user ? 'Good to see you, ' . e($user['username']) . '.' : 'Progress is built<br>one workout at a time.' ?></h1>
            <p class="hero-copy">Keep your training simple. Log the work, see your progress, and build a routine that feels like yours.</p>
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <a class="btn btn-light hero-button" href="<?= $user ? 'dashboard.php' : 'auth/register.php' ?>"><?= $user ? 'Open dashboard' : 'Start tracking' ?> <i class="bi bi-arrow-up-right ms-2"></i></a>
                <a class="hero-text-link" href="#how-it-works">See how it works <i class="bi bi-arrow-down ms-1"></i></a>
            </div>
        </div>
        <div class="hero-index" aria-hidden="true">01 <span></span> 03</div>
    </section>

    <section class="intro-band" id="how-it-works">
        <div class="container intro-grid">
            <div>
                <p class="eyebrow">Training with perspective</p>
                <h2>Make consistency<br>easy to see.</h2>
            </div>
            <p class="intro-copy">Pulse gives your workouts a home. Record an activity in seconds, keep an eye on time and calories, and use your own history to stay motivated.</p>
            <a class="round-link" href="<?= $user ? 'dashboard.php' : 'auth/register.php' ?>" aria-label="Get started"><i class="bi bi-arrow-up-right"></i></a>
        </div>
    </section>

    <section class="feature-band">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <article class="feature-item">
                        <span class="feature-number">01</span>
                        <i class="bi bi-journal-check feature-icon"></i>
                        <h3>Log the session</h3>
                        <p>Add the activity, duration, and calories. Your history stays organized in one place.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-item">
                        <span class="feature-number">02</span>
                        <i class="bi bi-bar-chart-line feature-icon"></i>
                        <h3>See your effort</h3>
                        <p>Track total workouts, active minutes, and calories alongside a live activity chart.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-item">
                        <span class="feature-number">03</span>
                        <i class="bi bi-arrow-repeat feature-icon"></i>
                        <h3>Keep your rhythm</h3>
                        <p>Use a clear view of what you have done to shape what comes next.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>
<footer class="site-footer"><div class="container d-flex justify-content-between flex-wrap gap-2"><span class="brand-mark">pulse<span>.</span></span><div class="d-flex gap-3"><a href="about.php">About</a><a href="contact.php">Contact the team</a></div><span>Move at your own pace.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="js/app.js" defer></script>
</body>
</html>
