<?php
/**
 * Public Site Global Footer
 */
?>
<footer class="bg-primary-dark text-white pt-5 pb-4 mt-auto border-top border-secondary border-opacity-25 no-print">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Brand & Mission -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-mark bg-success">
                        <i class="fa-solid fa-water"></i>
                    </div>
                    <span class="fs-5 fw-bold text-white"><?php echo SITE_NAME; ?></span>
                </div>
                <p class="text-white-50 small mb-3">
                    East Africa's renowned crocodile conservation sanctuary, indigenous botanical gardens, and premier family leisure destination situated in Karen/Langata, Nairobi.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px;"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px;"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase text-gold fw-bold mb-3 small" style="letter-spacing: 0.05em;">Explore Park</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-white-50">
                    <li><a href="<?php echo BASE_URL; ?>/attractions.php" class="text-white-50 text-decoration-none hover:text-white">Crocodile Ponds</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/attractions.php" class="text-white-50 text-decoration-none hover:text-white">Lake Mamba Boats</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/attractions.php" class="text-white-50 text-decoration-none hover:text-white">Ostrich & Tortoises</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/attractions.php" class="text-white-50 text-decoration-none hover:text-white">Botanical Gardens</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/attractions.php" class="text-white-50 text-decoration-none hover:text-white">Garden Restaurant</a></li>
                </ul>
            </div>

            <!-- Visitor Services -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase text-gold fw-bold mb-3 small" style="letter-spacing: 0.05em;">Visitor Services</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-white-50">
                    <li><a href="<?php echo BASE_URL; ?>/book.php" class="text-white-50 text-decoration-none hover:text-white">Online E-Ticketing</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/schedule.php" class="text-white-50 text-decoration-none hover:text-white">Feeding Show Timetable</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/group-booking.php" class="text-white-50 text-decoration-none hover:text-white">School Excursions & Packages</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/reviews.php" class="text-white-50 text-decoration-none hover:text-white">Visitor Testimonials</a></li>
                    <li><a href="<?php echo ADMIN_URL; ?>/login.php" class="text-white-50 text-decoration-none hover:text-white">Staff Management Portal</a></li>
                </ul>
            </div>

            <!-- Contact & Inquiries -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase text-gold fw-bold mb-3 small" style="letter-spacing: 0.05em;">Contact Sanctuary</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-white-50">
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-gold mt-1"></i>
                        <span><?php echo PHYSICAL_ADDRESS; ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-gold"></i>
                        <span><?php echo CONTACT_PHONE; ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-gold"></i>
                        <span><?php echo CONTACT_EMAIL; ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-fill text-gold"></i>
                        <span>Open 7 Days: 9:00 AM – 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary border-opacity-25 my-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center small text-white-50">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="<?php echo BASE_URL; ?>/about.php" class="text-white-50 text-decoration-none hover:text-white">Conservation Mission</a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="text-white-50 text-decoration-none hover:text-white">Privacy & Wildlife Safety</a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="text-white-50 text-decoration-none hover:text-white">Terms of Entry</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
