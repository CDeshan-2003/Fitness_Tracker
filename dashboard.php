<?php

require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/db.php';

$userId = (int) $_SESSION['user']['id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity = trim($_POST['activity'] ?? '');
    $duration = filter_var($_POST['duration'] ?? null, FILTER_VALIDATE_INT);
    $calories = filter_var($_POST['calories_burned'] ?? null, FILTER_VALIDATE_INT);

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please submit the form again.';
    }
    if ($activity === '' || strlen($activity) > 100) {
        $errors[] = 'Choose an activity name up to 100 characters.';
    }
    if ($duration === false || $duration < 1 || $duration > 1440) {
        $errors[] = 'Duration must be between 1 and 1,440 minutes.';
    }
    if ($calories === false || $calories < 0 || $calories > 20000) {
        $errors[] = 'Calories must be between 0 and 20,000.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'INSERT INTO workouts (user_id, activity, duration, calories_burned) VALUES (:user_id, :activity, :duration, :calories)'
        );
        $statement->execute([
            'user_id' => $userId,
            'activity' => $activity,
            'duration' => $duration,
            'calories' => $calories,
        ]);
        flash('success', 'Workout added to your activity log.');
        redirect('dashboard.php');
    }
}

$summaryQuery = $pdo->prepare(
    'SELECT COUNT(*) AS workout_count, COALESCE(SUM(duration), 0) AS total_minutes, '
    . 'COALESCE(SUM(calories_burned), 0) AS total_calories FROM workouts WHERE user_id = :user_id'
);
$summaryQuery->execute(['user_id' => $userId]);
$summary = $summaryQuery->fetch();

$workoutsQuery = $pdo->prepare(
    'SELECT activity, duration, calories_burned FROM workouts WHERE user_id = :user_id ORDER BY id DESC LIMIT 10'
);
$workoutsQuery->execute(['user_id' => $userId]);
$workouts = $workoutsQuery->fetchAll();

$activityQuery = $pdo->prepare(
    'SELECT activity, SUM(duration) AS total_minutes FROM workouts WHERE user_id = :user_id '
    . 'GROUP BY activity ORDER BY total_minutes DESC LIMIT 8'
);
$activityQuery->execute(['user_id' => $userId]);
$activities = $activityQuery->fetchAll();
$chartLabels = array_column($activities, 'activity');
$chartValues = array_map('intval', array_column($activities, 'total_minutes'));
$successMessage = flash('success');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Pulse Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="dashboard-page">
<nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
        <a class="navbar-brand brand-mark" href="index.php"><span class="brand-icon"><i class="bi bi-activity"></i></span> pulse<span>.</span></a>
        <div class="d-flex align-items-center gap-3">
            <a class="nav-link d-none d-sm-inline" href="contact.php">Contact</a>
            <span class="user-greeting"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['user']['username']) ?></span>
            <form method="post" action="auth/logout.php" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button class="btn btn-outline-primary btn-sm" type="submit">Log out</button>
            </form>
        </div>
    </div>
</nav>

