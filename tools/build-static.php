<?php
/**
 * Foggy Dew Farm: static snapshot for GitHub Pages (draft hosting).
 *
 * GitHub Pages can't run PHP, so this renders the public PHP pages into
 * plain HTML using the current published articles:
 *
 *   index.php                -> index.html
 *   news.php                 -> news.html
 *   article.php?slug=<slug>  -> news/<slug>.html
 *
 * and copies the static files (css/, js/, assets/, CREDITS.md). The admin
 * area, data/, backend/ and includes/ are never copied, so drafts and PHP
 * source stay out of the published snapshot.
 *
 * Usage (from the project root):  php tools/build-static.php [output-dir]
 * Output defaults to _site/ (git-ignored). The GitHub Action in
 * .github/workflows/pages.yml runs this on every push to main.
 *
 * The real site (Blacknight, Apache + PHP) doesn't use this at all.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = realpath(__DIR__ . '/..');
$out = $argv[1] ?? $root . '/_site';

require_once $root . '/includes/content.php';

/** Render one PHP page in its own scope, so page variables don't leak between renders. */
function fd_render_page($file, $get = [], $requestUri = '') {
    $_GET = $get;
    $_SERVER['REQUEST_URI'] = $requestUri;
    ob_start();
    include $file;
    return ob_get_clean();
}

/** Point links at the static files instead of the PHP pages. */
function fd_static_links($html) {
    $html = preg_replace('/"article\.php\?slug=([a-z0-9\-]+)"/', '"news/$1.html"', $html);
    return str_replace(['"index.php', '"news.php'], ['"index.html', '"news.html'], $html);
}

function fd_copy_dir($from, $to) {
    if (!is_dir($to)) {
        mkdir($to, 0755, true);
    }
    foreach (scandir($from) as $name) {
        if ($name === '.' || $name === '..' || $name === '.htaccess') {
            continue;
        }
        is_dir("$from/$name") ? fd_copy_dir("$from/$name", "$to/$name") : copy("$from/$name", "$to/$name");
    }
}

function fd_write_page($path, $html) {
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    file_put_contents($path, $html);
    echo '  ' . $path . "\n";
}

// Start from an empty output folder
if (is_dir($out)) {
    exec('rm -rf ' . escapeshellarg($out));
}
mkdir($out, 0755, true);
$out = realpath($out);

echo "Building static site in $out\n";

fd_write_page("$out/index.html", fd_static_links(fd_render_page("$root/index.php")));
fd_write_page("$out/news.html", fd_static_links(fd_render_page("$root/news.php")));

foreach (fd_get_articles(true) as $article) {
    $slug = $article['slug'];
    $html = fd_render_page("$root/article.php", ['slug' => $slug], "news/$slug.html");
    // Article pages live one folder down, so resolve their relative links from the site root
    $html = str_replace('<base href="' . e(fd_base_href()) . '">', '<base href="../">', $html);
    fd_write_page("$out/news/$slug.html", fd_static_links($html));
}

foreach (['css', 'js', 'assets'] as $dir) {
    fd_copy_dir("$root/$dir", "$out/$dir");
}
copy("$root/CREDITS.md", "$out/CREDITS.md");
touch("$out/.nojekyll");

echo "Done.\n";
