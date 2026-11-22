<?php
/**
 * School & Corporate Group Reservation Portal
 * Enforces visitor authentication
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$currentUser = getCurrentUser();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $orgName = trim($_POST['org_name'] ?? '');
        $headcount = (int)($_POST['headcount'] ?? 0);
        $date = $_POST['tentative_date'] ?? '';
        $packageType = $_POST['package_type'] ?? 'School Educational Biology Tour';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($orgName) || $headcount < 10 || empty($date)) {
            $error = 'Please provide organization name, minimum 10 headcount, and tentative date.';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO group_inquiries (user_id, organization_name, headcount, tentative_date, package_type, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->execute([$currentUser['user_id'], $orgName, $headcount, $date, $packageType, $notes]);

                logActivity('GROUP_INQUIRY', "Group quote submitted for {$orgName} ({$headcount} pax)", $currentUser['user_id']);
                setFlash('success', "Your group reservation inquiry for {$orgName} ({$headcount} attendees) has been submitted! Our Service Manager will send the formal invoice.");
                
                header('Location: ' . BASE_URL . '/my-bookings.php');
                exit;
            } catch (Exception $e) {
                $error = 'Submission error: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = "School & Institutional Group Packages";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="row g-5 align-items-center">
        <!-- Perks Column -->
        <div class="col-lg-6">
            <span class="badge bg-success bg-opacity-10 text-success text-uppercase small px-3 py-2 fw-bold mb-2">Institutional Packages</span>
            <h2 class="display-6 fw-bold text-dark mb-3">Group Safari Tours & Educational Expeditions</h2>
            <p class="text-muted mb-4">
                We accommodate hundreds of primary, secondary, and tertiary school groups annually, as well as corporate team-building retreats and community gatherings.
            </p>

            <div class="d-flex flex-column gap-3 mb-4">
                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 border shadow-sm">
                    <i class="bi bi-mortarboard-fill text-success fs-3"></i>
                    <div>
                        <strong class="d-block text-dark">Biological Science Curriculum Support</strong>
                        <small class="text-muted">Structured lectures on reptile anatomy, incubation cycles, and aquatic biodiversity.</small>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 border shadow-sm">
                    <i class="bi bi-award-fill text-gold fs-3"></i>
                    <div>
                        <strong class="d-block text-dark">Staff & Chaperone Concessions</strong>
                        <small class="text-muted">1 complimentary admission ticket provided for every 15 student passes.</small>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 border shadow-sm">
                    <i class="bi bi-receipt-cutoff text-primary fs-3"></i>
                    <div>
                        <strong class="d-block text-dark">Official Institutional Tax Invoices</strong>
                        <small class="text-muted">Consolidated itemized invoices including VAT calculations for school finance offices.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Column -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-primary-mv text-white p-4">
                    <h5 class="fw-bold mb-1">Request Institutional Group Quote</h5>
                    <small class="text-white-50">Authenticated as: <?php echo e($currentUser['name']); ?></small>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?php echo e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/group-booking.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Institution / Company Name</label>
                            <input type="text" name="org_name" class="form-control" placeholder="e.g. Nairobi International Academy" value="<?php echo e($_GET['org'] ?? ''); ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Estimated Headcount</label>
                                <input type="number" name="headcount" class="form-control" min="10" max="600" value="<?php echo e($_GET['count'] ?? '30'); ?>" required>
                                <small class="text-muted">Minimum 10 attendees</small>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Tentative Visit Date</label>
                                <input type="date" name="tentative_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo e($_GET['date'] ?? date('Y-m-d', strtotime('+3 days'))); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Package Category</label>
                            <select name="package_type" class="form-select">
                                <option value="School Educational Biology Tour" selected>School Educational Biology Tour</option>
                                <option value="University / College Research Fieldwork">University / College Research Fieldwork</option>
                                <option value="Corporate Team Building & BBQ">Corporate Team Building & BBQ</option>
                                <option value="Church / Community Picnic Gathering">Church / Community Picnic Gathering</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Itinerary Specifications & Special Requests</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Tell us if you require catering, Lake Mamba boat rides, or dedicated biology lecturers..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-mv-gold w-100 py-3 fw-bold shadow">
                            <i class="bi bi-send-check-fill me-1"></i> Submit Group Booking Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
