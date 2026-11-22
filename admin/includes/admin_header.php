<?php
/**
 * Dedicated Admin Portal Header
 */
require_once __DIR__ . '/admin_auth.php';
$adminTitle = isset($pageTitle) ? e($pageTitle) . ' | Operations Portal' : 'Park Administration & Operations Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $adminTitle; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mvgreen: '#123e2c',
                        mvgreenDark: '#092318',
                        mvgreenLight: '#1b5e43',
                        mvgold: '#d97706',
                        mvemerald: '#10b981'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Application Stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body class="bg-light">

<!-- Top Admin Navigation Bar -->
<header class="navbar navbar-dark bg-primary-dark sticky-top p-0 shadow-sm border-bottom border-secondary border-opacity-25 no-print">
    <div class="container-fluid px-3 py-2">
        <a class="navbar-brand d-flex align-items-center gap-2 me-0 fs-6 fw-bold text-white" href="<?php echo ADMIN_URL; ?>/index.php">
            <div class="brand-mark bg-success" style="width: 36px; height: 36px; font-size: 1rem;">
                <i class="fa-solid fa-water"></i>
            </div>
            <div>
                <span class="d-block lh-1 text-white"><?php echo SITE_NAME; ?></span>
                <small class="text-white-50 font-sans" style="font-size: 0.7rem; text-transform: uppercase;">Management Control Center</small>
            </div>
        </a>

        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" class="btn btn-sm btn-outline-light d-none d-md-inline-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-up-right"></i> Public Website
            </a>
            
            <a href="<?php echo ADMIN_URL; ?>/scanner.php" class="btn btn-sm btn-mv-gold d-inline-flex align-items-center gap-1 fw-bold">
                <i class="bi bi-qr-code-scan"></i> Gate Scanner
            </a>

            <!-- Staff Identity Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary text-white border-0 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="rounded-circle bg-success bg-opacity-25 text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                        <?php echo strtoupper(substr($staffName, 0, 1)); ?>
                    </span>
                    <span class="d-none d-sm-inline text-start lh-1">
                        <strong class="d-block small"><?php echo e($staffName); ?></strong>
                        <small class="text-white-50" style="font-size: 0.7rem;"><?php echo strtoupper(str_replace('_', ' ', $staffRole)); ?></small>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li class="px-3 py-2 border-bottom">
                        <span class="d-block fw-bold small"><?php echo e($staffName); ?></span>
                        <span class="d-block text-muted small"><?php echo e($staffEmail); ?></span>
                        <span class="badge bg-primary text-uppercase mt-1" style="font-size: 0.65rem;"><?php echo e($staffRole); ?></span>
                    </li>
                    <li><a class="dropdown-item py-2" href="<?php echo ADMIN_URL; ?>/users.php"><i class="bi bi-people me-2"></i> User Accounts</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="<?php echo ADMIN_URL; ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Staff Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar will be included next -->
