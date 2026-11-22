<?php
/**
 * Event & Feeding Timetable Management Module
 */
require_once __DIR__ . '/includes/admin_auth.php';
requireStaffRole(['admin', 'event_manager']);

$db = getDB();

// Handle Add Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_add_event'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $time = trim($_POST['event_time'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'feeding';
        $location = trim($_POST['location'] ?? 'Crocodile Pits');
        $keeper = trim($_POST['keeper'] ?? 'Samuel Kiprop');
        $capacity = trim($_POST['capacity'] ?? '50 Persons');
        $desc = trim($_POST['description'] ?? '');
        $isHighlight = isset($_POST['is_highlight']) ? 1 : 0;

        if (!empty($time) && !empty($title)) {
            $stmt = $db->prepare("INSERT INTO events (event_time, title, category, location, keeper, capacity, description, is_highlight, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
            $stmt->execute([$time, $title, $category, $location, $keeper, $capacity, $desc, $isHighlight]);
            
            logActivity('EVENT_CREATED', "New show '{$title}' scheduled at {$time}", $currentStaff['user_id']);
            setFlash('success', "Event slot '{$title}' created successfully.");
            header('Location: ' . ADMIN_URL . '/events.php');
            exit;
        }
    }
}

// Handle Delete Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_delete_event'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $eid = (int)$_POST['event_id'];
        $del = $db->prepare("DELETE FROM events WHERE event_id = ?");
        $del->execute([$eid]);
        logActivity('EVENT_DELETED', "Event ID #{$eid} deleted from schedule", $currentStaff['user_id']);
        setFlash('info', "Show slot removed from schedule.");
        header('Location: ' . ADMIN_URL . '/events.php');
        exit;
    }
}

$events = getEventsSchedule();

$pageTitle = "Feeding & Shows Scheduler";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Feeding Shows & Event Scheduler</h2>
        <span class="text-muted small">Manage live demonstrations, crocodile feeding sessions, and naturalist talks</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bi bi-plus-circle"></i> Add New Show Slot
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted text-uppercase">
                <tr>
                    <th class="ps-3">Time Slot</th>
                    <th>Demonstration Title</th>
                    <th>Category</th>
                    <th>Sanctuary Location</th>
                    <th>Lead Curator</th>
                    <th>Audience Capacity</th>
                    <th>Feature</th>
                    <th class="pe-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                <tr>
                    <td class="ps-3 font-monospace fw-bold text-dark"><?php echo e($e['event_time']); ?></td>
                    <td>
                        <strong class="text-dark d-block"><?php echo e($e['title']); ?></strong>
                        <small class="text-muted"><?php echo e($e['description']); ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?php echo e($e['category']); ?></span></td>
                    <td><span class="text-secondary"><i class="bi bi-geo-alt me-1"></i><?php echo e($e['location']); ?></span></td>
                    <td><?php echo e($e['keeper']); ?></td>
                    <td><?php echo e($e['capacity']); ?></td>
                    <td>
                        <?php if ($e['is_highlight']): ?>
                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Highlight</span>
                        <?php else: ?>
                            <span class="text-muted small">Standard</span>
                        <?php endif; ?>
                    </td>
                    <td class="pe-3 text-end">
                        <form action="<?php echo ADMIN_URL; ?>/events.php" method="POST" class="d-inline" onsubmit="return confirm('Remove this show from park timetable?');">
                            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                            <input type="hidden" name="event_id" value="<?php echo $e['event_id']; ?>">
                            <button type="submit" name="action_delete_event" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add Show Slot -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary-mv text-white p-4">
                <h5 class="modal-title fw-bold">Schedule New Wildlife Demonstration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo ADMIN_URL; ?>/events.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action_add_event" value="1">

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Scheduled Time</label>
                            <input type="text" name="event_time" class="form-control" placeholder="e.g. 02:30 PM" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Category</label>
                            <select name="category" class="form-select">
                                <option value="feeding">Feeding Show</option>
                                <option value="tour">Guided Tour</option>
                                <option value="family">Family & Kids</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Event / Demonstration Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Baby Crocodile Nursery Examination" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Park Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Nursery Ponds" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Lead Curator / Keeper</label>
                            <input type="text" name="keeper" class="form-control" placeholder="e.g. Samuel Kiprop" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Audience Limit</label>
                        <input type="text" name="capacity" class="form-control" placeholder="e.g. 80 Persons" value="60 Persons" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Demonstration Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief notes about the demonstration..."></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_highlight" id="is_highlight" value="1">
                        <label class="form-check-label small fw-semibold" for="is_highlight">
                            Mark as Highlight Spectacle (Featured with gold badge on public schedule)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mv-primary btn-sm fw-bold">Publish to Timetable</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
