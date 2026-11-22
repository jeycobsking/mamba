<?php
/**
 * Visitor & Customer Registration
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh the page.';
    } else {
        $fname = trim($_POST['fname'] ?? '');
        $lname = trim($_POST['lname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $redirectTarget = $_POST['redirect'] ?? '';

        if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($password)) {
            $error = 'All fields are required to create a verified visitor profile.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters in length.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Password confirmation does not match.';
        } else {
            $db = getDB();

            // Check if email already registered
            $check = $db->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'An account with this email address already exists. Please log in.';
            } else {
                try {
                    $db->beginTransaction();

                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $uStmt = $db->prepare("INSERT INTO users (email, password, role, is_active, created_at) VALUES (?, ?, 'visitor', 1, NOW())");
                    $uStmt->execute([$email, $hashedPassword]);
                    $userId = $db->lastInsertId();

                    $vStmt = $db->prepare("INSERT INTO visitors (user_id, fname, lname, phone, country) VALUES (?, ?, ?, ?, 'Kenya')");
                    $vStmt->execute([$userId, $fname, $lname, $phone]);

                    $db->commit();

                    // Automatic Login
                    loginUser($email, $password);
                    setFlash('success', 'Your visitor account was created successfully! Welcome to ' . SITE_NAME . '.');

                    if (!empty($redirectTarget)) {
                        header('Location: ' . $redirectTarget);
                    } else {
                        header('Location: ' . BASE_URL . '/book.php');
                    }
                    exit;

                } catch (Exception $e) {
                    $db->rollBack();
                    $error = 'Registration could not be completed: ' . $e->getMessage();
                }
            }
        }
    }
}

$pageTitle = "Register Account";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary-mv text-white text-center py-4">
                    <div class="brand-mark bg-white text-success mx-auto mb-2">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Create Visitor Account</h4>
                    <p class="text-white-50 small mb-0">Register once to access digital ticketing, e-passes, and tour bookings</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?php echo e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/register.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                        <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">First Name</label>
                                <input type="text" name="fname" class="form-control" placeholder="e.g. Samuel" value="<?php echo e($_POST['fname'] ?? ''); ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Last Name</label>
                                <input type="text" name="lname" class="form-control" placeholder="e.g. Mwangi" value="<?php echo e($_POST['lname'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="samuel@example.com" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">M-Pesa Mobile Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="0712 345 678" value="<?php echo e($_POST['phone'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-mv-primary w-100 py-2 fw-bold mb-3">
                            <i class="bi bi-check2-circle me-1"></i> Complete Registration
                        </button>

                        <div class="text-center small text-secondary">
                            Already registered? 
                            <a href="<?php echo BASE_URL; ?>/login.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" class="text-success fw-bold text-decoration-none">
                                Log In to Your Account
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
