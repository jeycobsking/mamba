<?php
/**
 * Public Homepage
 * Mamba Village Nairobi - Wildlife Sanctuary & Leisure Park
 */
$pageTitle = "Wildlife Sanctuary & Tourism Park";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$attractions = getAttractions(6);
$events = getEventsSchedule();
$ticketTypes = getTicketTypes();

$db = getDB();
$reviewsStmt = $db->query("SELECT * FROM feedback WHERE status = 'approved' ORDER BY feedback_id DESC LIMIT 3");
$reviews = $reviewsStmt->fetchAll();
?>

<!-- Hero Banner -->
<section class="hero-wrapper position-relative text-white overflow-hidden">
    <div class="container py-lg-5">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-8 text-center text-lg-start">
                <div class="hero-tag mb-3">
                    <i class="fa-solid fa-location-dot text-gold"></i>
                    <span>Karen, Nairobi &bull; East Africa's Premier Crocodile Park</span>
                </div>
                <h1 class="hero-title mb-3">
                    Experience the Untamed Splendor of <span class="text-gold">Mamba Village</span>
                </h1>
                <p class="lead text-white-50 mb-4 fs-5" style="max-width: 680px;">
                    Home to over 70 massive Nile crocodiles, ostrich and tortoise sanctuaries, lush botanical forest canopies, and tranquil boat cruises on Lake Mamba. Reserve your tickets securely online with instant Safaricom M-Pesa checkouts.
                </p>

                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                    <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-mv-gold btn-lg d-flex align-items-center gap-2">
                        <i class="bi bi-ticket-perforated-fill fs-5"></i>
                        <span>Book Passes Online</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/schedule.php" class="btn btn-outline-light btn-lg d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event fs-5"></i>
                        <span>Feeding Timetable</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/group-booking.php" class="btn btn-mv-primary btn-lg d-flex align-items-center gap-2">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                        <span>School & Group Tours</span>
                    </a>
                </div>

                <!-- Live Metrics Strip -->
                <div class="row g-3 pt-3 border-top border-white border-opacity-25 text-center text-lg-start">
                    <div class="col-6 col-md-3">
                        <div class="fs-3 fw-bold text-gold">70+</div>
                        <div class="small text-white-50">Nile Crocodiles</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-3 fw-bold text-gold">4.9 / 5.0</div>
                        <div class="small text-white-50">Visitor Satisfaction</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-3 fw-bold text-gold">15+ Acres</div>
                        <div class="small text-white-50">Botanical Flora</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="fs-3 fw-bold text-gold">100%</div>
                        <div class="small text-white-50">Instant E-Tickets</div>
                    </div>
                </div>
            </div>

            <!-- Quick Reservation Card -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-dark bg-white">
                    <div class="card-header bg-primary-mv text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-uppercase tracking-wider">Fast Pass Reservation</span>
                        <span class="badge bg-warning text-dark"><i class="bi bi-lightning-fill"></i> Instant QR</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                            <div>
                                <small class="text-muted d-block">Citizen Adult Admission</small>
                                <span class="fs-5 fw-bold text-success">KES 500.00</span>
                            </div>
                            <span class="badge bg-light text-secondary border">Per Person</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                            <div>
                                <small class="text-muted d-block">Child Pass (Ages 3–12)</small>
                                <span class="fs-5 fw-bold text-success">KES 300.00</span>
                            </div>
                            <span class="badge bg-light text-secondary border">Per Child</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                            <div>
                                <small class="text-muted d-block">International Tourist</small>
                                <span class="fs-5 fw-bold text-success">KES 2,000.00</span>
                            </div>
                            <span class="badge bg-light text-secondary border">Full Access</span>
                        </div>

                        <div class="alert alert-light border small text-muted mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>Account authentication required to generate official gate passes.</span>
                        </div>

                        <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-mv-primary w-100 py-2 fw-bold">
                            Proceed to Ticket Booking <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Park Attractions Section -->
