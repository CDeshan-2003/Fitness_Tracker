<?php

require_once __DIR__ . '/includes/functions.php';

$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Learn about Pulse, a simple place to log workouts and follow your own fitness progress.">
    <title>About Pulse | Fitness Tracker</title>
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
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="about.php" aria-current="page">About</a>
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

<main>
    <section class="about-intro">
        <div class="container about-intro-grid">
            <div>
                <p class="eyebrow">About Pulse</p>
                <h1>About the Application</h1>
            </div>
            <p class="about-intro-copy">Pulse is a simple fitness tracker for logging workouts and seeing your progress. Fitness should fit your life, so the app keeps the focus on your routine, one session at a time.</p>
        </div>
    </section>

    <section class="about-features" id="features">
        <div class="container">
            <div class="about-section-heading">
                <p class="eyebrow">Your activity, at a glance</p>
                <h2>Key Features</h2>
                <p>Practical tools to record workouts and make your activity history easier to understand.</p>
            </div>
            <div class="about-highlights" aria-label="Key features">
                <article class="about-highlight">
                    <span class="about-highlight-number">01</span>
                    <i class="bi bi-journal-plus" aria-hidden="true"></i>
                    <h3>Log your workouts</h3>
                    <p>Save an activity with its duration and calories.</p>
                </article>
                <article class="about-highlight">
                    <span class="about-highlight-number">02</span>
                    <i class="bi bi-speedometer2" aria-hidden="true"></i>
                    <h3>Review your totals</h3>
                    <p>See sessions, active minutes, and calories together.</p>
                </article>
                <article class="about-highlight">
                    <span class="about-highlight-number">03</span>
                    <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
                    <h3>Explore activity patterns</h3>
                    <p>Compare how your time is spread across activities.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="about-mission" id="mission">
        <div class="container">
            <div class="about-section-heading">
                <p class="eyebrow">Why Pulse exists</p>
                <h2>Mission &amp; Vision</h2>
            </div>
            <div class="about-mission-grid">
                <article class="about-mission-item">
                    <span class="about-highlight-number">OUR MISSION</span>
                    <h3>Make consistency easier to see.</h3>
                    <p>Give people a clear, simple place to record movement and recognize the effort they are putting in.</p>
                </article>
                <article class="about-mission-item">
                    <span class="about-highlight-number">OUR VISION</span>
                    <h3>A healthier routine, built at your pace.</h3>
                    <p>Encourage a practical relationship with fitness where progress is personal and small steps matter.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="about-safety" id="safety-privacy">
        <div class="container">
            <div class="about-section-heading">
                <p class="eyebrow">Built with care</p>
                <h2>Safety &amp; Privacy</h2>
                <p>Know what the tracker records and what it is designed to do.</p>
            </div>
            <div class="about-safety-grid">
                <article class="about-safety-item">
                    <span class="about-safety-icon"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                    <div>
                        <h3>Use it as a tracker, not medical advice</h3>
                        <p>Workout and calorie entries are for personal reference. Calorie amounts are estimates, not medical measurements. Follow qualified professional guidance for health concerns.</p>
                    </div>
                </article>
                <article class="about-safety-item">
                    <span class="about-safety-icon"><i class="bi bi-shield-lock" aria-hidden="true"></i></span>
                    <div>
                        <h3>Your account and workout records</h3>
                        <p>Passwords are stored as hashes, and workout queries are scoped to the signed-in account. Workout and contact details are stored in the configured MySQL database, so use secure database credentials and HTTPS when hosting the app online.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="about-cta container">
        <div>
            <p class="eyebrow">Your next session starts whenever you do</p>
            <h2>Ready to find your rhythm?</h2>
        </div>
        <a class="btn btn-primary" href="<?= $user ? 'dashboard.php' : 'auth/register.php' ?>"><?= $user ? 'Open dashboard' : 'Create your account' ?> <i class="bi bi-arrow-up-right ms-1"></i></a>
    </section>
</main>

<footer class="site-footer"><div class="container d-flex justify-content-between flex-wrap gap-2"><span class="brand-mark">pulse<span>.</span></span><div class="d-flex gap-3"><a href="about.php">About</a><a href="contact.php">Contact</a></div><span>Move at your own pace.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="js/app.js" defer></script>
</body>
</html>
