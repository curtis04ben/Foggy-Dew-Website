<?php
require_once __DIR__ . '/../../backend/config.php';
require_once __DIR__ . '/../../includes/content.php';

// Opened as /admin (no trailing slash): redirect to /admin/ first, otherwise
// the relative redirects and links below (setup.php, login.php, admin.css)
// resolve against the site root and 404.
$fdAdminDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$fdRequestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($fdAdminDir !== '' && $fdRequestPath === $fdAdminDir) {
    $query = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: ' . $fdAdminDir . '/' . ($query !== '' ? '?' . $query : ''), true, 301);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function fd_admin_configured() {
    return file_exists(FD_CREDENTIALS_FILE);
}

function fd_admin_credentials() {
    if (!fd_admin_configured()) {
        return null;
    }
    $data = json_decode((string) file_get_contents(FD_CREDENTIALS_FILE), true);
    if (!is_array($data) || empty($data['username']) || empty($data['password_hash'])) {
        return null;
    }
    return $data;
}

function fd_admin_save_credentials($username, $password) {
    $data = [
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ];
    return file_put_contents(FD_CREDENTIALS_FILE, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX) !== false;
}

function fd_admin_logged_in() {
    return !empty($_SESSION['fd_admin_logged_in']);
}

/**
 * Call at the top of every protected admin page.
 * Sends the visitor to setup (if no admin account exists yet) or to
 * login (if one exists but they haven't signed in), and stops execution.
 */
function fd_require_admin() {
    if (!fd_admin_configured()) {
        header('Location: setup.php');
        exit;
    }
    if (!fd_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/** Simple CSRF token helper for the admin forms. */
function fd_csrf_token() {
    if (empty($_SESSION['fd_csrf'])) {
        $_SESSION['fd_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['fd_csrf'];
}

function fd_csrf_check() {
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && !empty($_SESSION['fd_csrf']) && hash_equals($_SESSION['fd_csrf'], $token);
}
