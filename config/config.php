<?php
/**
 * Application Configuration & Security Settings
 * Mamba Village Nairobi - Operations & Visitor Management System
 */

// Enforce timezone
date_default_timezone_set('Africa/Nairobi');

// Secure Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Environment & Base URL Detection
if (php_sapi_name() === 'cli') {
    define('BASE_URL', '/mamba-village');
    define('ADMIN_URL', '/mamba-village/admin');
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Find directory path relative to DOCUMENT_ROOT
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $currentDir = str_replace('\\', '/', dirname(__DIR__));
    $relPath = str_replace($docRoot, '', $currentDir);
    $relPath = '/' . trim($relPath, '/');
    if ($relPath === '/') $relPath = '';

    define('BASE_URL', $protocol . $host . $relPath);
    define('ADMIN_URL', BASE_URL . '/admin');
}

// Site Identity
define('SITE_NAME', 'Mamba Village Nairobi');
define('SITE_TAGLINE', 'East Africa\'s Largest Crocodile Sanctuary & Eco-Park');
define('CONTACT_PHONE', '+254 722 000 000');
define('CONTACT_EMAIL', 'info@mambavillagenairobi.co.ke');
define('PHYSICAL_ADDRESS', 'Langata North Road, Karen, Nairobi, Kenya');
define('OPERATING_HOURS', 'Daily: 9:00 AM – 6:00 PM');

// Security CSRF Token Generator
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfToken() {
    return $_SESSION['csrf_token'] ?? '';
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
