<?php
/**
 * Visitor & Customer Authentication
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $redirectTarget = $_POST['redirect'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please provide both your registered email and password.';
        } else {
            $result = loginUser($email, $password);
            if ($result['success']) {
                setFlash('success', 'Welcome back, ' . e($_SESSION['user_name']) . '!');
                
                // If user is staff, redirect to admin
                if (isStaff()) {
                    header('Location: ' . ADMIN_URL . '/index.php');
                    exit;
                }

                if (!empty($redirectTarget) && str_starts_with($redirectTarget, BASE_URL)) {
                    header('Location: ' . $redirectTarget);
                } elseif (!empty($redirectTarget) && str_starts_with($redirectTarget, '/')) {
                    header('Location: ' . $redirectTarget);
                } else {
                    header('Location: ' . BASE_URL . '/book.php');
                }
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = "Visitor Sign In";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary-mv text-white text-center py-4">
                    <div class="brand-mark bg-white text-success mx-auto mb-2">
                        <i class="bi bi-person-fill-lock"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Visitor Portal Login</h4>
                    <p class="text-white-50 small mb-0">Sign in to book passes and access your digital tickets</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?php echo e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/login.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                        <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="e.g. visitor@gmail.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">Account Password</label>
                                <a href="<?php echo BASE_URL; ?>/contact.php" class="small text-muted text-decoration-none">Need Help?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-mv-primary w-100 py-2 fw-bold mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Account
                        </button>

                        <div class="p-3 bg-light rounded-3 border text-center small text-muted mb-3">
                            <span>Demo Visitor Account:</span><br>
                            <code>visitor@gmail.com</code> &bull; Pass: <code>Visitor@2026</code>
                        </div>

                        <div class="text-center small text-secondary">
                            Don't have an account yet? 
                            <a href="<?php echo BASE_URL; ?>/register.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" class="text-success fw-bold text-decoration-none">
                                Register Here
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
