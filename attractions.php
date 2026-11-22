<?php
/**
 * Exhibits & Attractions Showcase
 */
$pageTitle = "Sanctuary Exhibits & Encounters";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$attractions = getAttractions();
?>

<!-- Banner -->
<section class="py-5 bg-primary-dark text-white text-center">
    <div class="container py-4">
        <span class="badge bg-warning text-dark text-uppercase small px-3 py-2 fw-bold mb-2">Sanctuary Portfolio</span>
        <h1 class="display-5 fw-bold mb-2">Park Exhibits & Encounters</h1>
        <p class="text-white-50 mx-auto fs-6" style="max-width: 650px;">
            Immerse yourself in specialized wildlife enclosures, endangered reptile breeding centers, tranquil waterways, and botanical forest trails.
        </p>
    </div>
</section>

<!-- Attractions Grid -->
<div class="container py-5">
    <div class="row g-4">
        <?php foreach ($attractions as $item): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                <div class="row g-0 h-100">
                    <div class="col-md-5 position-relative">
                        <img src="<?php echo e($item['image_url']); ?>" class="img-fluid h-100 w-100 object-fit-cover" alt="<?php echo e($item['name']); ?>" style="min-height: 240px;" loading="lazy">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 text-white text-uppercase small fw-bold">
                            <?php echo e($item['category']); ?>
                        </span>
                    </div>
                    <div class="col-md-7 d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-bold fs-5 text-dark mb-0"><?php echo e($item['name']); ?></h4>
                            <span class="badge bg-warning bg-opacity-25 text-dark small"><?php echo e($item['badge']); ?></span>
                        </div>
                        <p class="text-muted small flex-grow-1 mb-3">
                            <?php echo e($item['description']); ?>
                        </p>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-secondary fw-semibold">
                                <i class="bi bi-clock text-success me-1"></i> <?php echo e($item['hours']); ?>
                            </span>
                            <a href="<?php echo BASE_URL; ?>/book.php" class="btn btn-sm btn-mv-primary fw-semibold">
                                Book Entry <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
