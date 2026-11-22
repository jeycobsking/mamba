<?php
/**
 * Dedicated Staff & Administrator Login Portal
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in as staff
if (isLoggedIn() && isStaff()) {
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh the page.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $redirectTarget = $_POST['redirect'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both staff work email and password.';
        } else {
            $result = loginUser($email, $password);
            if ($result['success']) {
                if (!isStaff()) {
                    logoutUser();
                    $error = 'Access denied. The provided account does not have administrative staff clearance.';
                } else {
                    setFlash('success', 'Staff authentication successful. Welcome, ' . e($_SESSION['user_name']));
                    if (!empty($redirectTarget)) {
                        header('Location: ' . $redirectTarget);
                    } else {
                        header('Location: ' . ADMIN_URL . '/index.php');
                    }
                    exit;
                }
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal Login | <?php echo SITE_NAME; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Application Stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body class="bg-primary-dark d-flex align-items-center justify-content-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="text-center mb-4">
                <div class="brand-mark bg-white text-success mx-auto mb-3 shadow" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="fa-solid fa-water"></i>
                </div>
                <h3 class="fw-bold text-white mb-1"><?php echo SITE_NAME; ?></h3>
                <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold small">Staff & Operations Management Portal</span>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-primary-mv text-white text-center py-3">
                    <span class="small fw-semibold text-uppercase tracking-wider">Restricted Administration Access</span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4 small" role="alert">
                            <i class="bi bi-shield-x me-2 fs-5"></i>
                            <div><?php echo e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo ADMIN_URL; ?>/login.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                        <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Staff Work Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="admin@mambavillage.co.ke" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-mv-primary w-100 py-2 fw-bold mb-3 shadow-sm">
                            <i class="bi bi-shield-lock-fill me-1"></i> Sign In to Staff Portal
                        </button>

                        <!-- Quick Demo Credentials Selector -->
                        <div class="p-3 bg-light rounded-3 border small text-secondary mb-3">
                            <strong class="d-block text-dark mb-1"><i class="bi bi-info-circle text-primary me-1"></i>Authorized Staff Credentials:</strong>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Super Administrator:</span>
                                <code>admin@mambavillage.co.ke</code>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Finance Manager:</span>
                                <code>finance@mambavillage.co.ke</code>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Default Password:</span>
                                <code>Admin@2026</code>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>/index.php" class="small text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Return to Public Visitor Website
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

