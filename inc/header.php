<?php
require_once 'config.php';

// Fetch fountain categories for menu
$fountainCategories = [];
$conn = getDBConnection();
$result = $conn->query("SELECT name, slug FROM categories WHERE status='active' ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fountainCategories[] = $row;
    }
}
$conn->close();
?>
<!-- PAGE LOADING
================================================== -->
<div id="preloader"></div>

<!-- MAIN WRAPPER
================================================== -->
<div class="main-wrapper">

    <!-- HEADER
    ================================================== -->
    <header class="header-style2">

        <div class="top-bar bg-primary">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 col-12">
                        <div class="top-bar-info">
                            <ul class="ps-0 mb-0 d-flex flex-wrap gap-3">
                                <li><i class="fa-solid fa-phone-volume text-white me-2"></i>+91-8045476031</li>
                                <li class="d-none d-sm-inline-block"><i class="fa-solid fa-envelope me-2"></i>masitortechnologycompanies@gmail.com</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-md-block">
                        <ul class="top-social-icon ps-0 mb-0 d-flex justify-content-end gap-3">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="navbar-default border-bottom border-color-light-white">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    
                    <!-- Logo -->
                    <a href="<?php echo BASE_URL; ?>index.php" class="navbar-brand logodefault">
                        <img id="logo" src="<?php echo BASE_URL; ?>assets/img/logos/logo.png" alt="logo">
                    </a>

                    <!-- Toggler button -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menu -->
                    <div class="collapse navbar-collapse" id="mainNav">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a></li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About Us</a></li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Fountains</a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($fountainCategories as $cat): ?>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo urlencode($cat['slug']); ?>" class="dropdown-item">
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>blogs.php" class="nav-link">Blogs</a></li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>contact-us.php" class="nav-link">Contact</a></li>
                        </ul>
                        <!-- Right buttons -->
                        <div class="attr-nav ms-lg-3">
                            <ul class="d-flex align-items-center gap-3 mb-0">       
    <li class="d-xl-inline-block">
    <a href="<?php echo BASE_URL; ?>catalog.pdf" class="btn-style1 text-white btn-sm" download>
        <i class="fa fa-download"></i> Catalog
    </a>
</li>


                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
</div>
