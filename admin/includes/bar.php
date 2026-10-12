<div class="admin-bar">
  <div>
    <a href="index.php" class="admin-bar__brand">Foggy Dew Admin</a>
    <a href="articles.php">Articles</a>
    <a href="../news.php" target="_blank">View Site</a>
  </div>
  <div>
    <span class="admin-bar__user">Signed in as <?php echo e($_SESSION['fd_admin_username'] ?? ''); ?></span>
    <a href="logout.php">Log Out</a>
  </div>
</div>
