<?php
/**
 * Admissions & Ticket Bookings Register
 */
$pageTitle = "Admissions & Gate Register";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = getDB();

// Handle Admit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_admit'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $bid = (int)$_POST['booking_id'];
        $up = $db->prepare("UPDATE bookings SET gate_status = 'admitted', admitted_at = NOW() WHERE booking_id = ?");
        $up->execute([$bid]);
        logActivity('GATE_ADMIT', "Booking ID #{$bid} admitted at turnstile gate", $currentStaff['user_id']);
        setFlash('success', "Guest admitted successfully.");
        header('Location: ' . ADMIN_URL . '/bookings.php');
        exit;
    }
}

// Search and Filter
$search = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? 'all';

$sql = "SELECT b.*, u.email, v.fname, v.lname, v.phone 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        LEFT JOIN visitors v ON u.user_id = v.user_id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (b.booking_reference LIKE ? OR v.fname LIKE ? OR v.lname LIKE ? OR v.phone LIKE ? OR b.payment_reference LIKE ?)";
    $term = "%{$search}%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}

if ($statusFilter !== 'all') {
    $sql .= " AND b.gate_status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY b.booking_id DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Visitor Admissions & Gate Register</h2>
        <span class="text-muted small">Verify credentials, admit visitors, and inspect digital passes</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo ADMIN_URL; ?>/scanner.php" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-qr-code-scan"></i> Turnstile Gate Scanner
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="<?php echo ADMIN_URL; ?>/bookings.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Search by Ticket ID, Customer Name, Phone, M-Pesa Ref..." value="<?php echo e($search); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Gate Statuses</option>
                <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed (Awaiting Entry)</option>
                <option value="admitted" <?php echo $statusFilter === 'admitted' ? 'selected' : ''; ?>>Admitted (Validated)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-mv-primary w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted text-uppercase">
                <tr>
                    <th class="ps-3">Ticket Reference</th>
                    <th>Guest Details</th>
                    <th>Visit Date & Slot</th>
                    <th>Breakdown</th>
                    <th>Add-Ons</th>
                    <th>Total</th>
                    <th>Payment Ref</th>
                    <th>Gate Status</th>
                    <th class="pe-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No booking records match your query.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): 
                        $bName = trim(($b['fname'] ?? '') . ' ' . ($b['lname'] ?? ''));
                        if (empty($bName)) $bName = 'Registered Visitor';
                        $totCount = (int)$b['adults_qty'] + (int)$b['children_qty'] + (int)$b['non_resident_qty'] + (int)$b['student_qty'];
                    ?>
                    <tr>
                        <td class="ps-3">
                            <span class="font-monospace fw-bold text-dark d-block"><?php echo e($b['booking_reference']); ?></span>
                            <small class="text-muted"><?php echo date('M d, H:i', strtotime($b['created_at'])); ?></small>
                        </td>
                        <td>
                            <strong class="text-dark d-block"><?php echo e($bName); ?></strong>
                            <small class="text-muted"><?php echo e($b['phone'] ?: 'No Phone'); ?> &bull; <?php echo e($b['email']); ?></small>
                        </td>
                        <td>
                            <strong class="text-dark d-block"><?php echo e($b['visit_date']); ?></strong>
                            <small class="text-secondary"><?php echo e($b['session_slot']); ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?php echo $totCount; ?> Pass(es)</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <?php if ($b['has_boat_ride']): ?><span class="badge bg-primary-mv text-white" style="font-size: 0.65rem;">Boat</span><?php endif; ?>
                                <?php if ($b['has_vip_platform']): ?><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">VIP</span><?php endif; ?>
                                <?php if ($b['has_guide']): ?><span class="badge bg-info text-dark" style="font-size: 0.65rem;">Guide</span><?php endif; ?>
                                <?php if (!$b['has_boat_ride'] && !$b['has_vip_platform'] && !$b['has_guide']): ?><span class="text-muted" style="font-size: 0.75rem;">Standard</span><?php endif; ?>
                            </div>
                        </td>
                        <td><strong class="text-success"><?php echo formatKsh($b['total_amount']); ?></strong></td>
                        <td><span class="font-monospace small text-muted"><?php echo e($b['payment_reference']); ?></span></td>
                        <td>
                            <span class="badge <?php echo $b['gate_status'] === 'admitted' ? 'bg-danger' : 'bg-success'; ?>">
                                <?php echo strtoupper(e($b['gate_status'])); ?>
                            </span>
                        </td>
                        <td class="pe-3 text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <?php if ($b['gate_status'] === 'confirmed'): ?>
                                    <form action="<?php echo ADMIN_URL; ?>/bookings.php" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                                        <button type="submit" name="action_admit" class="btn btn-sm btn-success py-1 px-2 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-lg"></i> Admit
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>/ticket-view.php?ref=<?php echo urlencode($b['booking_reference']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
