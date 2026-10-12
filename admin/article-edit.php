<?php
require_once __DIR__ . '/includes/auth.php';
fd_require_admin();

$editingSlug = $_GET['slug'] ?? ($_POST['original_slug'] ?? '');
$articles = fd_read_json(FD_ARTICLES_FILE);
$existing = $editingSlug ? fd_find_by_slug($articles, $editingSlug) : null;
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

$error = '';

if ($isPost) {
    if (!fd_csrf_check()) {
        $error = 'Your session expired. Please try saving again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $relatedSection = $_POST['related_section'] ?? '';

        if ($title === '' || $date === '') {
            $error = 'Title and date are required.';
        } else {
            $upload = fd_handle_image_upload('featured_image_file');
            if ($upload === false) {
                $error = 'Image upload failed. Please use a JPG, PNG, WEBP or GIF under 5MB.';
            } else {
                // The slug is fixed once an article exists, so shared links keep working
                $slug = $existing ? $existing['slug'] : fd_unique_slug($articles, fd_slugify($title));

                $record = [
                    'slug' => $slug,
                    'title' => $title,
                    'date' => $date,
                    'author' => trim($_POST['author'] ?? ''),
                    'category' => trim($_POST['category'] ?? ''),
                    'featured_image' => $upload ?: ($existing['featured_image'] ?? FD_DEFAULT_ARTICLE_IMAGE),
                    'excerpt' => trim($_POST['excerpt'] ?? ''),
                    'body' => trim($_POST['body'] ?? ''),
                    'related_section' => isset(FD_RELATED_SECTIONS[$relatedSection]) ? $relatedSection : '',
                    'published' => isset($_POST['published']),
                ];

                if ($existing) {
                    foreach ($articles as $i => $a) {
                        if ($a['slug'] === $existing['slug']) {
                            $articles[$i] = $record;
                        }
                    }
                } else {
                    $articles[] = $record;
                }

                if (fd_write_json(FD_ARTICLES_FILE, $articles)) {
                    header('Location: articles.php?saved=1');
                    exit;
                }
                $error = 'Could not save. Check that /data is writable by the web server.';
            }
        }
    }
}

// After a failed save, show what was typed rather than the stored version
$source = $isPost ? $_POST : ($existing ?? []);
$title = $source['title'] ?? '';
$date = $source['date'] ?? date('Y-m-d');
$author = $source['author'] ?? '';
$category = $source['category'] ?? '';
$excerpt = $source['excerpt'] ?? '';
$body = $source['body'] ?? '';
$relatedSection = $source['related_section'] ?? '';
$published = $isPost ? isset($_POST['published']) : !empty($existing['published']);
$featuredImage = $existing['featured_image'] ?? '';
$token = fd_csrf_token();
$pageHeading = $existing ? 'Edit Article' : 'New Article';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageHeading); ?> | Admin | Foggy Dew Farm</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <?php include __DIR__ . '/includes/bar.php'; ?>

  <div class="admin-wrap">
    <h1 class="admin-title"><?php echo e($pageHeading); ?></h1>

    <?php if ($error): ?><div class="admin-flash admin-flash--error"><?php echo e($error); ?></div><?php endif; ?>

    <div class="admin-card">
      <form method="post" enctype="multipart/form-data" class="admin-form" action="article-edit.php">
        <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
        <input type="hidden" name="original_slug" value="<?php echo e($existing['slug'] ?? ''); ?>">

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo e($title); ?>" required>

        <div class="admin-form__row">
          <div>
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo e($date); ?>" required>
          </div>
          <div>
            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?php echo e($author); ?>">
          </div>
          <div>
            <label for="category">Category</label>
            <input type="text" id="category" name="category" value="<?php echo e($category); ?>" placeholder="e.g. Lavender, Willow, Workshops">
          </div>
        </div>

        <label for="featured_image_file">Featured image</label>
        <?php if ($featuredImage): ?>
          <img src="../<?php echo e($featuredImage); ?>" class="admin-current-image" alt="Current featured image">
          <p class="admin-hint">Current image shown above. Upload a new file to replace it, or leave blank to keep it.</p>
        <?php endif; ?>
        <input type="file" id="featured_image_file" name="featured_image_file" accept="image/jpeg,image/png,image/webp,image/gif">
        <p class="admin-hint">JPG, PNG, WEBP or GIF, up to 5MB. Around 1200px wide works well.</p>

        <label for="excerpt">Excerpt (short summary shown on the News page and the homepage)</label>
        <textarea id="excerpt" name="excerpt" rows="3"><?php echo e($excerpt); ?></textarea>

        <label for="body">Full article body</label>
        <textarea id="body" name="body" rows="16"><?php echo e($body); ?></textarea>
        <p class="admin-hint">Leave a blank line between paragraphs.</p>

        <label for="related_section">Related section on the homepage (optional)</label>
        <select id="related_section" name="related_section">
          <option value="">None</option>
          <?php foreach (FD_RELATED_SECTIONS as $href => $label): ?>
            <option value="<?php echo e($href); ?>" <?php echo $relatedSection === $href ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>

        <label class="admin-check">
          <input type="checkbox" name="published" <?php echo $published ? 'checked' : ''; ?>>
          Published (visible on the live site)
        </label>

        <div class="admin-actions">
          <button type="submit" class="admin-btn">Save Article</button>
          <a href="articles.php" class="admin-btn admin-btn--secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
