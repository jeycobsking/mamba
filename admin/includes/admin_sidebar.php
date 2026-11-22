<?php
/**
 * Dedicated Admin Portal Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav id="adminSidebar" class="col-md-3 col-lg-2 d-md-block bg-primary-dark min-vh-100 sidebar p-3 text-white border-end border-secondary border-opacity-25 no-print">
    <div class="position-sticky pt-2">
        
        <!-- Role Indicator Pill -->
        <div class="p-2 mb-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10 small">
            <span class="text-white-50 d-block" style="font-size: 0.7rem; text-transform: uppercase;">Active Perspective:</span>
            <strong class="text-warning d-block"><?php echo strtoupper(str_replace('_', ' ', $staffRole)); ?></strong>
            <span class="text-success small"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> System Live</span>
        </div>

        <ul class="nav flex-column admin-sidebar-nav">
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/bookings.php">
                    <i class="bi bi-ticket-perforated"></i> Admissions Register
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'scanner.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/scanner.php">
                    <i class="bi bi-qr-code-scan"></i> Gate Scanner
                </a>
            </li>

            <?php if (in_array($staffRole, ['admin', 'finance'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'finances.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/finances.php">
                    <i class="bi bi-currency-exchange"></i> Finance & M-Pesa
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($staffRole, ['admin', 'event_manager'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'events.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/events.php">
                    <i class="bi bi-calendar-event"></i> Feeding & Shows
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($staffRole, ['admin', 'guide'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'guides.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/guides.php">
                    <i class="bi bi-compass"></i> Tour Guide Roster
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($staffRole, ['admin', 'supplier'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'suppliers.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/suppliers.php">
                    <i class="bi bi-truck"></i> Supply Logistics
                </a>
            </li>
            <?php endif; ?>

            <?php if ($staffRole === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/users.php">
                    <i class="bi bi-people"></i> Staff & User Roles
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>/reports.php">
                    <i class="bi bi-bar-chart-line"></i> Analytics & Reports
                </a>
            </li>
        </ul>

        <hr class="border-secondary border-opacity-25 my-4">

        <div class="px-2">
            <a href="<?php echo ADMIN_URL; ?>/logout.php" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Sign Out Staff
            </a>
        </div>
    </div>
</nav>

<!-- Main Admin Body Content Area -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Flash Messages -->
    <?php echo displayFlash(); ?>
