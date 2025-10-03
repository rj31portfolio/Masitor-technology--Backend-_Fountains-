<?php require_once __DIR__."/inc/config.php"; ?>
<?php require_once __DIR__."/inc/head.php"; ?>

<!-- HEADER
================================================== -->
<?php require_once __DIR__."/inc/header.php"; ?>

<!-- Breadcrumb Section -->
<section class="breadcrumb-section d-flex align-items-center text-center text-white"
    style="background: url('assets/img/banner/about-banner.png') center/cover no-repeat; height: 250px; position: relative;">

    <!-- Overlay -->
    <div style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
    <div class="container position-relative">
        <h1 class="display-5 fw-bold mb-2 text-white">404 - Page Not Found</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">404</li>
            </ol>
        </nav>
    </div>
</section>
<!-- ABOUT US -->

<div class="main-wrapper">

    <!-- ERROR PAGE
        ================================================== -->
<section class="p-0 bg-img bg-secondary cover-background" data-background="assets/img/content/error.jpg"
        style="background-image: url(&quot;assets/img/content/error.jpg&quot;);">
        <div class="container d-flex flex-column position-relative z-index-9">
            <div class="row align-items-center min-vh-100 text-center justify-content-center">
                <div class="col-lg-8 col-xl-6 wow fadIn animated" data-wow-delay="100ms"
                    style="visibility: visible; animation-delay: 100ms;">
                    <div class="bg-white px-1-9 px-sm-6 pt-1-6 pb-2-6 py-sm-7 rounded">
                        <h1 class="error-text">404</h1>
                        <h2 class="mb-1-9">Oops! This Page is Not Found.</h2>
                        <a href="index.php" class="btn-style1"><span>Home Page</span></a>
                    </div>
                </div>
            </div>
        </div>
</section>

<!-- FOOTER
================================================== -->
<?php require_once __DIR__."/inc/footer.php"; ?>