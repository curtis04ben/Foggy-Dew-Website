<?php
// Site header for the PHP pages (articles). Mirrors the header in index.php,
// but links back to the homepage sections and starts with a solid background
// since these pages have no full-screen hero behind the header.
include __DIR__ . '/sprite.php';
?>
<a class="skip-link" href="<?php echo e($skipHref ?? '#main'); ?>">Skip to content</a>

<header class="site-header site-header--solid" id="top">
  <div class="container header-inner">
    <a class="brand" href="index.php" aria-label="Foggy Dew Farm home">
      <svg class="brand-mark" aria-hidden="true"><use href="#knot"/></svg>
      <span class="brand-text">
        <span class="brand-name">Foggy Dew Farm</span>
        <span class="brand-sub">Whitesville · New York</span>
      </span>
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
      <span class="nav-toggle-bars" aria-hidden="true"></span>
      <span class="sr-only">Menu</span>
    </button>

    <nav class="site-nav" id="site-nav" aria-label="Main">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#farm">The Farm</a></li>
        <li><a href="index.php#lavender">Lavender</a></li>
        <li><a href="index.php#crafts">Crafts</a></li>
        <li><a href="index.php#experiences">Experiences</a></li>
        <li><a href="news.php" aria-current="page">News</a></li>
        <li><a href="index.php#contact" class="nav-cta">Contact</a></li>
      </ul>
    </nav>
  </div>
</header>
