<?php
/**
 * Global Utility & Security Functions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize strings for safe HTML display
 */
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency in Kenyan Shillings
 */
function formatKsh($amount): string {
    return 'KES ' . number_format((float)$amount, 2);
}

/**
 * Generate unique official booking reference
 */
function generateBookingReference(): string {
    return 'MV-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

/**
 * Generate simulated Safaricom M-Pesa receipt code
 */
function generateMpesaReference(): string {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = 'SJL';
    for ($i = 0; $i < 7; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

/**
 * Flash message helpers
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function displayFlash(): string {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $icon = match($flash['type']) {
            'success' => 'bi-check-circle-fill',
            'danger' => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-circle-fill',
            default => 'bi-info-circle-fill'
        };
        return '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi ' . $icon . ' me-2 fs-5"></i>
                    <div>' . e($flash['message']) . '</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    return '';
}

/**
 * Fetch all active attractions
 */
function getAttractions(?int $limit = null): array {
    $db = getDB();
    $sql = "SELECT * FROM attractions WHERE status = 1 ORDER BY is_featured DESC, attraction_id ASC";
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Fetch all active ticket types
 */
function getTicketTypes(): array {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM ticket_types WHERE is_active = 1 ORDER BY ticket_type_id ASC");
    return $stmt->fetchAll();
}

/**
 * Fetch upcoming feeding & event schedule
 */
function getEventsSchedule(?string $category = null): array {
    $db = getDB();
    if ($category && $category !== 'all') {
        $stmt = $db->prepare("SELECT * FROM events WHERE status = 'scheduled' AND category = ? ORDER BY event_time ASC");
        $stmt->execute([$category]);
    } else {
        $stmt = $db->query("SELECT * FROM events WHERE status = 'scheduled' ORDER BY event_time ASC");
    }
    return $stmt->fetchAll();
}

/**
 * System Activity Logger
 */
function logActivity(string $action, string $details = '', ?int $userId = null): void {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt->execute([$userId, $action, $details, $ip]);
    } catch (Exception $e) {
        // Silently continue if table not yet created
    }
}
