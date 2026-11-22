<?php
/**
 * Visitor Reviews & Feedback Module
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$currentUser = getCurrentUser();
$db = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin(); // Must be logged in to submit a review

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $category = $_POST['category'] ?? 'General Sanctuary Experience';
        $comment = trim($_POST['comment'] ?? '');

        if (empty($comment)) {
            $error = 'Please provide your feedback comments.';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO feedback (user_id, visitor_name, rating, category, comment, status, created_at) VALUES (?, ?, ?, ?, ?, 'approved', NOW())");
                $stmt->execute([$currentUser['user_id'], $currentUser['name'], $rating, $category, $comment]);
                
                logActivity('FEEDBACK_SUBMITTED', "Review submitted by {$currentUser['name']}", $currentUser['user_id']);
                setFlash('success', 'Thank you! Your feedback has been recorded.');
                header('Location: ' . BASE_URL . '/reviews.php');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to submit review: ' . $e->getMessage();
            }
        }
    }
}

$revStmt = $db->query("SELECT * FROM feedback WHERE status = 'approved' ORDER BY feedback_id DESC");
$reviews = $revStmt->fetchAll();

$pageTitle = "Visitor Reviews & Feedback";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="py-5 bg-primary-dark text-white text-center">
    <div class="container py-4">
        <span class="badge bg-warning text-dark text-uppercase small px-3 py-2 fw-bold mb-2">Visitor Experiences</span>
        <h1 class="display-5 fw-bold mb-2">Guest Testimonials & Ratings</h1>
        <p class="text-white-50 mx-auto fs-6" style="max-width: 650px;">
            Read firsthand accounts from families, tourists, and educational groups who visited Mamba Village Nairobi.
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">
        <!-- Reviews Grid -->
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4 text-dark">Verified Visitor Reviews (<?php echo count($reviews); ?>)</h4>

            <div class="d-flex flex-column gap-3">
                <?php foreach ($reviews as $r): ?>
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-warning">
                            <?php for ($i = 0; $i < $r['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                            <?php for ($i = $r['rating']; $i < 5; $i++): ?>
                                <i class="bi bi-star text-muted"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="badge bg-light text-secondary border small"><?php echo e($r['category']); ?></span>
                    </div>

                    <p class="text-muted small fst-italic mb-3">
                        "<?php echo e($r['comment']); ?>"
                    </p>

                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                            <?php echo strtoupper(substr($r['visitor_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong class="d-block small text-dark"><?php echo e($r['visitor_name']); ?></strong>
                            <small class="text-muted" style="font-size: 0.75rem;">Verified Guest &bull; <?php echo date('M Y', strtotime($r['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Review Form / Login prompt -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white sticky-top" style="top: 5rem;">
                <div class="card-header bg-primary-mv text-white p-4">
                    <h5 class="fw-bold mb-1">Share Your Visit Experience</h5>
                    <small class="text-white-50">Help park curators enhance wildlife exhibits and visitor safety</small>
                </div>

                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger small mb-3"><?php echo e($error); ?></div>
                    <?php endif; ?>

                    <?php if ($currentUser): ?>
                        <form action="<?php echo BASE_URL; ?>/reviews.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Logged in as:</label>
                                <input type="text" class="form-control" value="<?php echo e($currentUser['name']); ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Experience Rating</label>
                                <select name="rating" class="form-select">
                                    <option value="5" selected>5 Stars - Exceptional Encounter</option>
                                    <option value="4">4 Stars - Very Good Visit</option>
                                    <option value="3">3 Stars - Average Experience</option>
                                    <option value="2">2 Stars - Needs Improvement</option>
                                    <option value="1">1 Star - Poor Service</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Sanctuary Category</label>
                                <select name="category" class="form-select">
                                    <option value="Crocodile Feeding Show" selected>Crocodile Feeding Show</option>
                                    <option value="Lake Mamba Boat Cruise">Lake Mamba Boat Cruise</option>
                                    <option value="Ostrich & Tortoise Haven">Ostrich & Tortoise Haven</option>
                                    <option value="Botanical Gardens">Botanical Gardens</option>
                                    <option value="Garden Restaurant & BBQ">Garden Restaurant & BBQ</option>
                                    <option value="Gate Entry & Ticketing">Gate Entry & Ticketing</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Your Comments & Suggestions</label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="How was your visit to Mamba Village? Tell us about the keepers and animals..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-mv-primary w-100 py-2 fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Publish Review
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-shield-lock fs-1 text-muted mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark">Account Required to Submit Review</h6>
                            <p class="text-muted small mb-4">
                                To ensure authentic visitor testimonials, please log in with your verified visitor account.
                            </p>
                            <a href="<?php echo BASE_URL; ?>/login.php?redirect=<?php echo urlencode(BASE_URL . '/reviews.php'); ?>" class="btn btn-mv-primary w-100 fw-bold py-2 mb-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Review
                            </a>
                            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-outline-secondary w-100 fw-semibold py-2 small">
                                Register New Account
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
