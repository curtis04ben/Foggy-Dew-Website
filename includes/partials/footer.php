<!-- Site footer for the PHP pages. Mirrors the footer in index.php. -->
<footer class="site-footer">
  <div class="knot-border" aria-hidden="true"></div>
  <div class="container footer-grid">
    <div class="footer-brand">
      <a class="brand brand--footer" href="index.php" aria-label="Foggy Dew Farm home">
        <svg class="brand-mark" aria-hidden="true"><use href="#knot"/></svg>
        <span class="brand-text">
          <span class="brand-name">Foggy Dew Farm</span>
          <span class="brand-sub">Lavender · Willow · Old Crafts</span>
        </span>
      </a>
      <p class="footer-blurb">A small farm in Whitesville, New York, growing lavender for a first harvest in 2028 and keeping traditional crafts alive.</p>
    </div>

    <nav class="footer-nav" aria-label="Footer">
      <h2 class="footer-heading">Explore</h2>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#farm">The Farm</a></li>
        <li><a href="index.php#lavender">Lavender</a></li>
        <li><a href="index.php#crafts">Crafts</a></li>
        <li><a href="index.php#experiences">Experiences</a></li>
        <li><a href="index.php#camping">Camping</a></li>
        <li><a href="news.php">News</a></li>
        <li><a href="index.php#contact">Contact</a></li>
      </ul>
    </nav>

    <div class="footer-contact">
      <h2 class="footer-heading">Find Us</h2>
      <p>Whitesville, New York</p>
      <p class="placeholder-note placeholder-note--light">[Address, email &amp; phone to be added]</p>
    </div>
  </div>
  <div class="container footer-base">
    <p>&copy; <?php echo date('Y'); ?> Foggy Dew Farm. Draft prototype.</p>
    <p>Placeholder photography used under Creative Commons. See the <a href="CREDITS.md">photo credits</a>.</p>
  </div>
</footer>

<script src="js/main.js" defer></script>
