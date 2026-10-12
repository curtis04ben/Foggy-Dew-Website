<?php
require_once __DIR__ . '/includes/auth.php';

if (!fd_admin_configured()) {
    header('Location: setup.php');
    exit;
}

if (fd_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$justCreated = isset($_GET['created']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lightweight brute-force throttle: a growing delay after each failed
    // attempt (session-based, resets on success).
    $attempts = $_SESSION['fd_login_attempts'] ?? 0;
    if ($attempts > 0) {
        usleep((int) min($attempts, 5) * 700000); // up to ~3.5s
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $creds = fd_admin_credentials();

    if ($creds && $username === $creds['username'] && password_verify($password, $creds['password_hash'])) {
        // Prevent session fixation
        session_regenerate_id(true);
        $_SESSION['fd_admin_logged_in'] = true;
        $_SESSION['fd_admin_username'] = $username;
        unset($_SESSION['fd_login_attempts']);
        header('Location: index.php');
        exit;
    }
    $_SESSION['fd_login_attempts'] = $attempts + 1;
    $error = 'Incorrect username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | Foggy Dew Farm</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="admin-login-wrap">
    <div class="admin-card">
      <h1 class="admin-title">Admin Login</h1>

      <?php if ($justCreated): ?>
        <div class="admin-flash admin-flash--success">Admin account created. Please sign in.</div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="admin-flash admin-flash--error"><?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="post" class="admin-form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <div class="admin-actions">
          <button type="submit" class="admin-btn">Sign In</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