<main class="container dashboard-main">
    <div class="dashboard-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <p class="eyebrow mb-2">Your training overview</p>
            <h1 class="page-title mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">Track your progress. Improve your performance.</p>
        </div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#workoutModal"><i class="bi bi-plus-lg me-1"></i> Log workout</button>
    </div>

    <?php if ($successMessage): ?><div class="alert alert-success mt-4 mb-0" role="status"><?= e($successMessage) ?></div><?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger mt-4 mb-0" role="alert"><ul class="mb-0 ps-3"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <section class="stats-grid" aria-label="Workout statistics">
        <article class="stat-panel">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-lightning-charge"></i></div>
            <p class="stat-label">Workouts logged</p>
            <p class="stat-value"><?= number_format((int) $summary['workout_count']) ?></p>
            <span class="stat-foot">All-time sessions</span>
        </article>
        <article class="stat-panel">
            <div class="stat-icon stat-icon-teal"><i class="bi bi-clock"></i></div>
            <p class="stat-label">Active minutes</p>
            <p class="stat-value"><?= number_format((int) $summary['total_minutes']) ?></p>
            <span class="stat-foot">Across every activity</span>
        </article>
        <article class="stat-panel">
            <div class="stat-icon stat-icon-coral"><i class="bi bi-fire"></i></div>
            <p class="stat-label">Calories burned</p>
            <p class="stat-value"><?= number_format((int) $summary['total_calories']) ?></p>
            <span class="stat-foot">Self-reported total</span>
        </article>
    </section>

    <section class="row g-4 dashboard-content">
        <div class="col-lg-5">
            <article class="data-panel h-100">
                <div class="panel-heading">
                    <div><p class="eyebrow mb-1">Time by activity</p><h2 class="panel-title">Where you move</h2></div>
                    <span class="panel-mark"><i class="bi bi-pie-chart"></i></span>
                </div>
                <?php if ($activities): ?>
                    <div class="chart-wrap"><canvas id="activityChart" data-labels="<?= e(json_encode($chartLabels)) ?>" data-values="<?= e(json_encode($chartValues)) ?>" aria-label="Workout minutes by activity" role="img"></canvas></div>
                    <p class="chart-caption">Minutes logged for each activity type</p>
                <?php else: ?>
                    <div class="chart-empty"><span class="empty-ring"><i class="bi bi-bar-chart-line"></i></span><p class="mb-1 fw-semibold">Your chart starts here</p><p class="small text-secondary mb-0">Log a workout to see your activity breakdown.</p></div>
                <?php endif; ?>
            </article>
        </div>
        <div class="col-lg-7">
            <article class="data-panel h-100">
                <div class="panel-heading">
                    <div><p class="eyebrow mb-1">Latest sessions</p><h2 class="panel-title">Recent activity</h2></div>
                    <button class="btn btn-link btn-sm panel-action" type="button" data-bs-toggle="modal" data-bs-target="#workoutModal">Add new <i class="bi bi-arrow-up-right ms-1"></i></button>
                </div>
                <?php if ($workouts): ?>
                    <div class="table-responsive">
                        <table class="table activity-table align-middle mb-0">
                            <thead><tr><th scope="col">Activity</th><th scope="col">Duration</th><th scope="col" class="text-end">Calories</th></tr></thead>
                            <tbody>
                                <?php foreach ($workouts as $workout): ?>
                                    <tr>
                                        <td><span class="activity-dot"></span><?= e($workout['activity']) ?></td>
                                        <td><?= number_format((int) $workout['duration']) ?> min</td>
                                        <td class="text-end"><?= number_format((int) $workout['calories_burned']) ?> kcal</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="activity-empty"><i class="bi bi-journal-plus"></i><p class="fw-semibold mb-1">No workouts yet</p><p class="small text-secondary mb-3">Your sessions will appear here after you log them.</p><button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#workoutModal">Log your first workout</button></div>
                <?php endif; ?>
            </article>
        </div>
    </section>
</main>

<div class="modal fade" id="workoutModal" data-auto-open="<?= $errors ? 'true' : 'false' ?>" tabindex="-1" aria-labelledby="workoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="dashboard.php" data-validate novalidate>
                <div class="modal-header">
                    <div><p class="eyebrow mb-1">Add to your history</p><h2 class="modal-title fs-5" id="workoutModalLabel">Log a workout</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="activity">Activity</label>
                        <select class="form-select" id="activity" name="activity" required>
                            <option value="" selected disabled>Select an activity</option>
                            <option>Running</option><option>Walking</option><option>Cycling</option><option>Strength training</option><option>Swimming</option><option>Yoga</option><option>Other</option>
                        </select>
                        <div class="invalid-feedback">Choose an activity.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="duration">Duration (minutes)</label>
                            <input class="form-control" id="duration" name="duration" type="number" min="1" max="1440" step="1" required>
                            <div class="invalid-feedback">Enter 1 to 1,440 minutes.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="calories_burned">Calories burned</label>
                            <input class="form-control" id="calories_burned" name="calories_burned" type="number" min="0" max="20000" step="1" required>
                            <div class="invalid-feedback">Enter 0 to 20,000 calories.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save workout <i class="bi bi-check2 ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="site-footer"><div class="container d-flex justify-content-between flex-wrap gap-2"><span>Pulse Fitness Tracker</span><a href="contact.php">Contact the team</a><span>Move at your own pace.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script src="js/app.js" defer></script>
</body>
</html>
