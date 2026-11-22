<?php
/**
 * Public Site Global Header
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$title = isset($pageTitle) ? e($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' — ' . SITE_TAGLINE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
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
<body class="d-flex flex-column min-vh-100">

<!-- Top Information Ribbon -->
<aside class="bg-primary-dark text-white py-1 px-3 border-bottom border-secondary border-opacity-25 small no-print">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-clock me-1 text-gold"></i> <?php echo OPERATING_HOURS; ?></span>
            <span class="d-none d-md-inline"><i class="bi bi-geo-alt me-1 text-gold"></i> <?php echo PHYSICAL_ADDRESS; ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="text-white text-decoration-none hover:text-warning">
                <i class="bi bi-telephone-fill me-1 text-gold"></i> <?php echo CONTACT_PHONE; ?>
            </a>
            <span class="text-secondary">|</span>
            <a href="<?php echo ADMIN_URL; ?>/login.php" class="text-white text-decoration-none hover:text-warning">
                <i class="bi bi-shield-lock me-1 text-gold"></i> Staff Portal
            </a>
        </div>
    </div>
</aside>
