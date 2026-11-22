<?php
/**
 * Executive Operations Dashboard
 * Mamba Village Nairobi - Staff Administration
 */
$pageTitle = "Operations Dashboard";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = getDB();

// 1. KPI Queries
$today = date('Y-m-d');

// Visitors Today
$visStmt = $db->prepare("SELECT SUM(adults_qty + children_qty + non_resident_qty + student_qty) as total_vis, SUM(total_amount) as total_rev FROM bookings WHERE visit_date = ? AND payment_status = 'paid'");
$visStmt->execute([$today]);
$todayStats = $visStmt->fetch();
$visitorsToday = $todayStats['total_vis'] ?? 0;
$revenueToday = $todayStats['total_rev'] ?? 0;

// Total all-time revenue
$allRevStmt = $db->query("SELECT SUM(total_amount) as all_rev FROM bookings WHERE payment_status = 'paid'");
$allRev = $allRevStmt->fetch()['all_rev'] ?? 0;

// Active Guides
$guideCountStmt = $db->query("SELECT COUNT(*) as guides_active FROM tour_guides WHERE status = 'on_tour'");
$activeGuides = $guideCountStmt->fetch()['guides_active'] ?? 0;

// Pending Group Quotes
$quotesStmt = $db->query("SELECT COUNT(*) as pending_quotes FROM group_inquiries WHERE status = 'pending'");
$pendingQuotes = $quotesStmt->fetch()['pending_quotes'] ?? 0;

// 2. Today's Events
$events = getEventsSchedule();

// 3. Recent Bookings
$recStmt = $db->query("SELECT b.*, u.email, v.fname, v.lname, v.phone 
                       FROM bookings b 
                       JOIN users u ON b.user_id = u.user_id 
                       LEFT JOIN visitors v ON u.user_id = v.user_id 
                       ORDER BY b.booking_id DESC LIMIT 6");
$recentBookings = $recStmt->fetchAll();

// 4. Quick Admit Action from Dashboard
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admit_booking_id'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $bid = (int)$_POST['admit_booking_id'];
        $up = $db->prepare("UPDATE bookings SET gate_status = 'admitted', admitted_at = NOW() WHERE booking_id = ?");
        $up->execute([$bid]);
        logActivity('GATE_ADMIT', "Booking ID #{$bid} admitted at turnstile gate", $currentStaff['user_id']);
        setFlash('success', "Guest admitted successfully.");
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}
?>

<!-- Dashboard Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Park Operations Control Center</h2>
        <span class="text-muted small">Real-time telemetry, visitor admissions, and revenue ledger</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="<?php echo ADMIN_URL; ?>/scanner.php" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-qr-code-scan"></i> Scan Turnstile Pass
        </a>
        <a href="<?php echo ADMIN_URL; ?>/finances.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-download"></i> Finance Statement
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-stat-card">
            <span class="text-muted small text-uppercase fw-bold d-block">Today's Visitors</span>
            <div class="d-flex align-items-baseline gap-2 my-1">
                <span class="fs-2 fw-bold text-dark"><?php echo number_format($visitorsToday); ?></span>
                <span class="text-success small fw-semibold"><i class="bi bi-arrow-up-right"></i> Live</span>
            </div>
            <small class="text-muted">Admissions for <?php echo date('M d, Y'); ?></small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-stat-card">
            <span class="text-muted small text-uppercase fw-bold d-block">Gross Revenue (Today)</span>
            <div class="d-flex align-items-baseline gap-2 my-1">
                <span class="fs-2 fw-bold text-success"><?php echo formatKsh($revenueToday); ?></span>
            </div>
            <small class="text-muted">100% Settled via M-Pesa / Card</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-stat-card">
            <span class="text-muted small text-uppercase fw-bold d-block">Guides Dispatched</span>
            <div class="d-flex align-items-baseline gap-2 my-1">
                <span class="fs-2 fw-bold text-primary"><?php echo $activeGuides; ?></span>
                <span class="text-muted small">Active on tours</span>
            </div>
            <small class="text-muted">Certified wildlife guides</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-stat-card">
            <span class="text-muted small text-uppercase fw-bold d-block">Pending Group Quotes</span>
            <div class="d-flex align-items-baseline gap-2 my-1">
                <span class="fs-2 fw-bold text-warning"><?php echo $pendingQuotes; ?></span>
                <span class="text-warning small fw-semibold">Action needed</span>
            </div>
            <small class="text-muted">Schools & corporate retreats</small>
        </div>
    </div>
</div>

<!-- Two-Column Operational Row -->
<div class="row g-4 mb-4">
    
    <!-- Feeding & Event Queue -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-calendar3 text-success me-2"></i>Today's Feeding Shows & Demonstrations</h5>
                <a href="<?php echo ADMIN_URL; ?>/events.php" class="small text-primary fw-semibold text-decoration-none">Manage Schedule</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Time</th>
                            <th>Demonstration</th>
                            <th>Curator</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($events, 0, 5) as $ev): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-dark"><?php echo e($ev['event_time']); ?></td>
                            <td>
                                <strong class="text-dark d-block"><?php echo e($ev['title']); ?></strong>
                                <small class="text-muted"><?php echo e($ev['location']); ?></small>
                            </td>
                            <td><?php echo e($ev['keeper']); ?></td>
                            <td>
                                <span class="badge <?php echo $ev['is_highlight'] ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                    <?php echo $ev['is_highlight'] ? 'Highlight' : 'Scheduled'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Gate Admissions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-ticket-detailed text-warning me-2"></i>Recent Ticket Purchases & Gate Status</h5>
                <a href="<?php echo ADMIN_URL; ?>/bookings.php" class="small text-primary fw-semibold text-decoration-none">Full Register</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>Code</th>
                            <th>Visitor</th>
                            <th>Amount</th>
                            <th>Gate Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $rb): 
                            $rbName = trim(($rb['fname'] ?? '') . ' ' . ($rb['lname'] ?? ''));
                            if (empty($rbName)) $rbName = 'Registered Guest';
                        ?>
                        <tr>
                            <td class="font-monospace fw-bold text-dark"><?php echo e($rb['booking_reference']); ?></td>
                            <td>
                                <strong class="text-dark d-block"><?php echo e($rbName); ?></strong>
                                <small class="text-muted"><?php echo e($rb['visit_date']); ?></small>
                            </td>
                            <td><strong class="text-success"><?php echo formatKsh($rb['total_amount']); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $rb['gate_status'] === 'admitted' ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo strtoupper(e($rb['gate_status'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($rb['gate_status'] === 'confirmed'): ?>
                                    <form action="<?php echo ADMIN_URL; ?>/index.php" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                                        <input type="hidden" name="admit_booking_id" value="<?php echo $rb['booking_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                                            Admit
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Admitted</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
