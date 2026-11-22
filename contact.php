<?php
/**
 * Contact Information & Park Inquiries
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $sent = true;
        setFlash('success', 'Your message has been sent to our guest relations team. We will respond within 24 hours.');
    }
}

$pageTitle = "Contact & Directions";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="py-5 bg-primary-dark text-white text-center">
    <div class="container py-4">
        <span class="badge bg-warning text-dark text-uppercase small px-3 py-2 fw-bold mb-2">Location & Communications</span>
        <h1 class="display-5 fw-bold mb-2">Contact Mamba Village Nairobi</h1>
        <p class="text-white-50 mx-auto fs-6" style="max-width: 650px;">
            Have inquiries regarding tickets, private celebrations, photography permits, or guided school tours? Our guest team is at your service.
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-5">
            <h4 class="fw-bold text-dark mb-4">Direct Contact Channels</h4>

            <div class="d-flex flex-column gap-3 mb-4">
                <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-3 shadow-sm">
                    <i class="bi bi-geo-alt-fill text-gold fs-3 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark">Physical Address</strong>
                        <span class="text-muted small"><?php echo PHYSICAL_ADDRESS; ?></span>
                    </div>
                </div>

                <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-3 shadow-sm">
                    <i class="bi bi-telephone-fill text-success fs-3 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark">Telephone & Inquiries</strong>
                        <span class="text-muted small"><?php echo CONTACT_PHONE; ?></span>
                    </div>
                </div>

                <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-3 shadow-sm">
                    <i class="bi bi-envelope-fill text-primary fs-3 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark">Official Email</strong>
                        <span class="text-muted small"><?php echo CONTACT_EMAIL; ?></span>
                    </div>
                </div>

                <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-3 shadow-sm">
                    <i class="bi bi-clock-fill text-warning fs-3 mt-1"></i>
                    <div>
                        <strong class="d-block text-dark">Visiting Hours</strong>
                        <span class="text-muted small"><?php echo OPERATING_HOURS; ?> (Including all public holidays)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h4 class="fw-bold text-dark mb-1">Send an Online Inquiry</h4>
                <p class="text-muted small mb-4">We usually respond within a few hours during park opening times.</p>

                <form action="<?php echo BASE_URL; ?>/contact.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Your Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Grace Wambui" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="grace@example.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Contact Phone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0712 345 678" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Subject</label>
                            <select name="subject" class="form-select">
                                <option value="Ticket Booking Inquiry" selected>Ticket Booking Inquiry</option>
                                <option value="School Tour Package">School Tour Package</option>
                                <option value="Private Event / Birthday / Wedding">Private Event / Birthday / Wedding</option>
                                <option value="Commercial Photography Permit">Commercial Photography Permit</option>
                                <option value="General Feedback">General Feedback</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Message</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="How can our park management assist you?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-mv-primary px-4 py-2 fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Send Inquiry Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
