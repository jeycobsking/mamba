<?php
/**
 * About Mamba Village Nairobi
 */
$pageTitle = "About Our Sanctuary";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="py-5 bg-primary-dark text-white text-center">
    <div class="container py-4">
        <span class="badge bg-warning text-dark text-uppercase small px-3 py-2 fw-bold mb-2">Conservation & Wildlife</span>
        <h1 class="display-5 fw-bold mb-2">About Mamba Village Nairobi</h1>
        <p class="text-white-50 mx-auto fs-6" style="max-width: 650px;">
            East Africa's renowned crocodile conservation sanctuary, nature botanical park, and premier leisure destination.
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <span class="badge bg-success bg-opacity-10 text-success text-uppercase small px-3 py-2 fw-bold mb-2">Our Heritage</span>
            <h2 class="display-6 fw-bold text-dark mb-3">Reptile Conservation & Education Since 1996</h2>
            <p class="text-muted mb-3">
                Situated in the scenic serene surroundings of Karen and Langata in Nairobi, Mamba Village is renowned as one of the largest specialized crocodile parks in East Africa. Our primary mission bridges conservation biology, public environmental education, and leisure tourism.
            </p>
            <p class="text-muted mb-4">
                The park houses more than 70 Nile crocodiles across specialized life stages, from juvenile hatchling nurseries to massive 14-foot adult breeding pools. Alongside our crocodilian enclosures, we maintain thriving ostrich enclosures, giant Aldabra and leopard tortoises, and over 15 acres of protected indigenous botanical canopy.
            </p>

            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h3 class="fw-bold text-success mb-1">70+</h3>
                        <small class="text-muted">Nile Crocodiles Conserved</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h3 class="fw-bold text-success mb-1">15+</h3>
                        <small class="text-muted">Acres of Botanical Flora</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=800&q=80" alt="Nile Crocodile" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 420px; object-fit: cover;" loading="lazy">
        </div>
    </div>

    <!-- Conservation Pillars -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="brand-mark bg-success mx-auto mb-3">
                    <i class="fa-solid fa-shield-cat text-white"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Herpetology Protection</h5>
                <p class="text-muted small mb-0">
                    We maintain strictly regulated veterinary care, dietary nutritional balance, and incubation facilities to safeguard vulnerable reptile populations.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="brand-mark bg-warning text-dark mx-auto mb-3">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Educational Partnerships</h5>
                <p class="text-muted small mb-0">
                    Collaborating with Kenyan wildlife researchers and school boards to provide hands-on biological learning for thousands of young learners.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="brand-mark bg-primary mx-auto mb-3">
                    <i class="fa-solid fa-seedling text-white"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Indigenous Flora Preservation</h5>
                <p class="text-muted small mb-0">
                    Safeguarding rare indigenous Kenyan tree species, wetland lily habitats, and traditional medicinal plants in our botanical gardens.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
