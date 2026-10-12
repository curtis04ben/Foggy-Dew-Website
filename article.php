<?php
require_once __DIR__ . '/includes/content.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['slug'])) : '';
$article = $slug ? fd_get_article_by_slug($slug, true) : null;
if (!$article) {
    http_response_code(404);
}

$pageTitle = $article ? $article['title'] . ' | Foggy Dew Farm' : 'Article Not Found | Foggy Dew Farm';
$pageDescription = $article ? ($article['excerpt'] ?? '') : 'This article could not be found.';
$relatedSections = FD_RELATED_SECTIONS;
$related = $article['related_section'] ?? '';
// On the pretty URL (/news/slug) a bare "#main" would resolve against <base href>
$skipHref = ($_SERVER['REQUEST_URI'] ?? '') . '#main';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <!-- Keeps relative links working when this page is opened at its pretty
       URL (/news/slug). See fd_base_href(). -->
  <base href="<?php echo e(fd_base_href()); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageTitle); ?></title>
  <meta name="description" content="<?php echo e($pageDescription); ?>">
  <meta name="theme-color" content="#1f3326">
  <meta property="og:title" content="<?php echo e($article['title'] ?? 'Article Not Found'); ?>">
  <meta property="og:description" content="<?php echo e($pageDescription); ?>">
  <meta property="og:type" content="article">
  <?php if ($article): ?>
    <meta property="og:image" content="<?php echo e($article['featured_image'] ?: FD_DEFAULT_ARTICLE_IMAGE); ?>">
    <meta property="article:published_time" content="<?php echo e($article['date'] ?? ''); ?>">
  <?php endif; ?>
  <meta name="robots" content="noindex">
  <script>document.documentElement.classList.add("js");</script>
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Source+Sans+3:wght@400;600&family=Uncial+Antiqua&display=swap">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <?php include __DIR__ . '/includes/partials/header.php'; ?>

  <main id="main">
    <?php if (!$article): ?>
      <header class="page-intro">
        <div class="container">
          <h1 class="section-title section-title--light">Article Not Found</h1>
          <p class="lead lead--light">This article may have been unpublished, or the link may be incorrect.</p>
        </div>
      </header>
      <div class="container article-nav">
        <a class="text-link text-link--back" href="news.php">Back to all news</a>
      </div>
    <?php else: ?>
      <article>
        <header class="page-intro page-intro--article">
          <div class="container">
            <p class="article-meta article-meta--light">
              <?php if (!empty($article['category'])): ?><span><?php echo e($article['category']); ?></span><?php endif; ?>
              <time datetime="<?php echo e($article['date'] ?? ''); ?>"><?php echo e(fd_format_date($article['date'] ?? '')); ?></time>
              <?php if (!empty($article['author'])): ?><span>By <?php echo e($article['author']); ?></span><?php endif; ?>
            </p>
            <h1 class="section-title section-title--light"><?php echo e($article['title']); ?></h1>
            <?php if (!empty($article['excerpt'])): ?>
              <p class="lead lead--light"><?php echo e($article['excerpt']); ?></p>
            <?php endif; ?>
          </div>
        </header>

        <?php if (!empty($article['featured_image'])): ?>
          <figure class="media article-hero" data-label="<?php echo e($article['title']); ?>">
            <img src="<?php echo e($article['featured_image']); ?>" alt="">
          </figure>
        <?php endif; ?>

        <div class="article-body">
          <?php echo fd_render_body($article['body'] ?? ''); ?>
        </div>

        <div class="divider" aria-hidden="true"><svg><use href="#knot"/></svg></div>

        <div class="container article-nav">
          <a class="text-link text-link--back" href="news.php">Back to all news</a>
          <?php if (isset($relatedSections[$related])): ?>
            <a class="btn btn--outline" href="<?php echo e($related); ?>"><?php echo e($relatedSections[$related]); ?></a>
          <?php endif; ?>
        </div>
      </article>
    <?php endif; ?>
  </main>

  <?php include __DIR__ . '/includes/partials/footer.php'; ?>
</body>
</html>
