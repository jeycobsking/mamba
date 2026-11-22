<?php
/**
 * Public User Logout Handler
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

logoutUser();
setFlash('info', 'You have been safely signed out. Visit again soon!');
header('Location: ' . BASE_URL . '/index.php');
exit;
