<?php
/**
 * Foggy Dew Farm: backend configuration.
 *
 * Site-wide constants only, no secrets. Anything server-specific can go in
 * an optional, git-ignored backend/config.local.php. /backend is blocked
 * from web access by backend/.htaccess.
 *
 * All persistent-data paths are defined in ONE place (this file):
 *   - FD_DATA_DIR     (data/)           -> articles.json, admin-credentials.json
 *   - FD_UPLOADS_DIR  (assets/uploads/) -> images uploaded via /admin
 * Everything else is ordinary application code and can be replaced at any
 * time without losing content, as long as these two folders are kept.
 */

if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

define('FD_SITE_NAME', 'Foggy Dew Farm');

// --- Persistent data locations -------------------------------------------
define('FD_DATA_DIR', __DIR__ . '/../data');
define('FD_ARTICLES_FILE', FD_DATA_DIR . '/articles.json');
// Admin credentials live alongside the content so backing up data/ backs
// up everything the admin area can create or change.
define('FD_CREDENTIALS_FILE', FD_DATA_DIR . '/admin-credentials.json');
define('FD_UPLOADS_DIR', __DIR__ . '/../assets/uploads');

// Maximum uploaded image size (bytes) accepted by the admin area: 5MB
define('FD_MAX_UPLOAD_BYTES', 5 * 1024 * 1024);

// Fallback image for articles saved without one
define('FD_DEFAULT_ARTICLE_IMAGE', 'assets/images/foggy-field.jpg');

// Homepage sections an article can point readers to ("Related section").
define('FD_RELATED_SECTIONS', [
    'index.php#lavender'    => 'Lavender & Natural Products',
    'index.php#crafts'      => 'Willow & Handmade Crafts',
    'index.php#experiences' => 'Workshops & Experiences',
    'index.php#camping'     => 'Camping & the Trout Stream',
    'index.php#contact'     => 'Contact & Enquiries',
]);
