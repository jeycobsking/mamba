<?php
/**
 * Supplier Logistics & Inventory Deliveries Module
 */
require_once __DIR__ . '/includes/admin_auth.php';
requireStaffRole(['admin', 'supplier']);

$db = getDB();

// Handle Advance Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_advance_status'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $oid = (int)$_POST['order_id'];
        $curr = $_POST['current_status'] ?? 'pending';
        $next = match($curr) {
            'pending' => 'in_transit',
            'in_transit' => 'delivered',
            default => 'pending'
        };

        $up = $db->prepare("UPDATE supplier_orders SET status = ? WHERE order_id = ?");
        $up->execute([$next, $oid]);
        
        logActivity('SUPPLY_STATUS', "Order ID #{$oid} status changed to {$next}", $currentStaff['user_id']);
        setFlash('success', "Order status updated to " . strtoupper(str_replace('_', ' ', $next)));
        header('Location: ' . ADMIN_URL . '/suppliers.php');
        exit;
    }
}

// Handle New Supply Order Requisition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_new_order'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $supplier = trim($_POST['supplier_name'] ?? '');
        $item = trim($_POST['item_description'] ?? '');
        $qty = trim($_POST['quantity'] ?? '');
        $date = $_POST['expected_date'] ?? date('Y-m-d');
        $poNumber = 'PO-MV-' . date('Y') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);

        if (!empty($supplier) && !empty($item)) {
            $stmt = $db->prepare("INSERT INTO supplier_orders (po_number, supplier_name, item_description, quantity, expected_date, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$poNumber, $supplier, $item, $qty, $date]);
            
            logActivity('SUPPLY_ORDER_CREATED', "PO {$poNumber} created for {$supplier}", $currentStaff['user_id']);
            setFlash('success', "Purchase requisition {$poNumber} issued successfully.");
            header('Location: ' . ADMIN_URL . '/suppliers.php');
            exit;
        }
    }
}

$stmt = $db->query("SELECT * FROM supplier_orders ORDER BY order_id DESC");
$orders = $stmt->fetchAll();

$pageTitle = "Supplier Logistics & Deliveries";
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/includes/admin_sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Park Supply Requisitions & Deliveries</h2>
        <span class="text-muted small">Manage reptile dietary rations (beef & fish), marine life vests, and veterinary medicine orders</span>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-mv-gold btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#newOrderModal">
            <i class="bi bi-plus-circle"></i> Issue New Purchase Order
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted text-uppercase">
                <tr>
                    <th class="ps-3">PO Number</th>
                    <th>Supplier Partner</th>
                    <th>Material Description</th>
                    <th>Requisition Quantity</th>
                    <th>Expected Delivery</th>
                    <th>Status</th>
                    <th class="pe-3 text-end">Update Progress</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="ps-3 font-monospace fw-bold text-dark"><?php echo e($o['po_number']); ?></td>
                    <td><strong class="text-dark"><?php echo e($o['supplier_name']); ?></strong></td>
                    <td><?php echo e($o['item_description']); ?></td>
                    <td><span class="badge bg-light text-dark border"><?php echo e($o['quantity']); ?></span></td>
                    <td><?php echo e($o['expected_date']); ?></td>
                    <td>
                        <?php 
                        $statusClass = match($o['status']) {
                            'delivered' => 'bg-success',
                            'in_transit' => 'bg-warning text-dark',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $statusClass; ?>">
                            <?php echo strtoupper(str_replace('_', ' ', $o['status'])); ?>
                        </span>
                    </td>
                    <td class="pe-3 text-end">
                        <form action="<?php echo ADMIN_URL; ?>/suppliers.php" method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                            <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo e($o['status']); ?>">
                            <button type="submit" name="action_advance_status" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-arrow-repeat"></i> Advance Stage
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: New Supply PO -->
<div class="modal fade" id="newOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary-mv text-white p-4">
                <h5 class="modal-title fw-bold">Issue Supply Requisition PO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo ADMIN_URL; ?>/suppliers.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action_new_order" value="1">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Supplier Company Name</label>
                        <input type="text" name="supplier_name" class="form-control" placeholder="e.g. Karen Fishery & Meat Supplies Ltd" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Material / Food Item Description</label>
                        <input type="text" name="item_description" class="form-control" placeholder="e.g. 200 KG Fresh Tilapia & Beef Cuts for Crocodile Pits" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Requisition Quantity</label>
                            <input type="text" name="quantity" class="form-control" placeholder="e.g. 200 KG" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Target Delivery Date</label>
                            <input type="date" name="expected_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mv-primary btn-sm fw-bold">Submit Purchase Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
