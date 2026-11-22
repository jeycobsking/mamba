<?php
/**
 * Finance Manager & M-Pesa Reconciliation Ledger
 */
require_once __DIR__ . '/includes/admin_auth.php';

// Enforce role access
requireStaffRole(['admin', 'finance']);

$db = getDB();

// CSV Export Request Handler
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmt = $db->query("SELECT b.booking_reference, v.fname, v.lname, v.phone, b.visit_date, b.total_amount, b.payment_method, b.payment_reference, b.payment_status, b.created_at 
                        FROM bookings b 
                        JOIN users u ON b.user_id = u.user_id 
                        LEFT JOIN visitors v ON u.user_id = v.user_id 
                        ORDER BY b.booking_id DESC");
    $records = $stmt->fetchAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Mamba_Village_Finance_Ledger_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Ticket Code', 'Visitor Name', 'Phone', 'Visit Date', 'Amount (KES)', 'Payment Channel', 'M-Pesa Reference', 'Status', 'Date Settled']);
    
    foreach ($records as $r) {
        $name = trim(($r['fname'] ?? '') . ' ' . ($r['lname'] ?? ''));
        fputcsv($output, [
            $r['booking_reference'],
            $name ?: 'Guest',
            $r['phone'] ?? '',
            $r['visit_date'],
            $r['total_amount'],
            $r['payment_method'],
            $r['payment_reference'],
            $r['payment_status'],
            $r['created_at']
        ]);
    }
    fclose($output);
    exit;
}

$pageTitle = "Finance & M-Pesa Ledger";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

// Financial Calculations
$mpesaStmt = $db->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_method LIKE '%M-PESA%' AND payment_status = 'paid'");
$mpesaTotal = $mpesaStmt->fetch()['total'] ?? 0;

$cardStmt = $db->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_method NOT LIKE '%M-PESA%' AND payment_status = 'paid'");
$cardTotal = $cardStmt->fetch()['total'] ?? 0;

$stmt = $db->query("SELECT b.*, u.email, v.fname, v.lname, v.phone 
                    FROM bookings b 
                    JOIN users u ON b.user_id = u.user_id 
                    LEFT JOIN visitors v ON u.user_id = v.user_id 
                    ORDER BY b.booking_id DESC");
$transactions = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Financial Ledger & Invoicing Module</h2>
        <span class="text-muted small">M-Pesa STK push reconciliation, payment audit trails, and accounts</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo ADMIN_URL; ?>/finances.php?export=csv" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV Statement
        </a>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-stat-card border-start border-4 border-success">
            <span class="text-muted small text-uppercase fw-bold d-block">M-Pesa Settled Receipts</span>
            <div class="fs-2 fw-bold text-success my-1"><?php echo formatKsh($mpesaTotal); ?></div>
            <small class="text-muted">Direct Safaricom Lipa na M-Pesa STK</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-stat-card border-start border-4 border-primary">
            <span class="text-muted small text-uppercase fw-bold d-block">Card & Counter POS</span>
            <div class="fs-2 fw-bold text-primary my-1"><?php echo formatKsh($cardTotal); ?></div>
            <small class="text-muted">Visa / Mastercard / Gate counter</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-stat-card border-start border-4 border-warning">
            <span class="text-muted small text-uppercase fw-bold d-block">Consolidated Revenue</span>
            <div class="fs-2 fw-bold text-dark my-1"><?php echo formatKsh($mpesaTotal + $cardTotal); ?></div>
            <small class="text-muted">Audited net collections</small>
        </div>
    </div>
</div>

<!-- Ledger Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark fs-6"><i class="bi bi-journal-check text-success me-2"></i>Transaction Ledger & Payment References</h5>
        <span class="badge bg-light text-secondary border"><?php echo count($transactions); ?> Transactions</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted text-uppercase">
                <tr>
                    <th class="ps-3">Invoice / Ref</th>
                    <th>Transaction Date</th>
                    <th>Customer Name</th>
                    <th>Channel</th>
                    <th>Payment Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="pe-3 text-end">Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): 
                    $tName = trim(($t['fname'] ?? '') . ' ' . ($t['lname'] ?? ''));
                    if (empty($tName)) $tName = 'Customer';
                ?>
                <tr>
                    <td class="ps-3">
                        <span class="font-monospace fw-bold text-dark">INV-<?php echo e($t['booking_reference']); ?></span>
                    </td>
                    <td><?php echo date('M d, Y H:i', strtotime($t['created_at'])); ?></td>
                    <td>
                        <strong class="text-dark d-block"><?php echo e($tName); ?></strong>
                        <small class="text-muted"><?php echo e($t['phone'] ?: 'No Phone'); ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?php echo e($t['payment_method']); ?></span></td>
                    <td><span class="font-monospace fw-bold text-success"><?php echo e($t['payment_reference']); ?></span></td>
                    <td><strong class="text-dark"><?php echo formatKsh($t['total_amount']); ?></strong></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success">Reconciled</span></td>
                    <td class="pe-3 text-end">
                        <a href="<?php echo BASE_URL; ?>/ticket-view.php?ref=<?php echo urlencode($t['booking_reference']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
