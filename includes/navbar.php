<?php
/**
 * Public Navigation Bar
 */
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top no-print">
    <div class="container py-2">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/index.php">
            <div class="brand-mark">
                <i class="fa-solid fa-water"></i>
            </div>
            <div>
                <span class="d-block fw-bold text-success text-opacity-100 fs-5 lh-1"><?php echo SITE_NAME; ?></span>
                <small class="text-muted d-block font-sans" style="font-size: 0.725rem; letter-spacing: 0.05em; text-transform: uppercase;">Sanctuary & Leisure Park</small>
            </div>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/attractions.php">Attractions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/schedule.php">Feeding Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 text-success" href="<?php echo BASE_URL; ?>/book.php">
                        <i class="bi bi-ticket-perforated me-1"></i> Book Tickets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/group-booking.php">School & Groups</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/reviews.php">Reviews</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
                </li>
            </ul>

            <!-- Authentication Controls -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 py-2 px-3 rounded-pill" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 text-success"></i>
                            <span class="fw-semibold small"><?php echo e($currentUser['name']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuDropdown">
                            <li class="px-3 py-2 border-bottom">
                                <span class="d-block fw-bold small"><?php echo e($currentUser['name']); ?></span>
                                <span class="d-block text-muted small"><?php echo e($currentUser['email']); ?></span>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/my-bookings.php">
                                    <i class="bi bi-ticket-detailed me-2 text-primary"></i> My E-Tickets & Passes
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/book.php">
                                    <i class="bi bi-plus-circle me-2 text-success"></i> New Reservation
                                </a>
                            </li>
                            <?php if (isStaff()): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-semibold" href="<?php echo ADMIN_URL; ?>/index.php">
                                    <i class="bi bi-speedometer2 me-2"></i> Go to Staff Dashboard
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="<?php echo BASE_URL; ?>/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-outline-secondary px-3 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                    </a>
                    <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-mv-primary px-3 py-2">
                        <i class="bi bi-person-plus me-1"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- System Flash Message Container -->
<div class="container mt-3">
    <?php echo displayFlash(); ?>
</div>
