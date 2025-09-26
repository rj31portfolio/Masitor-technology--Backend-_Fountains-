<?php require_once 'config.php'; ?>
<!-- FOOTER
================================================== -->
<footer class="position-relative bg-secondary pt-2-6 pt-sm-6 pt-xxl-8">
    <div class="container">
        <div class="row mt-n1-9 mb-2-6 mb-sm-6 mb-xxl-8">
            <div class="col-xl-4 mt-1-9">
                <div class="footer-logo">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <img src="<?php echo BASE_URL; ?>assets/img/logos/footer.jpg" alt="...">
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 mt-1-9">
                <h2 class="mb-0 text-white h3">Subscribe to our newsletter! Stay always in touch!</h2>
            </div>
            <div class="col-md-6 col-xl-4 mt-1-9">
                <div>
                    <form class="quform newsletter-form" action="<?php echo BASE_URL; ?>quform/newsletter-two.php" method="post" enctype="multipart/form-data">

                        <div class="quform-elements">
                            <div class="row">
                                <!-- Begin Text input element -->
                                <div class="col-md-12">
                                    <div class="quform-element mb-0">
                                        <div class="quform-input">
                                            <input class="form-control" id="email_address" type="text" name="email_address" placeholder="Subscribe with us">
                                        </div>
                                    </div>
                                </div>
                                <!-- End Text input element -->

                                <!-- Begin Submit button -->
                                <div class="col-md-12">
                                    <div class="quform-submit-inner">
                                        <button class="btn btn-white text-white m-0" type="submit"><i class="fas fa-paper-plane"></i></button>
                                    </div>
                                    <div class="quform-loading-wrap"><span class="quform-loading"></span></div>
                                </div>
                                <!-- End Submit button -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4"></div>
            <div class="col-xl-12">
                <div>
                    <div class="row border-bottom border-color-light-white pb-2-2 pb-lg-6 pb-xxl-8 mb-1-9">
                        <div class="col-sm-6 col-lg-4 mb-1-9 mb-lg-0">
                            <div>
                                <h2 class="h5 text-primary mb-4">Our Address</h2>
                                <address class="mb-0 display-29 display-md-28 text-white opacity7">RZI 51, Mahavir Enclave, <br> Part 1 Palam Dabri Road, <br> Dwarka, New Delhi - 110045, India</address>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 mb-1-9 mb-lg-0">
                            <div class="ps-sm-6 ps-xl-1-9 ps-xxl-2-9">
                                <h2 class="h5 text-primary mb-4">Contact Us</h2>
                                <p class="email"><a href="mailto:masitortechnologycompanies@gmail.com" class="display-29 display-md-28 display-xl-27">masitortechnologycompanies@gmail.com</a></p>
                                <p class="phone"><a href="tel:+918045476031" class="display-26 display-md-25 display-xl-24">+91-8045476031</a></p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="ps-lg-6">
                                <h2 class="h5 text-primary mb-4">Our Social</h2>
                                <ul class="social-icon-style4 list-unstyled mb-0">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-1-9 mt-n1-9">
                        <div class="col-md-7 mt-1-9">
                            <div class="text-center text-md-start">
                                <p class="d-inline-block text-white mb-0">&copy; <span class="current-year"></span> MASITOR TECHNOLOGY COMPANIES. </p>
                            </div>
                        </div>
                        <div class="col-md-5 mt-1-9">
                            <div class="text-center text-md-end">
                                <ul class="list-unstyled mb-0">
                                    <li class="display-30 d-inline-block border-end border-color-light-white pe-3 me-2 lh-1">
                                        <a href="<?php echo BASE_URL; ?>terms-conditions.php" class="text-white text-primary-hover">Terms & Conditions</a>
                                    </li>
                                    <li class="display-30 d-inline-block lh-1">
                                        <a href="<?php echo BASE_URL; ?>privacy-policy.php" class="text-white text-primary-hover">Privacy Policy</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>

<!-- SCROLL TO TOP
================================================== -->
<div class="scroll-top-percentage"><span id="scroll-value"></span></div>

<!-- all js include start -->

<!-- jQuery -->
<script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>

<!-- popper js -->
<script src="<?php echo BASE_URL; ?>assets/js/popper.min.js"></script>

<!-- bootstrap -->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.min.js"></script>

<!-- scroll -->
<script src="<?php echo BASE_URL; ?>assets/js/jquery.scrollbar.min.js"></script>

<!-- jquery core -->
<script src="<?php echo BASE_URL; ?>assets/js/core.min.js"></script>

<!-- search -->
<script src="<?php echo BASE_URL; ?>assets/search/search.js"></script>

<!-- custom scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

<!-- form plugins js -->
<script src="<?php echo BASE_URL; ?>assets/quform/js/plugins.js"></script>

<!-- form scripts js -->
<script src="<?php echo BASE_URL; ?>assets/quform/js/scripts.js"></script>

<!-- all js include end -->

</body>
</html>
