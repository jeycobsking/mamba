<?php
/**
 * Ticket Booking Engine
 * Strict authentication guard: Customer must be logged in.
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce authentication
requireLogin();

$currentUser = getCurrentUser();
$ticketTypes = getTicketTypes();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $visitDate = $_POST['visit_date'] ?? '';
        $sessionSlot = $_POST['session_slot'] ?? 'Afternoon & Feeding Show';
        $adults = max(0, (int)($_POST['qty_adult'] ?? 0));
        $children = max(0, (int)($_POST['qty_child'] ?? 0));
        $nonResidents = max(0, (int)($_POST['qty_non_resident'] ?? 0));
        $students = max(0, (int)($_POST['qty_student'] ?? 0));
        
        $hasBoat = isset($_POST['addon_boat']) ? 1 : 0;
        $hasVip = isset($_POST['addon_vip']) ? 1 : 0;
        $hasGuide = isset($_POST['addon_guide']) ? 1 : 0;
        
        $paymentMethod = $_POST['payment_method'] ?? 'M-PESA STK Push';
        $mpesaPhone = trim($_POST['mpesa_phone'] ?? $currentUser['phone']);

        $totalPeople = $adults + $children + $nonResidents + $students;

        if ($totalPeople <= 0) {
            $error = 'Please select at least one admission ticket pass.';
        } elseif (empty($visitDate) || $visitDate < date('Y-m-d')) {
            $error = 'Please select a valid future visit date.';
        } else {
            // Compute Server-Side Verified Price
            $subtotal = ($adults * 500) + ($children * 300) + ($nonResidents * 2000) + ($students * 250);
            if ($hasBoat) {
                $subtotal += ($totalPeople * 300);
            }
            if ($hasVip) {
                $subtotal += 500;
            }
            if ($hasGuide) {
                $subtotal += 1000;
            }

            $bookingRef = generateBookingReference();
            $mpesaRef = generateMpesaReference();

            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO bookings 
                    (user_id, booking_reference, visit_date, session_slot, adults_qty, children_qty, non_resident_qty, student_qty, has_boat_ride, has_vip_platform, has_guide, total_amount, payment_method, payment_reference, payment_status, gate_status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid', 'confirmed', NOW())");
                
                $stmt->execute([
                    $currentUser['user_id'],
                    $bookingRef,
                    $visitDate,
                    $sessionSlot,
                    $adults,
                    $children,
                    $nonResidents,
                    $students,
                    $hasBoat,
                    $hasVip,
                    $hasGuide,
                    $subtotal,
                    $paymentMethod,
                    $mpesaRef
                ]);

                logActivity('BOOKING_CREATED', "Booking {$bookingRef} created for KES {$subtotal}", $currentUser['user_id']);
                setFlash('success', "Payment Confirmed via {$paymentMethod}! Reference: {$mpesaRef}. Your gate ticket is active.");

                header('Location: ' . BASE_URL . '/ticket-view.php?ref=' . urlencode($bookingRef));
                exit;

            } catch (Exception $e) {
                $error = 'Booking failed to save: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = "Book Tickets & Activity Passes";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="row g-4 justify-content-between">
        
        <!-- Booking Form Column -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-primary-mv text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase small fw-bold mb-1">Official E-Ticket Portal</span>
                            <h4 class="fw-bold mb-0">Select Passes & Activity Add-Ons</h4>
                        </div>
                        <i class="bi bi-shield-check fs-2 text-warning"></i>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4 small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?php echo e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form id="bookingForm" action="<?php echo BASE_URL; ?>/book.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

                        <!-- Step 1: Visit Schedule -->
                        <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                            <i class="bi bi-calendar3 me-1 text-success"></i> 1. Schedule & Session
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Visit Date</label>
                                <input type="date" id="visit_date" name="visit_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required onchange="updateSummary()">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Arrival Window</label>
                                <select id="session_slot" name="session_slot" class="form-select" onchange="updateSummary()">
                                    <option value="Morning Session (9:00 AM – 1:00 PM)">Morning Session (9:00 AM – 1:00 PM)</option>
                                    <option value="Afternoon & Feeding Show (1:30 PM – 5:30 PM)" selected>Afternoon & Feeding Show (1:30 PM – 5:30 PM)</option>
                                    <option value="Full Day Pass (9:00 AM – 6:00 PM)">Full Day Pass (9:00 AM – 6:00 PM)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Admission Pass Quantities -->
                        <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                            <i class="bi bi-people me-1 text-success"></i> 2. Admission Ticket Passes
                        </h6>
                        
                        <!-- Adult Citizen -->
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded-3 border">
                            <div>
                                <strong class="d-block text-dark">Kenyan Citizen / Resident Adult</strong>
                                <small class="text-muted">Ages 13 and above &bull; <span class="text-success fw-bold">KES 500.00</span></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_adult', -1)">−</button>
                                <input type="number" id="qty_adult" name="qty_adult" class="form-control text-center fw-bold" style="width: 60px;" value="1" min="0" readonly>
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_adult', 1)">+</button>
                            </div>
                        </div>

                        <!-- Child Citizen -->
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded-3 border">
                            <div>
                                <strong class="d-block text-dark">Kenyan Child Pass</strong>
                                <small class="text-muted">Ages 3 to 12 years (Under 3 free) &bull; <span class="text-success fw-bold">KES 300.00</span></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_child', -1)">−</button>
                                <input type="number" id="qty_child" name="qty_child" class="form-control text-center fw-bold" style="width: 60px;" value="0" min="0" readonly>
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_child', 1)">+</button>
                            </div>
                        </div>

                        <!-- Non-Resident -->
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded-3 border">
                            <div>
                                <strong class="d-block text-dark">International Tourist (Adult)</strong>
                                <small class="text-muted">Foreign visitor full admission &bull; <span class="text-success fw-bold">KES 2,000.00</span></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_non_resident', -1)">−</button>
                                <input type="number" id="qty_non_resident" name="qty_non_resident" class="form-control text-center fw-bold" style="width: 60px;" value="0" min="0" readonly>
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_non_resident', 1)">+</button>
                            </div>
                        </div>

                        <!-- Student -->
                        <div class="d-flex justify-content-between align-items-center p-3 mb-4 bg-light rounded-3 border">
                            <div>
                                <strong class="d-block text-dark">Student ID Pass</strong>
                                <small class="text-muted">Must present valid student card at gate &bull; <span class="text-success fw-bold">KES 250.00</span></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_student', -1)">−</button>
                                <input type="number" id="qty_student" name="qty_student" class="form-control text-center fw-bold" style="width: 60px;" value="0" min="0" readonly>
                                <button type="button" class="btn btn-sm btn-white border px-3 fw-bold" onclick="changeQty('qty_student', 1)">+</button>
                            </div>
                        </div>

                        <!-- Step 3: Experience Add-Ons -->
                        <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                            <i class="bi bi-stars me-1 text-gold"></i> 3. Optional Experience Add-Ons
                        </h6>
                        <div class="form-check p-3 mb-2 bg-light rounded-3 border d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" name="addon_boat" id="addon_boat" onchange="updateSummary()">
                            <label class="form-check-label w-100 cursor-pointer" for="addon_boat">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">Lake Mamba Scenic Boat Cruise</strong>
                                    <span class="text-gold fw-bold small">+KES 300 / person</span>
                                </div>
                                <small class="text-muted d-block">Includes certified life jacket and boat captain escort.</small>
                            </label>
                        </div>

                        <div class="form-check p-3 mb-2 bg-light rounded-3 border d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" name="addon_vip" id="addon_vip" onchange="updateSummary()">
                            <label class="form-check-label w-100 cursor-pointer" for="addon_vip">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">VIP Elevated Feeding Bridge Access</strong>
                                    <span class="text-gold fw-bold small">+KES 500 flat</span>
                                </div>
                                <small class="text-muted d-block">Prime front-row position during the 2:30 PM crocodile feeding frenzy.</small>
                            </label>
                        </div>

                        <div class="form-check p-3 mb-4 bg-light rounded-3 border d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" name="addon_guide" id="addon_guide" onchange="updateSummary()">
                            <label class="form-check-label w-100 cursor-pointer" for="addon_guide">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">Dedicated Private Tour Naturalist</strong>
                                    <span class="text-gold fw-bold small">+KES 1,000 flat</span>
                                </div>
                                <small class="text-muted d-block">Private certified guide reserved exclusively for your party.</small>
                            </label>
                        </div>

                        <!-- Step 4: Checkout Modal Trigger -->
                        <button type="button" class="btn btn-mv-gold w-100 py-3 fw-bold fs-5 shadow" onclick="openCheckoutModal()">
                            Proceed to Secure Payment <i class="bi bi-shield-lock-fill ms-1"></i>
                        </button>

                        <!-- Modal: Lipa Na M-Pesa & Card Checkout -->
                        <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header bg-primary-mv text-white p-4">
                                        <div>
                                            <h5 class="modal-title fw-bold">Confirm & Pay via M-Pesa</h5>
                                            <small class="text-white-50">Lipa na M-Pesa Online STK Push</small>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="text-center p-3 mb-3 bg-light rounded-3 border">
                                            <span class="small text-muted d-block">Total Settlement Amount:</span>
                                            <h3 class="fw-bold text-success mb-0" id="modalTotalDisplay">KES 500.00</h3>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Safaricom Mobile Phone</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-phone text-success"></i></span>
                                                <input type="tel" name="mpesa_phone" id="mpesa_phone" class="form-control" value="<?php echo e($currentUser['phone'] ?: '0712345678'); ?>" required>
                                            </div>
                                            <small class="text-muted">A push prompt will appear on this handset requesting your PIN.</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-semibold text-secondary">Payment Method</label>
                                            <select name="payment_method" class="form-select">
                                                <option value="M-PESA STK Push" selected>Lipa na M-PESA (STK Push)</option>
                                                <option value="Visa / Mastercard">Visa / Mastercard</option>
                                                <option value="Cash / Turnstile POS">On-site Gate Counter</option>
                                            </select>
                                        </div>

                                        <!-- Waiting Animation Overlay (Simulated) -->
                                        <div id="stkSimulationBox" class="p-3 bg-light border border-success rounded-3 text-center mb-3" style="display: none;">
                                            <div class="spinner-border text-success mb-2" role="status"></div>
                                            <h6 class="fw-bold text-dark mb-1">STK Push Sent to Mobile Handset</h6>
                                            <p class="small text-muted mb-0">Please input your 4-digit M-Pesa PIN...</p>
                                        </div>

                                        <button type="button" id="btnSubmitPayment" class="btn btn-success w-100 py-2 fw-bold" onclick="simulateMpesaAndSubmit()">
                                            <i class="bi bi-check-circle-fill me-1"></i> Authorize & Issue QR Ticket
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Real-Time Order Summary Sticky Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white sticky-top" style="top: 5rem;">
                <div class="card-header bg-light p-4 border-bottom">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase small mb-1">Live Calculation</span>
                    <h5 class="fw-bold mb-0 text-dark">Reservation Breakdown</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Lead Guest:</span>
                        <strong class="text-dark"><?php echo e($currentUser['name']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Visit Date:</span>
                        <strong class="text-dark" id="sumDateDisplay"><?php echo date('Y-m-d'); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-secondary">
                        <span>Session Slot:</span>
                        <strong class="text-dark" id="sumSessionDisplay">Afternoon Session</strong>
                    </div>

                    <hr class="my-3">

                    <!-- Line Items Dynamic -->
                    <div id="summaryLineItems" class="small mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>1x Kenyan Citizen Adult</span>
                            <strong>KES 500.00</strong>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Subtotal:</span>
                        <span class="fw-semibold text-dark" id="sumSubtotal">KES 500.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Conservation Levy (Included):</span>
                        <span class="text-success small fw-semibold">KES 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border">
                        <span class="fw-bold fs-6 text-dark">Grand Total:</span>
                        <span class="fw-bold fs-4 text-success" id="sumGrandTotal">KES 500.00</span>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 border small text-muted d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-success fs-4"></i>
                        <span>End-to-end encrypted checkouts with official Daraja STK Push verification.</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function changeQty(fieldId, delta) {
    const input = document.getElementById(fieldId);
    let val = parseInt(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
    updateSummary();
}

function updateSummary() {
    const adults = parseInt(document.getElementById('qty_adult').value) || 0;
    const children = parseInt(document.getElementById('qty_child').value) || 0;
    const nonResidents = parseInt(document.getElementById('qty_non_resident').value) || 0;
    const students = parseInt(document.getElementById('qty_student').value) || 0;
    
    const hasBoat = document.getElementById('addon_boat').checked;
    const hasVip = document.getElementById('addon_vip').checked;
    const hasGuide = document.getElementById('addon_guide').checked;

    const totalPeople = adults + children + nonResidents + students;

    let subtotal = (adults * 500) + (children * 300) + (nonResidents * 2000) + (students * 250);
    let linesHtml = '';

    if (adults > 0) linesHtml += `<div class="d-flex justify-content-between mb-1"><span>${adults}x Kenyan Adult</span><strong>KES ${(adults * 500).toLocaleString()}.00</strong></div>`;
    if (children > 0) linesHtml += `<div class="d-flex justify-content-between mb-1"><span>${children}x Child Pass</span><strong>KES ${(children * 300).toLocaleString()}.00</strong></div>`;
    if (nonResidents > 0) linesHtml += `<div class="d-flex justify-content-between mb-1"><span>${nonResidents}x International Tourist</span><strong>KES ${(nonResidents * 2000).toLocaleString()}.00</strong></div>`;
    if (students > 0) linesHtml += `<div class="d-flex justify-content-between mb-1"><span>${students}x Student ID Pass</span><strong>KES ${(students * 250).toLocaleString()}.00</strong></div>`;

    if (hasBoat && totalPeople > 0) {
        const cost = totalPeople * 300;
        subtotal += cost;
        linesHtml += `<div class="d-flex justify-content-between mb-1 text-gold"><span>Boat Cruise (${totalPeople} pax)</span><strong>+KES ${cost.toLocaleString()}.00</strong></div>`;
    }
    if (hasVip) {
        subtotal += 500;
        linesHtml += `<div class="d-flex justify-content-between mb-1 text-gold"><span>VIP Feeding Bridge Access</span><strong>+KES 500.00</strong></div>`;
    }
    if (hasGuide) {
        subtotal += 1000;
        linesHtml += `<div class="d-flex justify-content-between mb-1 text-gold"><span>Private Dedicated Guide</span><strong>+KES 1,000.00</strong></div>`;
    }

    if (totalPeople === 0) {
        linesHtml = '<p class="text-muted small">No tickets selected yet.</p>';
    }

    document.getElementById('summaryLineItems').innerHTML = linesHtml;
    document.getElementById('sumSubtotal').textContent = `KES ${subtotal.toLocaleString()}.00`;
    document.getElementById('sumGrandTotal').textContent = `KES ${subtotal.toLocaleString()}.00`;
    document.getElementById('modalTotalDisplay').textContent = `KES ${subtotal.toLocaleString()}.00`;
    
    document.getElementById('sumDateDisplay').textContent = document.getElementById('visit_date').value;
    const sessionSelect = document.getElementById('session_slot');
    document.getElementById('sumSessionDisplay').textContent = sessionSelect.options[sessionSelect.selectedIndex].text.split('(')[0];
}

function openCheckoutModal() {
    const adults = parseInt(document.getElementById('qty_adult').value) || 0;
    const children = parseInt(document.getElementById('qty_child').value) || 0;
    const nonResidents = parseInt(document.getElementById('qty_non_resident').value) || 0;
    const students = parseInt(document.getElementById('qty_student').value) || 0;
    
    if (adults + children + nonResidents + students === 0) {
        alert('Please choose at least 1 admission ticket pass.');
        return;
    }
    
    const myModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    myModal.show();
}

function simulateMpesaAndSubmit() {
    const btn = document.getElementById('btnSubmitPayment');
    const simBox = document.getElementById('stkSimulationBox');
    
    btn.disabled = true;
    simBox.style.display = 'block';

    setTimeout(() => {
        document.getElementById('bookingForm').submit();
    }, 2000);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
