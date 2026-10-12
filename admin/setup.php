<?php
require_once __DIR__ . '/includes/auth.php';

// Setup can only run once. If an account already exists, send people to login.
if (fd_admin_configured()) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter a username and password.';
    } elseif (strlen($password) < 8) {
        $error = 'Please choose a password of at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (fd_admin_save_credentials($username, $password)) {
        header('Location: login.php?created=1');
        exit;
    } else {
        $error = 'Could not save admin account. Check that the data/ folder is writable by the web server.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Set Up Admin Account | Foggy Dew Farm</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="admin-login-wrap">
    <div class="admin-card">
      <h1 class="admin-title">Set Up Admin Account</h1>
      <p class="admin-intro">This runs once, the first time the site is deployed. Choose a username and password for writing and publishing articles.</p>

      <?php if ($error): ?>
        <div class="admin-flash admin-flash--error"><?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="post" class="admin-form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">
        <p class="admin-hint">At least 8 characters.</p>

        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">

        <div class="admin-actions">
          <button type="submit" class="admin-btn">Create Admin Account</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
