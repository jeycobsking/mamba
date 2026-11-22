<?php
/**
 * Visitor Booking History & Digital Ticket Passes
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$currentUser = getCurrentUser();

$db = getDB();
$stmt = $db->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_id DESC");
$stmt->execute([$currentUser['user_id']]);
$bookings = $stmt->fetchAll();

$pageTitle = "My E-Tickets & Passes";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">My Digital Passes & Reservations</h3>
            <p class="text-muted small mb-0">Manage your active park admissions, tickets, and payment receipts.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-mv-primary mt-3 mt-sm-0">
            <i class="bi bi-plus-circle me-1"></i> Book New Passes
        </a>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
            <div class="card-body">
                <i class="bi bi-ticket-perforated fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">No Active Passes Found</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 450px;">
                    You have not made any ticket reservations yet. Plan your weekend safari tour and book passes online with instant M-Pesa confirmation.
                </p>
                <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-mv-gold fw-bold px-4">
                    Book Tickets Now
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-secondary">
                        <tr>
                            <th class="ps-4">Ticket Reference</th>
                            <th>Visit Date & Slot</th>
                            <th>Passes Count</th>
                            <th>Experience Add-ons</th>
                            <th>Amount</th>
                            <th>Payment Ref</th>
                            <th>Gate Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): 
                            $totalCount = (int)$b['adults_qty'] + (int)$b['children_qty'] + (int)$b['non_resident_qty'] + (int)$b['student_qty'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-dark"><?php echo e($b['booking_reference']); ?></span>
                                <small class="text-muted d-block"><?php echo date('M d, Y', strtotime($b['created_at'])); ?></small>
                            </td>
                            <td>
                                <strong class="text-dark d-block"><?php echo e($b['visit_date']); ?></strong>
                                <small class="text-secondary"><?php echo e($b['session_slot']); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person me-1"></i> <?php echo $totalCount; ?> Person(s)
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if ($b['has_boat_ride']): ?>
                                        <span class="badge bg-primary-mv text-white small">Boat Ride</span>
                                    <?php endif; ?>
                                    <?php if ($b['has_vip_platform']): ?>
                                        <span class="badge bg-warning text-dark small">VIP Bridge</span>
                                    <?php endif; ?>
                                    <?php if ($b['has_guide']): ?>
                                        <span class="badge bg-info text-dark small">Private Guide</span>
                                    <?php endif; ?>
                                    <?php if (!$b['has_boat_ride'] && !$b['has_vip_platform'] && !$b['has_guide']): ?>
                                        <span class="text-muted small">Standard Entry</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong class="text-success"><?php echo formatKsh($b['total_amount']); ?></strong>
                            </td>
                            <td>
                                <span class="font-monospace small text-muted"><?php echo e($b['payment_reference']); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo $b['gate_status'] === 'admitted' ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo strtoupper(e($b['gate_status'])); ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="<?php echo BASE_URL; ?>/ticket-view.php?ref=<?php echo urlencode($b['booking_reference']); ?>" class="btn btn-sm btn-outline-success fw-semibold">
                                    <i class="bi bi-qr-code me-1"></i> View Ticket
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
