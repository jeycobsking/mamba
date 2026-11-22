<?php
/**
 * Authentication & Access Control Subsystem
 * Enforces Role-Based Access Control (RBAC) and Session Hardening
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Check if any user (visitor or staff) is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get active authenticated user details
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'user_id' => $_SESSION['user_id'],
        'email'   => $_SESSION['user_email'] ?? '',
        'role'    => $_SESSION['user_role'] ?? 'visitor',
        'name'    => $_SESSION['user_name'] ?? 'User',
        'phone'   => $_SESSION['user_phone'] ?? ''
    ];
}

/**
 * Check if current user is staff
 */
function isStaff(): bool {
    if (!isLoggedIn()) return false;
    $role = $_SESSION['user_role'] ?? '';
    return in_array($role, ['admin', 'finance', 'event_manager', 'guide', 'supplier', 'service_manager']);
}

/**
 * Enforce customer/visitor login before accessing sensitive service routes
 */
function requireLogin(?string $redirectUrl = null): void {
    if (!isLoggedIn()) {
        $target = $redirectUrl ?? ($_SERVER['REQUEST_URI'] ?? '/');
        setFlash('warning', 'Please log in or create an account to access our booking and visitor services.');
        header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode($target));
        exit;
    }
}

/**
 * Enforce staff authorization for administrative routes
 */
function requireStaffRole($roles = []): void {
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    // If not logged in at all, redirect to admin login
    if (!isLoggedIn()) {
        setFlash('warning', 'Staff credentials required to access the administration portal.');
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        header('Location: ' . ADMIN_URL . '/login.php?redirect=' . urlencode($uri));
        exit;
    }

    // If logged in as customer/visitor, block access
    if (!isStaff()) {
        setFlash('danger', 'Access denied. You do not hold staff privileges.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    // Role-specific enforcement if specified
    if (!empty($roles) && !in_array($_SESSION['user_role'], $roles) && $_SESSION['user_role'] !== 'admin') {
        setFlash('danger', 'Unauthorized access level for your staff designation.');
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

/**
 * Process Login Authentication
 */
function loginUser(string $email, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id, email, password, role, is_active FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email address or password.'];
    }

    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'Your account has been deactivated. Contact administration.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email address or password.'];
    }

    // Session Fixation Hardening
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['user_id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Retrieve name depending on role
    $fullName = 'User';
    $phone = '';

    if ($user['role'] === 'visitor') {
        $vStmt = $db->prepare("SELECT fname, lname, phone FROM visitors WHERE user_id = ? LIMIT 1");
        $vStmt->execute([$user['user_id']]);
        if ($vis = $vStmt->fetch()) {
            $fullName = trim($vis['fname'] . ' ' . $vis['lname']);
            $phone = $vis['phone'];
        }
    } else {
        // Staff lookup
        $fullName = ucfirst(str_replace('_', ' ', $user['role'])) . ' Officer';
        if ($user['role'] === 'admin') {
            $aStmt = $db->prepare("SELECT fname, lname FROM administrators WHERE user_id = ? LIMIT 1");
            $aStmt->execute([$user['user_id']]);
            if ($adm = $aStmt->fetch()) {
                $fullName = trim($adm['fname'] . ' ' . $adm['lname']);
            }
        }
    }

    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_phone'] = $phone;

    // Update last login
    $upStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    $upStmt->execute([$user['user_id']]);

    logActivity('LOGIN', "User {$user['email']} authenticated with role {$user['role']}", $user['user_id']);

    return ['success' => true, 'role' => $user['role']];
}

/**
 * Terminate Session
 */
function logoutUser(): void {
    if (isLoggedIn()) {
        logActivity('LOGOUT', "User logged out", $_SESSION['user_id']);
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
