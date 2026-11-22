<?php
/**
 * Turnstile Gate Barcode & QR Code Scanner
 */
$pageTitle = "Gate Turnstile Scanner";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';

$db = getDB();
$scanResult = null;
$searchedCode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $searchedCode = trim($_POST['ticket_code'] ?? '');

        if (!empty($searchedCode)) {
            $stmt = $db->prepare("SELECT b.*, u.email, v.fname, v.lname, v.phone 
                                  FROM bookings b 
                                  JOIN users u ON b.user_id = u.user_id 
                                  LEFT JOIN visitors v ON u.user_id = v.user_id 
                                  WHERE b.booking_reference = ? LIMIT 1");
            $stmt->execute([$searchedCode]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                $scanResult = [
                    'status' => 'invalid',
                    'message' => "INVALID TICKET CODE: \"{$searchedCode}\". No matching record found in sanctuary database."
                ];
            } elseif ($ticket['gate_status'] === 'admitted') {
                $scanResult = [
                    'status' => 'already_used',
                    'ticket' => $ticket,
                    'message' => "ALREADY REDEEMED: Ticket \"{$searchedCode}\" was previously validated on {$ticket['admitted_at']}."
                ];
            } else {
                // Admit guest
                $up = $db->prepare("UPDATE bookings SET gate_status = 'admitted', admitted_at = NOW() WHERE booking_id = ?");
                $up->execute([$ticket['booking_id']]);
                
                logActivity('SCANNER_ADMIT', "Ticket {$searchedCode} validated at scanner", $currentStaff['user_id']);
                
                $ticket['gate_status'] = 'admitted';
                $scanResult = [
                    'status' => 'success',
                    'ticket' => $ticket,
                    'message' => "ACCESS GRANTED! Ticket \"{$searchedCode}\" validated. Turnstile gate unlatched."
                ];
            }
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Turnstile Gate QR & Barcode Scanner</h2>
        <span class="text-muted small">Instant electronic credential verification for visitors at the entrance</span>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <!-- Scanner Optical Viewfinder Simulation -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-body p-4 p-md-5 text-center">
                
                <div class="scanner-viewfinder mx-auto mb-4 position-relative" style="width: 260px; height: 260px; background: #092318; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <!-- Laser Line -->
                    <div style="position: absolute; left: 0; right: 0; height: 3px; background: #ef4444; box-shadow: 0 0 12px #ef4444; animation: laserScan 2s ease-in-out infinite alternate;"></div>
                    
                    <div style="position: relative; width: 170px; height: 170px; border: 2px dashed rgba(16, 185, 129, 0.4); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white-50 small font-sans"><i class="bi bi-qr-code text-white fs-1 d-block mb-1"></i>Align QR Code</span>
                    </div>
                </div>

                <form action="<?php echo ADMIN_URL; ?>/scanner.php" method="POST" class="mx-auto" style="max-width: 480px;">
                    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                    <div class="input-group input-group-lg mb-3">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-upc-scan"></i></span>
                        <input type="text" name="ticket_code" class="form-control border-start-0 text-center font-monospace fw-bold" placeholder="e.g. MV-2026-8942" value="<?php echo e($searchedCode); ?>" autofocus required>
                        <button type="submit" class="btn btn-mv-primary px-4 fw-bold">Validate Code</button>
                    </div>
                    <small class="text-muted">Enter or scan the alphanumeric ticket code printed below the guest QR pass.</small>
                </form>

            </div>
        </div>

        <!-- Scan Result Notification -->
        <?php if ($scanResult): ?>
            <?php if ($scanResult['status'] === 'success'): 
                $t = $scanResult['ticket'];
                $gName = trim(($t['fname'] ?? '') . ' ' . ($t['lname'] ?? ''));
                if (empty($gName)) $gName = 'Guest';
                $tot = (int)$t['adults_qty'] + (int)$t['children_qty'] + (int)$t['non_resident_qty'] + (int)$t['student_qty'];
            ?>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 border-start border-5 border-success">
                    <div class="card-body p-4 bg-success bg-opacity-10">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            <div>
                                <h4 class="fw-bold text-success mb-0"><?php echo e($scanResult['message']); ?></h4>
                                <small class="text-muted">Admitted at <?php echo date('H:i:s, d M Y'); ?></small>
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-3 border">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Lead Guest:</span>
                                    <strong class="text-dark fs-6"><?php echo e($gName); ?></strong>
                                    <small class="text-muted d-block"><?php echo e($t['phone'] ?: 'No phone'); ?></small>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Visit Date & Slot:</span>
                                    <strong class="text-dark"><?php echo e($t['visit_date']); ?></strong>
                                    <small class="text-secondary d-block"><?php echo e($t['session_slot']); ?></small>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Admitted Pass Count:</span>
                                    <span class="badge bg-primary fs-6"><?php echo $tot; ?> Persons</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">M-Pesa Verification:</span>
                                    <span class="font-monospace text-success fw-bold"><?php echo e($t['payment_reference']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($scanResult['status'] === 'already_used'): 
                $t = $scanResult['ticket'];
                $gName = trim(($t['fname'] ?? '') . ' ' . ($t['lname'] ?? ''));
            ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-start gap-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mt-1"></i>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Ticket Already Redeemed</h5>
                        <p class="mb-2"><?php echo e($scanResult['message']); ?></p>
                        <span class="small text-muted">Original Holder: <strong><?php echo e($gName); ?></strong> (<?php echo e($t['phone']); ?>)</span>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-x-octagon-fill text-danger fs-1"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Gate Validation Denied</h5>
                        <p class="mb-0 small"><?php echo e($scanResult['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<style>
@keyframes laserScan {
    0% { top: 15%; }
    100% { top: 85%; }
}
</style>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
