<?php
require_once __DIR__ . '/includes/auth.php';
fd_require_admin();

$articles = fd_read_json(FD_ARTICLES_FILE);
$publishedArticles = count(array_filter($articles, fn($a) => !empty($a['published'])));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | Foggy Dew Farm</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <?php include __DIR__ . '/includes/bar.php'; ?>

  <div class="admin-wrap">
    <h1 class="admin-title">Dashboard</h1>

    <div class="admin-card">
      <h2 class="admin-subtitle">Content Overview</h2>
      <p>Articles: <strong><?php echo count($articles); ?></strong> total, <strong><?php echo $publishedArticles; ?></strong> published.</p>
      <div class="admin-actions">
        <a href="article-edit.php" class="admin-btn">+ New Article</a>
        <a href="articles.php" class="admin-btn admin-btn--secondary">Manage Articles</a>
      </div>
    </div>

    <div class="admin-card">
      <h2 class="admin-subtitle">How This Works</h2>
      <p class="admin-intro">
        Articles are stored as a JSON file in <code>/data</code>; there's no database to manage.
        Anything marked <strong>Published</strong> appears immediately on the News page and, if it is one of the three newest, in the homepage's What's New section;
        anything left as a <strong>Draft</strong> stays hidden from visitors until you're ready.
      </p>
    </div>
  </div>
</body>
</html>
