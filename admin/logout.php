<?php
/**
 * Dedicated Staff Logout Handler
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

logoutUser();
setFlash('info', 'Staff session terminated securely.');
header('Location: ' . ADMIN_URL . '/login.php');
exit;

