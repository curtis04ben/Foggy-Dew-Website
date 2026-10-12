<?php
require_once __DIR__ . '/includes/auth.php';
fd_require_admin();

$flash = '';
if (isset($_GET['saved'])) $flash = 'Article saved.';
if (isset($_GET['deleted'])) $flash = 'Article deleted.';

// Handle delete (POST, CSRF-protected)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_slug'])) {
    if (!fd_csrf_check()) {
        die('Invalid request. Please go back and try again.');
    }
    $items = fd_read_json(FD_ARTICLES_FILE);
    $items = array_values(array_filter($items, fn($a) => $a['slug'] !== $_POST['delete_slug']));
    fd_write_json(FD_ARTICLES_FILE, $items);
    header('Location: articles.php?deleted=1');
    exit;
}

$articles = fd_get_articles(false);
$token = fd_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Articles | Admin | Foggy Dew Farm</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <?php include __DIR__ . '/includes/bar.php'; ?>

  <div class="admin-wrap">
    <h1 class="admin-title">Articles</h1>

    <?php if ($flash): ?><div class="admin-flash admin-flash--success"><?php echo e($flash); ?></div><?php endif; ?>

    <div class="admin-card">
      <div class="admin-actions admin-actions--top">
        <a href="article-edit.php" class="admin-btn">+ New Article</a>
      </div>

      <?php if (empty($articles)): ?>
        <p>No articles yet.</p>
      <?php else: ?>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr><th>Title</th><th>Date</th><th>Category</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($articles as $a): ?>
                <tr>
                  <td>
                    <?php if (!empty($a['published'])): ?>
                      <a href="../<?php echo e(fd_article_url($a['slug'])); ?>" target="_blank"><?php echo e($a['title']); ?></a>
                    <?php else: ?>
                      <?php echo e($a['title']); ?>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($a['date'] ?? ''); ?></td>
                  <td><?php echo e($a['category'] ?? ''); ?></td>
                  <td>
                    <?php if (!empty($a['published'])): ?>
                      <span class="admin-badge admin-badge--published">Published</span>
                    <?php else: ?>
                      <span class="admin-badge admin-badge--draft">Draft</span>
                    <?php endif; ?>
                  </td>
                  <td class="admin-table__actions">
                    <a href="article-edit.php?slug=<?php echo urlencode($a['slug']); ?>" class="admin-btn admin-btn--secondary">Edit</a>
                    <form method="post" onsubmit="return confirm('Delete this article? This cannot be undone.');">
                      <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                      <input type="hidden" name="delete_slug" value="<?php echo e($a['slug']); ?>">
                      <button type="submit" class="admin-btn admin-btn--danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
