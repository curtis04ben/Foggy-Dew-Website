<?php
// One article card, used by news.php and the homepage's What's New section.
// Expects $article; $cardHeading sets the title's heading level (default h2).
$cardHeading = $cardHeading ?? 'h2';
?>
<li class="article-card reveal">
  <a class="article-card__link" href="<?php echo e(fd_article_url($article['slug'])); ?>">
    <figure class="media article-card__media" data-label="<?php echo e($article['title']); ?>">
      <img src="<?php echo e($article['featured_image'] ?: FD_DEFAULT_ARTICLE_IMAGE); ?>" alt="" loading="lazy">
    </figure>
    <div class="article-card__body">
      <p class="article-meta">
        <?php if (!empty($article['category'])): ?><span><?php echo e($article['category']); ?></span><?php endif; ?>
        <time datetime="<?php echo e($article['date'] ?? ''); ?>"><?php echo e(fd_format_date($article['date'] ?? '')); ?></time>
      </p>
      <<?php echo $cardHeading; ?>><?php echo e($article['title']); ?></<?php echo $cardHeading; ?>>
      <?php if (!empty($article['excerpt'])): ?><p><?php echo e($article['excerpt']); ?></p><?php endif; ?>
      <span class="text-link">Read more</span>
    </div>
  </a>
</li>
