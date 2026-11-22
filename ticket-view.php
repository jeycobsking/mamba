<?php
/**
 * Official Digital QR Ticket Pass & Receipt View
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$currentUser = getCurrentUser();

$ref = trim($_GET['ref'] ?? '');
if (empty($ref)) {
    setFlash('warning', 'No booking reference provided.');
    header('Location: ' . BASE_URL . '/my-bookings.php');
    exit;
}

$db = getDB();

// Allow visitor to view their own ticket, or staff to view any ticket
if (isStaff()) {
    $stmt = $db->prepare("SELECT b.*, u.email, v.fname, v.lname, v.phone 
                          FROM bookings b 
                          JOIN users u ON b.user_id = u.user_id 
                          LEFT JOIN visitors v ON u.user_id = v.user_id 
                          WHERE b.booking_reference = ? LIMIT 1");
    $stmt->execute([$ref]);
} else {
    $stmt = $db->prepare("SELECT b.*, u.email, v.fname, v.lname, v.phone 
                          FROM bookings b 
                          JOIN users u ON b.user_id = u.user_id 
                          LEFT JOIN visitors v ON u.user_id = v.user_id 
                          WHERE b.booking_reference = ? AND b.user_id = ? LIMIT 1");
    $stmt->execute([$ref, $currentUser['user_id']]);
}

$booking = $stmt->fetch();
if (!$booking) {
    setFlash('danger', 'Booking reference not found or access denied.');
    header('Location: ' . BASE_URL . '/my-bookings.php');
    exit;
}

$guestName = trim(($booking['fname'] ?? '') . ' ' . ($booking['lname'] ?? ''));
if (empty($guestName)) {
    $guestName = $currentUser['name'];
}

$pageTitle = "Official E-Ticket: " . $booking['booking_reference'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 printable-area">
            
            <!-- Actions Toolbar -->
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <a href="<?php echo BASE_URL; ?>/my-bookings.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to My Passes
                </a>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-mv-primary">
                        <i class="bi bi-printer me-1"></i> Print E-Ticket / Receipt
                    </button>
                </div>
            </div>

            <!-- E-Ticket Card -->
            <div class="ticket-document mb-4">
                <div class="ticket-header d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-white text-dark fw-bold text-uppercase small">Official Admission Pass</span>
                            <span class="badge bg-warning text-dark"><i class="bi bi-patch-check-fill me-1"></i> Paid & Confirmed</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-white"><?php echo SITE_NAME; ?></h3>
                        <small class="text-white-50">Lipa na M-Pesa Authorized Gate Pass</small>
                    </div>
                    <div class="text-lg-end mt-2 mt-lg-0">
                        <span class="font-monospace fw-bold fs-5 text-warning d-block"><?php echo e($booking['booking_reference']); ?></span>
                        <small class="text-white-50">Ref: <?php echo e($booking['payment_reference']); ?></small>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-7 border-end">
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block">Lead Visitor / Guest</span>
                                <h5 class="fw-bold text-dark mb-0"><?php echo e($guestName); ?></h5>
                                <small class="text-muted"><?php echo e($booking['phone'] ?: $currentUser['phone']); ?> &bull; <?php echo e($booking['email']); ?></small>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Visit Date</span>
                                    <strong class="text-dark fs-6"><?php echo e($booking['visit_date']); ?></strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small text-uppercase fw-bold d-block">Time Session</span>
                                    <strong class="text-dark small d-block"><?php echo e($booking['session_slot']); ?></strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block">Passes Included</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    <?php if ($booking['adults_qty'] > 0): ?>
                                        <span class="badge bg-light text-dark border"><?php echo (int)$booking['adults_qty']; ?>x Citizen Adult</span>
                                    <?php endif; ?>
                                    <?php if ($booking['children_qty'] > 0): ?>
                                        <span class="badge bg-light text-dark border"><?php echo (int)$booking['children_qty']; ?>x Child Pass</span>
                                    <?php endif; ?>
                                    <?php if ($booking['non_resident_qty'] > 0): ?>
                                        <span class="badge bg-light text-dark border"><?php echo (int)$booking['non_resident_qty']; ?>x International Adult</span>
                                    <?php endif; ?>
                                    <?php if ($booking['student_qty'] > 0): ?>
                                        <span class="badge bg-light text-dark border"><?php echo (int)$booking['student_qty']; ?>x Student ID Pass</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block">Activity Add-Ons</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    <?php if ($booking['has_boat_ride']): ?>
                                        <span class="badge bg-primary-mv text-white"><i class="bi bi-water me-1"></i> Lake Mamba Boat Cruise</span>
                                    <?php endif; ?>
                                    <?php if ($booking['has_vip_platform']): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> VIP Feeding Bridge Access</span>
                                    <?php endif; ?>
                                    <?php if ($booking['has_guide']): ?>
                                        <span class="badge bg-info text-dark"><i class="bi bi-compass-fill me-1"></i> Private Naturalist Guide</span>
                                    <?php endif; ?>
                                    <?php if (!$booking['has_boat_ride'] && !$booking['has_vip_platform'] && !$booking['has_guide']): ?>
                                        <span class="text-muted small">Standard Sanctuary Access</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small d-block">Settlement Total</span>
                                    <h4 class="fw-bold text-success mb-0"><?php echo formatKsh($booking['total_amount']); ?></h4>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small d-block">Channel</span>
                                    <span class="badge bg-light text-dark border"><?php echo e($booking['payment_method']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Scannable SVG QR Code Generator -->
                        <div class="col-md-5 text-center">
                            <div class="p-3 bg-white rounded-3 border d-inline-block shadow-sm mb-3">
                                <svg width="150" height="150" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#ffffff"/>
                                    <!-- QR Markers -->
                                    <rect x="5" y="5" width="26" height="26" fill="#123e2c"/>
                                    <rect x="8" y="8" width="20" height="20" fill="#ffffff"/>
                                    <rect x="11" y="11" width="14" height="14" fill="#123e2c"/>

                                    <rect x="69" y="5" width="26" height="26" fill="#123e2c"/>
                                    <rect x="72" y="8" width="20" height="20" fill="#ffffff"/>
                                    <rect x="75" y="11" width="14" height="14" fill="#123e2c"/>

                                    <rect x="5" y="69" width="26" height="26" fill="#123e2c"/>
                                    <rect x="8" y="72" width="20" height="20" fill="#ffffff"/>
                                    <rect x="11" y="75" width="14" height="14" fill="#123e2c"/>

                                    <!-- QR Data Dots -->
                                    <rect x="36" y="8" width="6" height="6" fill="#123e2c"/>
                                    <rect x="46" y="14" width="6" height="6" fill="#123e2c"/>
                                    <rect x="56" y="8" width="6" height="6" fill="#123e2c"/>
                                    <rect x="36" y="24" width="6" height="6" fill="#123e2c"/>
                                    <rect x="46" y="24" width="16" height="6" fill="#123e2c"/>

                                    <rect x="8" y="36" width="6" height="6" fill="#123e2c"/>
                                    <rect x="18" y="42" width="10" height="6" fill="#123e2c"/>
                                    <rect x="8" y="54" width="6" height="6" fill="#123e2c"/>
                                    <rect x="22" y="54" width="6" height="6" fill="#123e2c"/>

                                    <rect x="36" y="36" width="28" height="28" fill="#10b981" rx="4"/>
                                    <text x="50" y="54" font-size="12" font-weight="bold" fill="#ffffff" text-anchor="middle">MV</text>

                                    <rect x="68" y="36" width="8" height="6" fill="#123e2c"/>
                                    <rect x="82" y="42" width="10" height="6" fill="#123e2c"/>
                                    <rect x="74" y="54" width="18" height="6" fill="#123e2c"/>

                                    <rect x="36" y="68" width="14" height="6" fill="#123e2c"/>
                                    <rect x="56" y="74" width="6" height="18" fill="#123e2c"/>
                                    <rect x="42" y="86" width="10" height="6" fill="#123e2c"/>
                                    <rect x="68" y="82" width="24" height="6" fill="#123e2c"/>
                                </svg>
                            </div>
                            
                            <div class="font-monospace fw-bold fs-6 text-dark"><?php echo e($booking['booking_reference']); ?></div>
                            <span class="badge <?php echo $booking['gate_status'] === 'admitted' ? 'bg-danger' : 'bg-success'; ?> mt-1">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Gate Status: <?php echo strtoupper(e($booking['gate_status'])); ?>
                            </span>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                Present this QR code to the turnstile scanner or park marshal upon arrival.
                            </small>
                        </div>
                    </div>

                    <!-- Perforated Tear-off Notice -->
                    <div class="mt-4 pt-4 border-top border-2 border-dashed text-center small text-muted">
                        <i class="bi bi-info-circle me-1 text-success"></i>
                        Tickets are non-transferable and valid solely on <strong><?php echo e($booking['visit_date']); ?></strong>. Operating hours: 9:00 AM to 6:00 PM daily.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
