<?php
/**
 * User & Staff Roles Management Module
 */
require_once __DIR__ . '/includes/admin_auth.php';
requireStaffRole('admin'); // Super admin only

$db = getDB();

// Handle Create New Staff Account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_create_staff'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'guide';
        $password = $_POST['password'] ?? 'Staff@2026';

        if (!empty($email) && !empty($name)) {
            // Check existing
            $chk = $db->prepare("SELECT user_id FROM users WHERE email = ?");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                setFlash('danger', 'An account with this email address already exists.');
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $uStmt = $db->prepare("INSERT INTO users (email, password, role, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
                $uStmt->execute([$email, $hash, $role]);
                $newUid = $db->lastInsertId();

                $parts = explode(' ', $name);
                $aStmt = $db->prepare("INSERT INTO administrators (user_id, fname, lname, phone, department, position, access_level) VALUES (?, ?, ?, ?, ?, 'Officer', 'admin')");
                $aStmt->execute([$newUid, $parts[0], $parts[1] ?? '', $phone, ucfirst(str_replace('_', ' ', $role)) . ' Unit']);

                logActivity('STAFF_ACCOUNT_CREATED', "New staff account {$email} ({$role}) created", $currentStaff['user_id']);
                setFlash('success', "Staff account for {$name} created successfully with role {$role}.");
                header('Location: ' . ADMIN_URL . '/users.php');
                exit;
            }
        }
    }
}

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_toggle_active'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $uid = (int)$_POST['user_id'];
        $curr = (int)$_POST['current_active'];
        $next = $curr ? 0 : 1;

        if ($uid !== $currentStaff['user_id']) {
            $up = $db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
            $up->execute([$next, $uid]);
            setFlash('info', "Account status toggled.");
        } else {
            setFlash('warning', "You cannot deactivate your own active session.");
        }
        header('Location: ' . ADMIN_URL . '/users.php');
        exit;
    }
}

$stmt = $db->query("SELECT u.user_id, u.email, u.role, u.is_active, u.created_at, u.last_login,
                           v.fname as v_fname, v.lname as v_lname, v.phone as v_phone,
                           a.fname as a_fname, a.lname as a_lname, a.phone as a_phone, a.department
                    FROM users u 
                    LEFT JOIN visitors v ON u.user_id = v.user_id 
                    LEFT JOIN administrators a ON u.user_id = a.user_id 
                    ORDER BY u.user_id DESC");
$allUsers = $stmt->fetchAll();

$pageTitle = "Staff & User Roles Management";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Staff Clearance & User Management</h2>
        <span class="text-muted small">Manage access control levels, user privileges, and staff designations</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#newStaffModal">
            <i class="bi bi-person-plus-fill"></i> Add Staff Account
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted text-uppercase">
                <tr>
                    <th class="ps-3">User ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Role Designation</th>
                    <th>Department / Type</th>
                    <th>Last Login</th>
                    <th>Account Status</th>
                    <th class="pe-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $u): 
                    $uName = $u['role'] === 'visitor' 
                        ? trim(($u['v_fname'] ?? '') . ' ' . ($u['v_lname'] ?? ''))
                        : trim(($u['a_fname'] ?? '') . ' ' . ($u['a_lname'] ?? ''));
                    if (empty($uName)) $uName = 'User';

                    $roleBadge = match($u['role']) {
                        'admin' => 'bg-danger',
                        'finance' => 'bg-primary',
                        'event_manager' => 'bg-warning text-dark',
                        'guide' => 'bg-info text-dark',
                        'supplier' => 'bg-secondary',
                        default => 'bg-success'
                    };
                ?>
                <tr>
                    <td class="ps-3 font-monospace fw-bold text-dark">#<?php echo $u['user_id']; ?></td>
                    <td><strong class="text-dark"><?php echo e($uName); ?></strong></td>
                    <td><code><?php echo e($u['email']); ?></code></td>
                    <td><span class="badge <?php echo $roleBadge; ?>"><?php echo strtoupper(str_replace('_', ' ', $u['role'])); ?></span></td>
                    <td><?php echo e($u['department'] ?: 'Visitor Account'); ?></td>
                    <td><?php echo $u['last_login'] ? date('M d, H:i', strtotime($u['last_login'])) : 'Never'; ?></td>
                    <td>
                        <span class="badge <?php echo $u['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $u['is_active'] ? 'ACTIVE' : 'SUSPENDED'; ?>
                        </span>
                    </td>
                    <td class="pe-3 text-end">
                        <?php if ($u['user_id'] !== $currentStaff['user_id']): ?>
                        <form action="<?php echo ADMIN_URL; ?>/users.php" method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <input type="hidden" name="current_active" value="<?php echo $u['is_active']; ?>">
                            <button type="submit" name="action_toggle_active" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                                <?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                        <?php else: ?>
                            <span class="text-muted small">Current Session</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: New Staff Member -->
<div class="modal fade" id="newStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary-mv text-white p-4">
                <h5 class="modal-title fw-bold">Register New Staff Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo ADMIN_URL; ?>/users.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action_create_staff" value="1">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Staff Member Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Christine Mutua" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Work Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="c.mutua@mambavillage.co.ke" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Contact Phone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+254 722 000 000" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Assigned Role</label>
                            <select name="role" class="form-select">
                                <option value="admin">Administrator</option>
                                <option value="finance">Finance Manager</option>
                                <option value="event_manager">Event Manager</option>
                                <option value="guide" selected>Tour Guide</option>
                                <option value="supplier">Supply Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Initial Password</label>
                        <input type="password" name="password" class="form-control" value="Staff@2026" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mv-primary btn-sm fw-bold">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
