<?php
/**
 * Dedicated Staff & Administrator Authentication Guard
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

// Enforce staff login immediately
requireStaffRole();

$currentStaff = getCurrentUser();
$staffRole = $currentStaff['role'] ?? 'admin';
$staffName = $currentStaff['name'] ?? 'Staff Officer';
$staffEmail = $currentStaff['email'] ?? '';
