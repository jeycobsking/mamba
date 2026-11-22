<?php
/**
 * Automated Database Installer & Data Seeder
 * Mamba Village Nairobi Management System
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$output = [];
$error = null;

try {
    $db = getDB();
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 1. Execute Schema
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $db->exec($schemaSql);
    $output[] = "Database schema initialized successfully.";

    // 2. Clear & Seed System Users
    $passwordCommon = password_hash('Admin@2026', PASSWORD_BCRYPT);
    $visitorPassword = password_hash('Visitor@2026', PASSWORD_BCRYPT);

    $usersToSeed = [
        ['admin@mambavillage.co.ke', $passwordCommon, 'admin', 'System Administrator', '0722111222'],
        ['finance@mambavillage.co.ke', $passwordCommon, 'finance', 'Head of Accounts & Revenue', '0722333444'],
        ['events@mambavillage.co.ke', $passwordCommon, 'event_manager', 'Wildlife Shows & Operations Manager', '0722555666'],
        ['guide@mambavillage.co.ke', $passwordCommon, 'guide', 'Chief Herpetology Guide', '0722777888'],
        ['supplier@mambavillage.co.ke', $passwordCommon, 'supplier', 'Supply Chain Superintendent', '0722999000'],
        ['visitor@gmail.com', $visitorPassword, 'visitor', 'Faith Wanjiku', '0712345678']
    ];

    foreach ($usersToSeed as $u) {
        $check = $db->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->execute([$u[0]]);
        $existing = $check->fetch();

        if (!$existing) {
            $ins = $db->prepare("INSERT INTO users (email, password, role, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
            $ins->execute([$u[0], $u[1], $u[2]]);
            $userId = $db->lastInsertId();

            if ($u[2] === 'visitor') {
                $vIns = $db->prepare("INSERT INTO visitors (user_id, fname, lname, phone, country) VALUES (?, ?, ?, ?, 'Kenya')");
                $parts = explode(' ', $u[3]);
                $vIns->execute([$userId, $parts[0], $parts[1] ?? '', $u[4]]);
            } else {
                $parts = explode(' ', $u[3]);
                $aIns = $db->prepare("INSERT INTO administrators (user_id, fname, lname, phone, department, position, access_level) VALUES (?, ?, ?, ?, ?, ?, 'super_admin')");
                $aIns->execute([$userId, $parts[0], $parts[1] ?? 'Officer', $u[4], ucfirst(str_replace('_', ' ', $u[2])) . ' Department', 'Manager']);
            }
        }
    }
    $output[] = "Standard system users and staff accounts verified.";

    // 3. Seed Attractions with Real Photography Images
    $db->exec("DELETE FROM attractions");
    $attractionsData = [
        [
            'Giant Nile Crocodile Sanctuary',
            'crocodile',
            'Home to over 70 massive Nile crocodiles spanning breeding pens, hatchling nurseries, and elevated predator observation decks. Witness live feeding shows led by seasoned keepers.',
            'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=800&q=80',
            'Daily: 9:00 AM – 6:00 PM',
            'Most Popular',
            'fa-solid fa-water',
            1
        ],
        [
            'Lake Mamba Scenic Boat Cruise',
            'ride',
            'Cruise across our tranquil freshwater lake in pedal boats or escorted safety launches. Surrounded by peaceful flora, water lilies, and diverse indigenous birdlife.',
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
            'Daily: 10:00 AM – 5:30 PM',
            'Family Favorite',
            'fa-solid fa-ship',
            1
        ],
        [
            'Ostrich Interaction & Farm',
            'ostrich',
            'Interact safely with towering East African ostriches. Learn about ostrich biology, observe giant clutch eggs, and hand-feed these magnificent flightless birds.',
            'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80',
            'Daily: 9:30 AM – 5:30 PM',
            'Interactive',
            'fa-solid fa-feather-pointed',
            1
        ],
        [
            'Giant Tortoise Haven',
            'tortoise',
            'Observe majestic Aldabra and leopard tortoises, some exceeding 100 years of age, grazing peacefully in their dedicated lush grass sanctuary enclosures.',
            'https://images.unsplash.com/photo-1508817628294-5a453fa0b8fb?auto=format&fit=crop&w=800&q=80',
            'Daily: 9:00 AM – 5:30 PM',
            'Heritage',
            'fa-solid fa-shield-halved',
            1
        ],
        [
            'Indigenous Botanical Canopy Gardens',
            'garden',
            'Stroll through over 15 acres of curated indigenous Kenyan flora, medicinal forest walkways, rare tropical orchids, and serene reading gazebos.',
            'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=800&q=80',
            'Daily: 9:00 AM – 6:00 PM',
            'Nature Walk',
            'fa-solid fa-tree',
            1
        ],
        [
            'Garden Restaurant & Nyama Choma',
            'restaurant',
            'Enjoy authentic Kenyan culinary hospitality featuring prime grilled meats (Nyama Choma), traditional side dishes, cool refreshments, and our signature exotic crocodile delicacies.',
            'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80',
            'Daily: 11:00 AM – 7:00 PM',
            'Prime Dining',
            'fa-solid fa-utensils',
            1
        ]
    ];

    $attStmt = $db->prepare("INSERT INTO attractions (name, category, description, image_url, hours, badge, icon_class, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    foreach ($attractionsData as $item) {
        $attStmt->execute($item);
    }
    $output[] = "Real exhibits & attractions seeded.";

    // 4. Seed Ticket Types
    $db->exec("DELETE FROM ticket_types");
    $ticketTypesData = [
        ['Kenyan Citizen / Resident Adult', 'General park admission for citizens and residents aged 13+', 500.00, 'citizen_adult'],
        ['Kenyan Citizen / Resident Child', 'Admission for children aged 3 to 12 years (Under 3 free)', 300.00, 'citizen_child'],
        ['International Tourist Adult', 'Full day international visitor entry pass', 2000.00, 'non_resident'],
        ['Student ID Pass', 'Valid student ID card holders and high school groups', 250.00, 'student'],
        ['Lake Mamba Boat Ride Pass', 'Scenic pedal boat or launch cruise per person', 300.00, 'addon'],
        ['VIP Feeding Platform Pass', 'Elevated feeding bridge access during 2:30 PM feeding', 500.00, 'addon'],
        ['Dedicated Private Tour Guide', 'Certified private naturalist guide for group (up to 10 pax)', 1000.00, 'addon']
    ];

    $ttStmt = $db->prepare("INSERT INTO ticket_types (name, description, price, category, is_active) VALUES (?, ?, ?, ?, 1)");
    foreach ($ticketTypesData as $tt) {
        $ttStmt->execute($tt);
    }
    $output[] = "Ticket pricing tiers and add-ons configured.";

    // 5. Seed Feeding Shows & Events Schedule
    $db->exec("DELETE FROM events");
    $eventsData = [
        ['10:00 AM', 'Guided Botanical Canopy & Medicinal Plant Tour', 'tour', 'Botanical Zone D', 'Peter Githinji (Naturalist)', '35 Persons', 'Educational walkthrough exploring indigenous flora and medicinal remedies.', 0],
        ['11:30 AM', 'Ostrich Interaction & Giant Egg Exhibition', 'feeding', 'Ostrich Enclosure C', 'Evans Mwangi (Keeper)', '50 Persons', 'Hands-on feeding and biology lecture regarding flightless birds.', 0],
        ['01:00 PM', 'Giant Tortoise Encounter', 'family', 'Tortoise Lawn C', 'Jane Wambui (Guide)', '40 Persons', 'Safe close encounter and photo opportunities with hundred-year-old tortoises.', 0],
        ['02:30 PM', 'HIGHLIGHT: Nile Crocodile Feeding Frenzy (Main Show)', 'feeding', 'Crocodile Pits 1 & 2', 'Samuel Kiprop (Head Keeper)', '180 Persons', 'Our headline spectacle. Watch massive Nile crocodiles surge from the water for meat.', 1],
        ['03:30 PM', 'Lake Mamba Scenic Boat Regatta', 'family', 'Boat Jetty Zone B', 'Skipper Kevin', '25 Persons', 'Guided water cruise around sanctuary lake exploring wetland bird species.', 0],
        ['04:30 PM', 'HIGHLIGHT: Late Afternoon Crocodile Feeding Showcase', 'feeding', 'Intermediate Ponds Zone A', 'Samuel Kiprop (Head Keeper)', '120 Persons', 'Secondary feeding presentation focused on intermediate and sub-adult crocodiles.', 1]
    ];

    $evStmt = $db->prepare("INSERT INTO events (event_time, title, category, location, keeper, capacity, description, is_highlight, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'scheduled')");
    foreach ($eventsData as $ev) {
        $evStmt->execute($ev);
    }
    $output[] = "Daily feeding and show schedules populated.";

    // 6. Seed Tour Guides
    $db->exec("DELETE FROM tour_guides");
    $guidesData = [
        ['John Kiprono', '+254 711 234 567', 'Apex Reptiles & Crocodile Ecology', 'St. Austin High School (45 students)', 'on_tour'],
        ['Mary Muthoni', '+254 722 345 678', 'Herpetology & Multilingual Safari Guidance', 'European Delegation (6 pax)', 'available'],
        ['Evans Otieno', '+254 733 456 789', 'Botanical Conservation & Ornithology', 'Family Safari Group (4 pax)', 'on_tour'],
        ['Grace Cherono', '+254 744 567 890', 'Lake Ecology & Child Safety Orientation', 'Jetty Standby', 'available']
    ];
    $gStmt = $db->prepare("INSERT INTO tour_guides (name, phone, specialty, assigned_tour, status) VALUES (?, ?, ?, ?, ?)");
    foreach ($guidesData as $g) {
        $gStmt->execute($g);
    }
    $output[] = "Certified tour guide roster initialized.";

    // 7. Seed Supplier Orders
    $db->exec("DELETE FROM supplier_orders");
    $ordersData = [
        ['PO-MV-2026-081', 'Karen Fresh Meats Wholesalers', '250 KG Fresh Beef & Fish (Crocodile Rations)', '250 KG', date('Y-m-d', strtotime('+1 day')), 'in_transit'],
        ['PO-MV-2026-079', 'SafeFloat Marine Equipment Kenya', '15 Certified Adult & Child Life Vests', '15 Units', date('Y-m-d', strtotime('-2 days')), 'delivered'],
        ['PO-MV-2026-083', 'Reptile Health East Africa', 'Calcium & Multivitamin Supplement Powders', '24 Tins', date('Y-m-d', strtotime('+3 days')), 'pending']
    ];
    $oStmt = $db->prepare("INSERT INTO supplier_orders (po_number, supplier_name, item_description, quantity, expected_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($ordersData as $o) {
        $oStmt->execute($o);
    }
    $output[] = "Supply chain orders initialized.";

    // 8. Seed Sample Bookings
    $db->exec("DELETE FROM bookings");
    $bookingsData = [
        [
            6, // visitor user_id
            'MV-2026-8942',
            date('Y-m-d'),
            'Afternoon Session & Feeding Show',
            2, 1, 0, 0,
            1, 1, 0,
            2400.00,
            'M-PESA STK Push',
            'SJL79X2Q9B',
            'paid',
            'confirmed'
        ],
        [
            6,
            'MV-2026-7115',
            date('Y-m-d', strtotime('-1 day')),
            'Morning Session',
            1, 0, 0, 1,
            0, 0, 0,
            750.00,
            'M-PESA STK Push',
            'SJL81A44KL',
            'paid',
            'admitted'
        ]
    ];
    $bStmt = $db->prepare("INSERT INTO bookings (user_id, booking_reference, visit_date, session_slot, adults_qty, children_qty, non_resident_qty, student_qty, has_boat_ride, has_vip_platform, has_guide, total_amount, payment_method, payment_reference, payment_status, gate_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    foreach ($bookingsData as $b) {
        $bStmt->execute($b);
    }
    $output[] = "Sample verified bookings with M-Pesa references generated.";

    // 9. Seed Feedback & Testimonials
    $db->exec("DELETE FROM feedback");
    $feedbackData = [
        ['Faith Wanjiku', 5, 'Crocodile Feeding Show', 'Standing on the upper gallery bridge during the 2:30 PM feeding is electrifying! The keepers are remarkably professional.'],
        ['Brian Ochieng', 5, 'E-Ticketing & Gate Entry', 'Booking online took under 60 seconds with M-Pesa STK push. Scanned our digital QR code at the turnstile without waiting in queues.'],
        ['Sarah Jenkins', 4, 'Lake Mamba Boat Cruise', 'Very peaceful boat ride on Lake Mamba and beautiful botanical gardens. Food at the restaurant was delicious too.'],
        ['Dr. James Kariuki', 5, 'Educational Tour Guidance', 'Guide John was exceptionally knowledgeable on reptile biology. Our visiting student group gained tremendous insight.']
    ];
    $fbStmt = $db->prepare("INSERT INTO feedback (visitor_name, rating, category, comment, status) VALUES (?, ?, ?, ?, 'approved')");
    foreach ($feedbackData as $fb) {
        $fbStmt->execute($fb);
    }
    $output[] = "Approved customer reviews seeded.";
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = "System Database Installer";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary-mv text-white p-4 text-center">
                    <div class="brand-mark bg-white text-success mx-auto mb-2">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Mamba Village Database Setup</h3>
                    <p class="text-white-50 small mb-0">System Initialization & Schema Synchronization</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4">
                            <i class="bi bi-x-octagon-fill fs-3 me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Installation Failed</h5>
                                <p class="mb-0 small"><?php echo e($error); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                            <div>
                                <h5 class="fw-bold mb-1 text-success">Database Initialized Successfully!</h5>
                                <p class="mb-0 small text-success">All tables, constraints, real images, and authentication accounts are active.</p>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Execution Log</h6>
                        <ul class="list-group list-group-flush mb-4 border rounded">
                            <?php foreach ($output as $line): ?>
                                <li class="list-group-item d-flex align-items-center py-2 small">
                                    <i class="bi bi-check2 text-success me-2 fw-bold"></i>
                                    <span><?php echo e($line); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="p-4 bg-light rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-key-fill text-gold me-2"></i>Default System Credentials</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Role</th>
                                            <th>Email Address</th>
                                            <th>Password</th>
                                            <th>Portal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-danger">Administrator</span></td>
                                            <td><code>admin@mambavillage.co.ke</code></td>
                                            <td><code>Admin@2026</code></td>
                                            <td><a href="<?php echo ADMIN_URL; ?>/login.php" class="text-primary fw-semibold">Staff Portal</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-primary">Finance Manager</span></td>
                                            <td><code>finance@mambavillage.co.ke</code></td>
                                            <td><code>Admin@2026</code></td>
                                            <td><a href="<?php echo ADMIN_URL; ?>/login.php" class="text-primary fw-semibold">Staff Portal</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">Event Manager</span></td>
                                            <td><code>events@mambavillage.co.ke</code></td>
                                            <td><code>Admin@2026</code></td>
                                            <td><a href="<?php echo ADMIN_URL; ?>/login.php" class="text-primary fw-semibold">Staff Portal</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-info text-dark">Tour Guide</span></td>
                                            <td><code>guide@mambavillage.co.ke</code></td>
                                            <td><code>Admin@2026</code></td>
                                            <td><a href="<?php echo ADMIN_URL; ?>/login.php" class="text-primary fw-semibold">Staff Portal</a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">Visitor / Customer</span></td>
                                            <td><code>visitor@gmail.com</code></td>
                                            <td><code>Visitor@2026</code></td>
                                            <td><a href="<?php echo BASE_URL; ?>/login.php" class="text-success fw-semibold">Visitor Login</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-between">
                            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house me-1"></i> Public Website Home
                            </a>
                            <div class="d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-mv-primary">
                                    <i class="bi bi-person-circle me-1"></i> Visitor Login
                                </a>
                                <a href="<?php echo ADMIN_URL; ?>/login.php" class="btn btn-mv-gold">
                                    <i class="bi bi-shield-lock me-1"></i> Admin Portal
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
