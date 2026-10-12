<?php
require_once __DIR__ . '/includes/content.php';
$articles = fd_get_articles(true);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>News | Foggy Dew Farm</title>
  <meta name="description" content="News and notes from Foggy Dew Farm in Whitesville, New York: the lavender as it grows, willow and basketry, workshops and life on the farm.">
  <meta name="theme-color" content="#1f3326">
  <meta property="og:title" content="News | Foggy Dew Farm">
  <meta property="og:description" content="News and notes from Foggy Dew Farm in Whitesville, New York.">
  <meta property="og:type" content="website">
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
    <header class="page-intro">
      <div class="container">
        <p class="eyebrow eyebrow--light">From the Farm</p>
        <h1 class="section-title section-title--light">News</h1>
        <div class="divider divider--light" aria-hidden="true"><svg><use href="#knot"/></svg></div>
        <p class="lead lead--light">News and notes from Foggy Dew: the lavender as it grows, willow and the old crafts, and the seasons on the farm.</p>
      </div>
    </header>

    <section class="section section--articles" aria-label="All news">
      <div class="container">
        <?php if (empty($articles)): ?>
          <div class="articles-empty">
            <svg class="more-sprig" aria-hidden="true"><use href="#sprig"/></svg>
            <p>News is on its way. In the meantime, <a href="index.php#contact">get in touch</a> to hear about the farm and the 2028 harvest.</p>
          </div>
        <?php else: ?>
          <ul class="article-grid" role="list">
            <?php foreach ($articles as $article): ?>
              <?php include __DIR__ . '/includes/partials/article-card.php'; ?>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/partials/footer.php'; ?>
</body>
</html>
