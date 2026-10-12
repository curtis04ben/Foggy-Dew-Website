<?php
/**
 * Foggy Dew Farm: lightweight content store helpers.
 *
 * Articles are stored as a JSON file in /data. This keeps the site simple
 * (no database required) while still letting the /admin area add, edit and
 * publish articles without touching HTML.
 *
 * If the site later outgrows a JSON file, these functions are the only
 * place that would need to change to swap in a database: everything else
 * (public pages, admin forms) calls through here.
 */

require_once __DIR__ . '/../backend/config.php';

/**
 * Read all records from a JSON content file.
 * Returns an array (empty array if the file is missing or invalid).
 */
function fd_read_json($path) {
    if (!file_exists($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Write records back to a JSON content file (pretty-printed).
 * Writes to a temp file then renames, so a half-written file is never read.
 */
function fd_write_json($path, $data) {
    $json = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        return false;
    }
    return rename($tmp, $path);
}

/** Articles, newest first. Drafts are excluded unless $publishedOnly is false. */
function fd_get_articles($publishedOnly = true) {
    $items = fd_read_json(FD_ARTICLES_FILE);
    if ($publishedOnly) {
        $items = array_values(array_filter($items, function ($a) {
            return !empty($a['published']);
        }));
    }
    usort($items, function ($a, $b) {
        return strcmp($b['date'] ?? '', $a['date'] ?? '');
    });
    return $items;
}

function fd_find_by_slug($items, $slug) {
    foreach ($items as $item) {
        if (isset($item['slug']) && $item['slug'] === $slug) {
            return $item;
        }
    }
    return null;
}

function fd_get_article_by_slug($slug, $publishedOnly = true) {
    return fd_find_by_slug(fd_get_articles($publishedOnly), $slug);
}

/** Turn a title into a URL-safe slug, e.g. "Planting Day!" -> "planting-day". */
function fd_slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    if ($text === '') {
        $text = 'article-' . time();
    }
    return $text;
}

/** Ensure a slug is unique within a set of items, appending -2, -3, etc. if needed. */
function fd_unique_slug($items, $slug) {
    $base = $slug;
    $i = 2;
    while (fd_find_by_slug($items, $slug) !== null) {
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}

/**
 * URL path of the site root, e.g. "/" (or "/subfolder/" if the site is
 * installed below the web root). Used as <base href> on article pages,
 * which are also reachable at pretty URLs like /news/slug, so their
 * relative links (css/, assets/, index.php) still resolve from the root.
 */
function fd_base_href() {
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $siteRoot = realpath(__DIR__ . '/..') ?: '';
    $relative = ($docRoot !== '' && strpos($siteRoot, $docRoot) === 0) ? substr($siteRoot, strlen($docRoot)) : '';
    $relative = trim(str_replace('\\', '/', $relative), '/');
    return '/' . ($relative === '' ? '' : $relative . '/');
}

/** Basic HTML-escaping shortcut used throughout the templates. */
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Render a plain-text body (as stored from a <textarea>) as paragraphs of safe HTML. */
function fd_render_body($text) {
    $paragraphs = preg_split('/\r\n\r\n|\n\n/', trim((string) $text));
    $html = '';
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if ($p === '') {
            continue;
        }
        $html .= '<p>' . nl2br(e($p)) . "</p>\n";
    }
    return $html;
}

/**
 * Handle an optional image upload from an admin form.
 * Returns the relative path (e.g. "assets/uploads/xyz.jpg") to store on
 * the record, null if no file was uploaded, or false on error.
 */
function fd_handle_image_upload($fieldName) {
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > FD_MAX_UPLOAD_BYTES) {
        return false;
    }
    // Checked by actual file content, not by the filename
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return false;
    }
    $filename = 'upload-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    if (!is_dir(FD_UPLOADS_DIR)) {
        mkdir(FD_UPLOADS_DIR, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], FD_UPLOADS_DIR . '/' . $filename)) {
        return false;
    }
    return 'assets/uploads/' . $filename;
}

function fd_format_date($dateString) {
    $ts = strtotime((string) $dateString);
    return $ts ? date('F j, Y', $ts) : (string) $dateString;
}

/** Link to an article: the pretty /news/slug form needs .htaccess, so the site itself uses ?slug=. */
function fd_article_url($slug) {
    return 'article.php?slug=' . urlencode($slug);
}