<section class="py-5 bg-light" id="attractions">
    <div class="container py-lg-4">
        <div class="text-center mb-5">
            <span class="badge bg-success bg-opacity-10 text-success text-uppercase px-3 py-2 fw-bold small">Wildlife Encounters</span>
            <h2 class="display-6 fw-bold mt-2 text-dark">Explore Our Sanctuary Exhibits</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                From apex Nile crocodiles lunging for meat to hundred-year-old giant tortoises and tranquil lake cruises, experience Kenya's unique wildlife ecosystem.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($attractions as $item): ?>
            <div class="col-md-6 col-lg-4">
                <article class="attraction-card h-100">
                    <div class="attraction-img-wrap">
                        <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['name']); ?>" loading="lazy">
                        <span class="attraction-category-badge"><?php echo e($item['category']); ?></span>
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fs-5 fw-bold mb-0 text-dark"><?php echo e($item['name']); ?></h4>
                            <span class="badge bg-warning bg-opacity-25 text-dark fw-semibold small"><?php echo e($item['badge']); ?></span>
                        </div>
                        <p class="text-muted small flex-grow-1 mb-3">
                            <?php echo e($item['description']); ?>
                        </p>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-secondary fw-semibold">
                                <i class="bi bi-clock me-1 text-success"></i> <?php echo e($item['hours']); ?>
                            </span>
                            <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-sm btn-outline-success fw-semibold">
                                Book Entry
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo BASE_URL; ?>/attractions.php" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                View All Exhibits & Guided Tours <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- Daily Feeding Shows & Events Schedule -->
<section class="py-5 bg-white" id="schedule">
    <div class="container py-lg-4">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8 text-center text-lg-start">
                <span class="badge bg-warning bg-opacity-10 text-warning text-uppercase px-3 py-2 fw-bold small">Live Demonstrations</span>
                <h2 class="display-6 fw-bold mt-2 text-dark">Daily Feeding & Tour Timetable</h2>
                <p class="text-muted mb-0">Join our experienced wildlife curators for daily demonstrations and educational lectures.</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                <a href="<?php echo BASE_URL; ?>/schedule.php" class="btn btn-mv-primary">
                    <i class="bi bi-calendar-check me-1"></i> Full Schedule Details
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="timeline-track">
                    <?php foreach ($events as $ev): ?>
                    <div class="position-relative mb-4 ps-4">
                        <div class="timeline-point <?php echo $ev['is_highlight'] ? 'point-highlight' : ''; ?>"></div>
                        <div class="card border rounded-3 shadow-sm <?php echo $ev['is_highlight'] ? 'border-warning' : ''; ?>">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-mv text-white px-3 py-2 fw-bold font-monospace fs-6">
                                            <i class="bi bi-clock me-1"></i> <?php echo e($ev['event_time']); ?>
                                        </span>
                                        <span class="badge bg-light text-secondary border small">
                                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($ev['location']); ?>
                                        </span>
                                    </div>
                                    <?php if ($ev['is_highlight']): ?>
                                        <span class="badge bg-warning text-dark fw-bold">
                                            <i class="bi bi-star-fill me-1"></i> Highlight Spectacle
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold mb-1 text-dark"><?php echo e($ev['title']); ?></h5>
                                <p class="text-muted small mb-2"><?php echo e($ev['description']); ?></p>
                                <div class="d-flex flex-wrap justify-content-between align-items-center text-secondary small pt-2 border-top">
                                    <span><i class="bi bi-person-badge me-1"></i> Curator: <strong><?php echo e($ev['keeper']); ?></strong></span>
                                    <span><i class="bi bi-people me-1"></i> Capacity: <?php echo e($ev['capacity']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Feeding Show Highlight Banner -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 overflow-hidden shadow text-white position-relative" style="background: linear-gradient(rgba(18, 62, 44, 0.85), rgba(9, 35, 24, 0.95)), url('https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=800&q=80') center/cover no-repeat; min-height: 400px;">
                    <div class="card-body p-4 d-flex flex-column justify-content-end">
                        <span class="badge bg-warning text-dark align-self-start mb-2 px-3 py-2 fw-bold">Daily at 2:30 PM & 4:30 PM</span>
                        <h3 class="fw-bold mb-2">The Crocodile Feeding Spectacle</h3>
                        <p class="small text-white-50 mb-4">
                            Don't miss the heart-pounding moment as our 14-foot apex Nile crocodiles launch themselves from the ponds to snatch fresh meat from the elevated safety bridge.
                        </p>
                        <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-mv-gold fw-bold py-2">
                            Reserve Timed Passes <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Institutional & School Group Tours -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #092318 0%, #123e2c 100%);">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-warning bg-opacity-25 text-warning text-uppercase px-3 py-2 fw-bold small">Academic & Institutional Tours</span>
                <h2 class="display-6 fw-bold mt-2 text-white">Educational School Packages & Corporate Retreats</h2>
                <p class="text-white-50 lead fs-6 mb-4">
                    Mamba Village hosts over 250 school excursions each academic year. We offer tailored natural science syllabus lectures, guided zoological walkthroughs, safety-escorted boat rides, and spacious dining grounds.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-gold fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block text-white">Zoological Biology Lectures</strong>
                                <small class="text-white-50">Anatomy, breeding, and conservation cycles.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-gold fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block text-white">Complimentary Staff Passes</strong>
                                <small class="text-white-50">1 teacher free for every 15 students.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-gold fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block text-white">Dedicated Private Guide</strong>
                                <small class="text-white-50">Full group escort throughout the park.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-gold fs-5 mt-1"></i>
                            <div>
                                <strong class="d-block text-white">Official Tax Invoices</strong>
                                <small class="text-white-50">Itemized electronic invoices with VAT breakdown.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-dark bg-white">
                    <h4 class="fw-bold mb-2">Request Group Package Quote</h4>
                    <p class="text-muted small mb-4">Fill out your organization details for immediate invoice generation.</p>
                    
                    <form action="<?php echo BASE_URL; ?>/group-booking.php" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Institution / Company Name</label>
                            <input type="text" name="org" class="form-control" placeholder="e.g., Nairobi Day School" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Expected Headcount</label>
                                <input type="number" name="count" class="form-control" min="15" value="30" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Preferred Date</label>
                                <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-mv-gold w-100 py-2 fw-bold">
                            Continue to Group Booking Portal <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visitor Reviews & Testimonials -->
<section class="py-5 bg-light" id="reviews">
    <div class="container py-lg-4">
        <div class="text-center mb-5">
            <span class="badge bg-success bg-opacity-10 text-success text-uppercase px-3 py-2 fw-bold small">Customer Experiences</span>
            <h2 class="display-6 fw-bold mt-2 text-dark">Verified Visitor Testimonials</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Transparent feedback from tourists, educators, and families who visited our sanctuary.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($reviews as $rev): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-warning">
                            <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="badge bg-light text-secondary border small"><?php echo e($rev['category']); ?></span>
                    </div>
                    <p class="text-muted small fst-italic flex-grow-1 mb-3">
                        "<?php echo e($rev['comment']); ?>"
                    </p>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            <?php echo strtoupper(substr($rev['visitor_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong class="d-block small text-dark"><?php echo e($rev['visitor_name']); ?></strong>
                            <small class="text-muted" style="font-size: 0.75rem;">Verified Visitor</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo BASE_URL; ?>/reviews.php" class="btn btn-outline-success fw-semibold px-4">
                <i class="bi bi-pencil-square me-1"></i> Submit Your Review & Rating
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
