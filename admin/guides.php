<?php
/**
 * Tour Guide Allocations & Roster Module
 */
require_once __DIR__ . '/includes/admin_auth.php';
requireStaffRole(['admin', 'guide']);

$db = getDB();

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_toggle_status'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $gid = (int)$_POST['guide_id'];
        $currentStatus = $_POST['current_status'] ?? 'available';
        $newStatus = $currentStatus === 'available' ? 'on_tour' : 'available';

        $up = $db->prepare("UPDATE tour_guides SET status = ? WHERE guide_id = ?");
        $up->execute([$newStatus, $gid]);
        
        logActivity('GUIDE_STATUS', "Guide ID #{$gid} status changed to {$newStatus}", $currentStaff['user_id']);
        setFlash('success', "Guide duty status updated to " . strtoupper($newStatus));
        header('Location: ' . ADMIN_URL . '/guides.php');
        exit;
    }
}

// Handle New Guide Dispatch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_add_guide'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $tour = trim($_POST['assigned_tour'] ?? '');

        if (!empty($name) && !empty($phone)) {
            $stmt = $db->prepare("INSERT INTO tour_guides (name, phone, specialty, assigned_tour, status, created_at) VALUES (?, ?, ?, ?, 'on_tour', NOW())");
            $stmt->execute([$name, $phone, $specialty, $tour]);
            
            logActivity('GUIDE_DISPATCHED', "Guide {$name} dispatched to {$tour}", $currentStaff['user_id']);
            setFlash('success', "Guide {$name} dispatched on tour successfully.");
            header('Location: ' . ADMIN_URL . '/guides.php');
            exit;
        }
    }
}

$stmt = $db->query("SELECT * FROM tour_guides ORDER BY guide_id ASC");
$guides = $stmt->fetchAll();

$pageTitle = "Tour Guide Roster & Assignments";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Tour Guide Allocations & Roster</h2>
        <span class="text-muted small">Assign certified wildlife naturalists to visiting student groups and tourist delegations</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#dispatchGuideModal">
            <i class="bi bi-person-plus-fill"></i> Dispatch New Guide
        </button>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($guides as $g): ?>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                        <i class="bi bi-compass"></i>
                    </div>
                    <span class="badge <?php echo $g['status'] === 'on_tour' ? 'bg-warning text-dark' : 'bg-success'; ?>">
                        <?php echo strtoupper(str_replace('_', ' ', $g['status'])); ?>
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1"><?php echo e($g['name']); ?></h5>
                <small class="text-muted mb-3 d-block"><i class="bi bi-telephone me-1"></i><?php echo e($g['phone']); ?></small>

                <div class="p-2 bg-light rounded border small mb-3 flex-grow-1">
                    <span class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">Specialty:</span>
                    <strong class="text-dark d-block mb-2"><?php echo e($g['specialty']); ?></strong>
                    <span class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">Active Assignment:</span>
                    <span class="text-primary fw-semibold"><?php echo e($g['assigned_tour'] ?: 'Available on Standby'); ?></span>
                </div>

                <form action="<?php echo ADMIN_URL; ?>/guides.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                    <input type="hidden" name="guide_id" value="<?php echo $g['guide_id']; ?>">
                    <input type="hidden" name="current_status" value="<?php echo e($g['status']); ?>">
                    <button type="submit" name="action_toggle_status" class="btn btn-sm btn-outline-secondary w-100 fw-semibold">
                        Toggle Duty Status
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Dispatch Guide -->
<div class="modal fade" id="dispatchGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary-mv text-white p-4">
                <h5 class="modal-title fw-bold">Dispatch Certified Naturalist Guide</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo ADMIN_URL; ?>/guides.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action_add_guide" value="1">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Guide Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Peter Githinji" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Mobile Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+254 711 000 000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Herpetological / Botanical Specialty</label>
                        <input type="text" name="specialty" class="form-control" placeholder="e.g. Nile Crocodile Ecology & Reptile Anatomy" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Assigned Group / Delegation Itinerary</label>
                        <input type="text" name="assigned_tour" class="form-control" placeholder="e.g. St. Austin Academy (35 students)" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mv-primary btn-sm fw-bold">Dispatch Guide on Duty</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
