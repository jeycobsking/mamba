<?php
/**
 * Daily Feeding Shows & Events Schedule
 */
$pageTitle = "Feeding Shows & Event Timetable";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$category = $_GET['cat'] ?? 'all';
$events = getEventsSchedule($category);
?>

<!-- Banner -->
<section class="py-5 bg-primary-dark text-white text-center">
    <div class="container py-4">
        <span class="badge bg-warning text-dark text-uppercase small px-3 py-2 fw-bold mb-2">Daily Park Operations</span>
        <h1 class="display-5 fw-bold mb-2">Shows & Feeding Schedule</h1>
        <p class="text-white-50 mx-auto fs-6" style="max-width: 650px;">
            Witness our veteran keepers conduct live reptile demonstrations, botanical walks, and the electrifying 2:30 PM & 4:30 PM crocodile feeding spectacles.
        </p>
    </div>
</section>

<div class="container py-5">
    
    <!-- Category Filter Bar -->
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
        <a href="<?php echo BASE_URL; ?>/schedule.php?cat=all" class="btn <?php echo $category === 'all' ? 'btn-mv-primary' : 'btn-outline-secondary'; ?> rounded-pill px-4">
            All Demonstrations
        </a>
        <a href="<?php echo BASE_URL; ?>/schedule.php?cat=feeding" class="btn <?php echo $category === 'feeding' ? 'btn-mv-primary' : 'btn-outline-secondary'; ?> rounded-pill px-4">
            Feeding Shows
        </a>
        <a href="<?php echo BASE_URL; ?>/schedule.php?cat=tour" class="btn <?php echo $category === 'tour' ? 'btn-mv-primary' : 'btn-outline-secondary'; ?> rounded-pill px-4">
            Guided Tours
        </a>
        <a href="<?php echo BASE_URL; ?>/schedule.php?cat=family" class="btn <?php echo $category === 'family' ? 'btn-mv-primary' : 'btn-outline-secondary'; ?> rounded-pill px-4">
            Family & Kids
        </a>
    </div>

    <!-- Timeline List -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="timeline-track">
                <?php if (empty($events)): ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle fs-4 me-2"></i> No scheduled events found for this filter category today.
                    </div>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                    <div class="position-relative mb-4 ps-4">
                        <div class="timeline-point <?php echo $ev['is_highlight'] ? 'point-highlight' : ''; ?>"></div>
                        <div class="card border rounded-4 shadow-sm bg-white overflow-hidden <?php echo $ev['is_highlight'] ? 'border-warning' : ''; ?>">
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-mv text-white px-3 py-2 fw-bold font-monospace fs-6">
                                            <i class="bi bi-clock me-1"></i> <?php echo e($ev['event_time']); ?>
                                        </span>
                                        <span class="badge bg-light text-secondary border">
                                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($ev['location']); ?>
                                        </span>
                                    </div>
                                    <?php if ($ev['is_highlight']): ?>
                                        <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                                            <i class="bi bi-star-fill me-1"></i> Highlight Spectacle
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h4 class="fw-bold mb-2 text-dark"><?php echo e($ev['title']); ?></h4>
                                <p class="text-muted small mb-3"><?php echo e($ev['description']); ?></p>

                                <div class="d-flex flex-wrap justify-content-between align-items-center text-secondary small pt-3 border-top">
                                    <div class="d-flex gap-3">
                                        <span><i class="bi bi-person-badge me-1"></i> Keeper: <strong><?php echo e($ev['keeper']); ?></strong></span>
                                        <span><i class="bi bi-people me-1"></i> Audience Limit: <strong><?php echo e($ev['capacity']); ?></strong></span>
                                    </div>
                                    <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-sm btn-outline-success fw-semibold mt-2 mt-sm-0">
                                        Book Visit Pass <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
