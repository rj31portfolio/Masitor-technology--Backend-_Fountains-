<?php
require_once __DIR__."/inc/config.php";
require_once __DIR__."/inc/head.php";

// Fetch all active categories
$conn = getDBConnection();
$categories = [];
$result = $conn->query("SELECT id, name, slug, image, short_description FROM categories WHERE status='active' ORDER BY id ASC Limit 8");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();
?>

  <!-- HEADER
        ================================================== -->
  <?php require_once __DIR__."/inc/header.php"; ?>

  <!-- BANNER
        ================================================== -->
  <section class="video-banner position-relative">
      <video autoplay muted loop playsinline controls id="bg-video" class="w-100 h-100 object-fit-cover">
          <source src="assets/banner-2.mp4" type="video/mp4">
          Your browser does not support the video tag.
      </video>
      <audio id="bg-audio" autoplay loop muted>
          <source src="assets/sound.mp3" type="audio/mpeg">
          Your browser does not support the audio element.
      </audio>
      <script>
          window.addEventListener("load", function() {
              const audio = document.getElementById("bg-audio");
              // Try to unmute after a short delay
              setTimeout(() => {
                  audio.muted = false;
                  audio.play().catch(err => {
                      console.log("Autoplay with sound blocked by browser:", err);
                  });
              }, 1000);
          });
      </script>
      <!-- Overlay content -->
      <div class="banner-content position-absolute top-50 start-50 translate-middle text-center text-white px-3">
          <h1 class="display-4 fw-bold text-white">Welcome to Our Website</h1>
          <p class="lead">Connecting You with Nature’s</p>
          <a href="#services" class="btn-style1 mt-3">Explore More</a>
      </div>
  </section>

  <style>
      .video-banner {
          width: 100%;
          height: 100vh;
          /* Full screen */
          overflow: hidden;
          position: relative;
      }

      .video-banner video {
          position: absolute;
          top: 0;
          left: 0;
          object-fit: cover;
          width: 100%;
          height: 100%;
      }

      .banner-content {
          z-index: 2;
      }

      @media (max-width: 768px) {
          .video-banner {
              height: 80vh;
          }

          .banner-content h1 {
              font-size: 1.8rem;
          }

          .banner-content p {
              font-size: 1rem;
          }
      }
  </style>
  <!-- ABOUT US
        ================================================== -->
  <section class="about-style-02 pb-lg-20">
      <div class="container">
          <div class="row align-items-center justify-content-center mt-n1-9 mt-md-n6">
              <div class="col-lg-6 mt-1-9 mt-md-6 order-2 order-lg-1 wow fadeIn" data-wow-delay="100ms">
                  <div class="pe-lg-6 pe-xl-10">
                      <div class="mb-2-1">
                          <span
                              class="text-muted text-uppercase small letter-spacing-4 d-block mb-2 font-weight-600">Welcome
                              To</span>
                          <h2 class="display-5 font-weight-800 mb-0">MASITOR TECHNOLOGY <span class="title-sm"></span>
                          </h2>
                      </div>
                      <p class="mb-1-9 display-28">Masitor Technology , established in 2017 in New Delhi,
                          manufactures and supplies designer fountains including Programmable, Musical, Interactive,
                          Static, and Floating types. Known for elegant designs, durability, and low energy use, we also
                          provide expert Fountain Installation Services under the guidance of proprietor Mr. Amarjeet
                          Kumar.</p>
                      <div class="d-flex align-items-center">
                          <a href="about.html" class="btn-style1 me-4"><span><span class="btn-small">Read
                                      More</span></span></a>
                          <a href="#" class="display-27 text-dark font-weight-700"><i
                                  class="fa-solid fa-phone-volume text-primary me-2"></i>+91 8045476031</a>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 mt-1-9 mt-md-6 order-1 order-lg-2 wow fadeIn" data-wow-delay="150ms">
                  <div class="about-image position-relative">
                      <div class="text-md-end">
                          <img src="<?php echo BASE_URL; ?>assets/img/content/about-04.jpg" alt="...">
                      </div>
                      <div class="about-img-text ani-top-bottom d-none d-md-block">
                          <span class='d-none'>Started in</span>
                          <p class="mb-0 d-none">2017</p>
                      </div>
                      <div class="about-img-one ani-top-bottom d-none d-md-block">
                          <img src="<?php echo BASE_URL; ?>assets/img/content/about-05.png" alt="...">
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>

  <!-- SERVICES
        ================================================== -->
  <section class="pt-0">
      <div class="container">
          <div class="mb-8 text-center wow fadeIn" data-wow-delay="100ms">
              <span class="text-muted text-uppercase small letter-spacing-4 d-block mb-2 font-weight-600">Service</span>
              <h2 class="display-5 font-weight-800 mb-0 w-sm-70 w-md-60 w-lg-50 w-xl-40 mx-auto">
                  Discover the range of Popular <span class="title-sm">Category</span>
              </h2>
          </div>

          <!-- Carousel wrapper -->
          <div id="carouselFountains" data-mdb-carousel-init class="carousel slide carousel-dark text-center"
              data-mdb-ride="carousel">
              <div class="carousel-inner py-4">
                  <div class="carousel-item active">
                      <div class="container">
                          <div class="row g-4">
                              <?php foreach ($categories as $cat): ?>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="card h-100">
                                        <img src="<?php echo BASE_URL; ?>admin/categories/uploads/categories/<?php echo htmlspecialchars($cat['image']); ?>"
                                            class="card-img-top"
                                            alt="<?php echo htmlspecialchars($cat['name']); ?>" />
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($cat['short_description']); ?></p>
                                            <a href="category.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>" class="btn-style1">Explore</a>
                                        </div>
                                    </div>
                                </div>
                              <?php endforeach; ?>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <!-- Carousel wrapper -->
      </div>
  </section>
  <!-- TESTIMONIALS
        ================================================== -->
  <section class="bg-img cover-background overflow-visible secondary-overlay" data-overlay-dark="7"
      data-background="<?php echo BASE_URL; ?>assets/img/bg/banner-bg.png">
      <div class="container">
          <div class="row justify-content-center mb-8">
              <div class="col-lg-6 text-center">
                  <div class="testimonial-carousel2 owl-carousel owl-theme">

                      <!-- Review 1 -->
                      <div>
                          <div class="mb-2-6">
                              <img src="<?php echo BASE_URL; ?>assets/img/avatars/avatar-04.jpg" class="rounded-circle"
                                  alt="...">
                          </div>
                          <p
                              class="mb-1-9 display-28 display-md-26 w-sm-85 mx-auto text-white opacity4 font-weight-400">
                              Very good service. They delivered on time and the quality is excellent. Happy with my
                              purchase.
                          </p>
                          <h4 class="mb-0 text-primary display-29 text-uppercase lh-1">Rakesh Kumar</h4>
                          <span class="small text-white text-uppercase display-31 opacity4 lh-base">Business
                              Owner</span>
                      </div>

                      <!-- Review 2 -->
                      <div>
                          <div class="mb-2-6">
                              <img src="<?php echo BASE_URL; ?>assets/img/avatars/avatar-05.jpg" class="rounded-circle"
                                  alt="...">
                          </div>
                          <p
                              class="mb-1-9 display-28 display-md-26 w-sm-85 mx-auto text-white opacity4 font-weight-400">
                              Professional team and good support. They understand customer needs and give proper
                              solutions.
                          </p>
                          <h4 class="mb-0 text-primary display-29 text-uppercase lh-1">Anita Sharma</h4>
                          <span class="small text-white text-uppercase display-31 opacity4 lh-base">Manager</span>
                      </div>

                      <!-- Review 3 -->
                      <div>
                          <div class="mb-2-6">
                              <img src="<?php echo BASE_URL; ?>assets/img/avatars/avatar-06.jpg" class="rounded-circle"
                                  alt="...">
                          </div>
                          <p
                              class="mb-1-9 display-28 display-md-26 w-sm-85 mx-auto text-white opacity4 font-weight-400">
                              Great experience. Easy process and very reliable people. I recommend them to others also.
                          </p>
                          <h4 class="mb-0 text-primary display-29 text-uppercase lh-1">Sandeep Verma</h4>
                          <span class="small text-white text-uppercase display-31 opacity4 lh-base">Entrepreneur</span>
                      </div>
                  </div>
              </div>
          </div>
          <!-- Clients Logo Section -->
          <div class="row client-style01 justify-content-center">
              <div class="col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="100ms">
                  <div class="client-logo text-center text-lg-start">
                      <img src="<?php echo BASE_URL; ?>assets/img/clients/client-07.png" alt="...">
                  </div>
              </div>
              <div class="col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="150ms">
                  <div class="client-logo text-center text-lg-start">
                      <img src="<?php echo BASE_URL; ?>assets/img/clients/client-08.png" alt="...">
                  </div>
              </div>
              <div class="col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="200ms">
                  <div class="client-logo text-center text-lg-start">
                      <img src="<?php echo BASE_URL; ?>assets/img/clients/client-09.png" alt="...">
                  </div>
              </div>
              <div class="col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="250ms">
                  <div class="client-logo text-center text-lg-start">
                      <img src="<?php echo BASE_URL; ?>assets/img/clients/client-10.png" alt="...">
                  </div>
              </div>
          </div>
      </div>
  </section>

  <!-- PROCESS
        ================================================== -->
  <section class="process-style01 pt-15">
      <div class="container">
          <div class="row pt-1-9 pt-md-6 pt-xl-10 mt-n1-9">

              <!-- Step 1 -->
              <div class="col-sm-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="100ms">
                  <div class="process-box position-relative">
                      <span class="process-no">01</span>
                      <h3 class="mb-3 h4">Choose Your Fountain</h3>
                      <p class="w-90 mb-3">
                          Explore our wide collection of designer, musical, and outdoor fountains that add beauty to any
                          space.
                      </p>
                      <a href="about.html"
                          class="text-uppercase text-primary text-secondary-hover display-30 d-flex align-items-center">
                          Read More <i class="ti-arrow-right display-31 ms-2"></i>
                      </a>
                  </div>
              </div>

              <!-- Step 2 -->
              <div class="col-sm-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="150ms">
                  <div class="process-box position-relative mt-8 mt-sm-2-5">
                      <span class="process-no">02</span>
                      <h3 class="mb-3 h4">Get Expert Guidance</h3>
                      <p class="w-90 mb-3">
                          MASITOR TECHNOLOGY team guides you with the right fountain as per your space &
                          budget.
                      </p>
                      <a href="about.html"
                          class="text-uppercase text-primary text-secondary-hover display-30 d-flex align-items-center">
                          Read More <i class="ti-arrow-right display-31 ms-2"></i>
                      </a>
                  </div>
              </div>
              <!-- Step 3 -->
              <div class="col-sm-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="200ms">
                  <div class="process-box position-relative mt-8 mt-sm-9">
                      <span class="process-no">03</span>
                      <h3 class="mb-3 h4">Quality Installation</h3>
                      <p class="w-90 mb-3">
                          We ensure smooth installation and long-lasting quality finish for every fountain we deliver.
                      </p>
                      <a href="about.html"
                          class="text-uppercase text-primary text-secondary-hover display-30 d-flex align-items-center">
                          Read More <i class="ti-arrow-right display-31 ms-2"></i>
                      </a>
                  </div>
              </div>

          </div>
      </div>
  </section>

  <!-- CONTACT US
        ================================================== -->
  <section class="contact-style02 bg-secondary">
      <div class="container">
          <div class="row align-items-center mt-n2-9">
              <div class="col-lg-6 mt-2-9 wow fadeIn" data-wow-delay="100ms">
                  <div>
                      <div class="mb-2-1">
                          <span
                              class="text-white opacity7 text-uppercase small letter-spacing-4 d-block mb-2 font-weight-600">Contact</span>
                          <h2 class="display-5 font-weight-800 mb-0 text-white">Let's <span class="title-sm">Talk
                                  Business!</span></h2>
                      </div>
                      <div class="row mt-n1-9 mb-6">
                          <div class="col-sm-6 mt-1-9">
                              <h4 class="text-white">RZI 51, Mahavir Enclave,</h4>
                              <p class="text-white opacity8 mb-0">Part 1 Palam Dabri Road, Dwarka, New Delhi - 110045,
                                  India</p>
                          </div>
                          <div class="col-sm-6 mt-1-9">
                              <h4 class="text-white">Phone</h4>
                              <p class="mb-0"><a href="#" class="text-primary text-white-hover">+91-8045476031</a></p>
                              <p class="mb-0"><a href="#" class="text-primary text-white-hover">+91-8045476031</a></p>
                          </div>
                          <div class="col-sm-6 mt-1-9">
                              <h4 class="text-white">Follow us</h4>
                              <ul class="social-icon-style1 list-unstyled">
                                  <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                  <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                  <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                  <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                              </ul>
                          </div>
                          <div class="col-sm-6 mt-1-9">
                              <h4 class="text-white">Email</h4>
                              <p class="mb-0 text-white opacity8">Interested in working with us?</p>
                              <p class="mb-0"><a href="#"
                                      class="text-decoration-underline text-primary text-white-hover">info@masitorfountain.com/</a>
                              </p>
                          </div>
                      </div>
                      <div class="contact-text">
                          <p class="mb-0">since 2017</p>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 mt-2-9 wow fadeIn" data-wow-delay="150ms">
                  <div class="bg-white p-1-9 p-md-2-9 p-xl-6">
                      <div class="mb-3">
                          <h2 class="display-6 font-weight-800 mb-0">Get in Touch</h2>
                      </div>
                      <p class="mb-1-6">Please take a quick moment to complete this form and a business representative
                          will get back to you swiftly</p>
                      <form class="contact quform" action="contact.php" method="post" enctype="multipart/form-data"
                          id="contactForm" novalidate>
                          <div class="quform-elements">
                              <div class="row">
                                  <!-- Name -->
                                  <div class="col-md-6">
                                      <label for="name">Your Name <span class="quform-required">*</span></label>
                                      <input class="form-control" id="name" type="text" name="name" required
                                          placeholder="Your name here" />
                                  </div>

                                  <!-- Email -->
                                  <div class="col-md-6">
                                      <label for="email">Your Email <span class="quform-required">*</span></label>
                                      <input class="form-control" id="email" type="email" name="email" required
                                          placeholder="Your email here" />
                                  </div>

                                  <!-- Subject -->
                                  <div class="col-md-6">
                                      <label for="subject">Your Subject <span class="quform-required">*</span></label>
                                      <input class="form-control" id="subject" type="text" name="subject" required
                                          placeholder="Your subject here" />
                                  </div>

                                  <!-- Phone -->
                                  <div class="col-md-6">
                                      <label for="phone">Contact Number</label>
                                      <input class="form-control" id="phone" type="text" name="phone"
                                          placeholder="Your phone here" />
                                  </div>

                                  <!-- Message -->
                                  <div class="col-md-12">
                                      <label for="message">Message <span class="quform-required">*</span></label>
                                      <textarea class="form-control" id="message" name="message" rows="4" required
                                          placeholder="Tell us a few words"></textarea>
                                  </div>

                                  <!-- Captcha -->
                                  <div class="col-md-12" style="margin-top:12px;">
                                      <label for="captchaInput">Type the word shown below <span
                                              class="quform-required">*</span></label>

                                      <!-- Captcha display box -->
                                      <div id="captchaBox" style="
            display:flex;
            align-items:center;
            gap:12px;
            border:1px dashed #ccc;
            padding:10px;
            width:320px;
            border-radius:6px;
            background: linear-gradient(90deg,#fff,#fff);
            ">
                                          <div id="captchaText" aria-live="polite" style="
              user-select:none;
              font-family: 'Courier New', monospace;
              font-size:22px;
              letter-spacing:4px;
              padding:6px 10px;
              border-radius:4px;
              background:#f5f5f5;
              box-shadow: inset 0 0 0 1px rgba(0,0,0,0.03);
            "> </div>

                                          <!-- Refresh button -->
                                          <button type="button" id="refreshCaptcha" title="Refresh captcha" style="
              padding:6px 8px;
              border-radius:4px;
              border:1px solid #ddd;
              background:white;
              cursor:pointer;
            ">↻</button>
                                      </div>

                                      <!-- Input and hidden -->
                                      <input class="form-control" id="captchaInput" type="text" name="captcha" required
                                          placeholder="Enter captcha here" style="margin-top:8px; width:320px;">
                                      <input type="hidden" id="captchaHidden" name="captcha_hidden">
                                  </div>

                                  <!-- Submit -->
                                  <div class="col-md-12" style="margin-top:14px;">
                                      <button class="btn-style1 border-0" type="submit"><span><span
                                                  class="btn-small">Send Message</span></span></button>
                                  </div>
                              </div>
                          </div>
                      </form>

                      <script>
                          /*
  Reliable captcha generation & display.
  - Uses DOMContentLoaded to ensure elements exist.
  - Shows captcha in #captchaText.
  - Stores the value in hidden input #captchaHidden.
  - Refresh button reloads new captcha without page reload.
*/
                          (function() {
                              const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789"; // avoid ambiguous 0,O,1,I
                              function makeCaptcha(len = 6) {
                                  let out = "";
                                  for (let i = 0; i < len; i++) {
                                      out += chars.charAt(Math.floor(Math.random() * chars.length));
                                  }
                                  return out;
                              }

                              function setCaptcha() {
                                  const textEl = document.getElementById("captchaText");
                                  const hiddenEl = document.getElementById("captchaHidden");
                                  if (!textEl || !hiddenEl) return;
                                  const code = makeCaptcha(6);
                                  textEl.textContent = code;
                                  hiddenEl.value = code;
                              }
                              // Wait until DOM loaded
                              document.addEventListener("DOMContentLoaded", function() {
                                  setCaptcha();
                                  const refreshBtn = document.getElementById("refreshCaptcha");
                                  if (refreshBtn) {
                                      refreshBtn.addEventListener("click", function(e) {
                                          e.preventDefault();
                                          setCaptcha();
                                          // focus input for accessibility
                                          const capIn = document.getElementById("captchaInput");
                                          if (capIn) capIn.focus();
                                      });
                                  }
                                  // Optional: simple client-side validation to show alert before submitting
                                  const form = document.getElementById("contactForm");
                                  form.addEventListener("submit", function(e) {
                                      // ensure captcha matches (case-insensitive)
                                      const input = (document.getElementById("captchaInput")
                                          .value || "").trim();
                                      const expected = (document.getElementById("captchaHidden")
                                          .value || "").trim();
                                      if (!input || input.toLowerCase() !== expected
                                          .toLowerCase()) {
                                          e.preventDefault();
                                          alert("Captcha incorrect. Please try again.");
                                          setCaptcha();
                                          document.getElementById("captchaInput").value = "";
                                          return false;
                                      }
                                      // allow submit; server side will re-check too
                                      return true;
                                  });
                              });
                          })();
                      </script>
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- BLOG
        ================================================== -->
  <section class="bg-light">
      <div class="container">
          <div class="mb-2-1 text-center wow fadeIn" data-wow-delay="100ms">
              <span class="text-muted text-uppercase small letter-spacing-4 d-block mb-2 font-weight-600">Our
                  Blog</span>
              <h2 class="display-5 font-weight-800 mb-0 w-sm-70 w-md-60 w-lg-50 w-xl-40 mx-auto">
                  Latest <span class="title-sm">Fountain Articles</span>
              </h2>
          </div>

          <div class="row g-xl-5 mt-n1-9">
              <!-- Blog 1 -->
              <div class="col-md-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="100ms">
                  <article class="card card-style06 border-0 rounded-0 h-100">
                      <div class="position-relative overflow-hidden">
                          <img src="<?php echo BASE_URL; ?>assets/img/blog/blog1.png" alt="...">
                          <div class="card-img-text"><a href="#">Design</a></div>
                      </div>
                      <div class="card-body position-relative pt-2-6 pb-1-9 pb-xl-2-6 px-1-9 px-xl-2-4">
                          <div class="mb-3">
                              <div class="d-inline-block me-3">
                                  <i class="fa-solid fa-user me-2 text-primary"></i><a href="#">Admin</a>
                              </div>
                              <div class="d-inline-block"><a href="#" class="display-30">13 Comments</a></div>
                          </div>
                          <h3 class="h4 mb-0">
                              <a href="blog-details.html">10 Reasons to Add a Fountain in Your Home & Garden</a>
                          </h3>
                      </div>
                  </article>
              </div>
              <!-- Blog 2 -->
              <div class="col-md-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="150ms">
                  <article class="card card-style06 border-0 rounded-0 h-100">
                      <div class="position-relative overflow-hidden">
                          <img src="<?php echo BASE_URL; ?>assets/img/blog/blog2.png" alt="...">
                          <div class="card-img-text"><a href="#">Tips</a></div>
                      </div>
                      <div class="card-body position-relative pt-2-6 pb-1-9 pb-xl-2-6 px-1-9 px-xl-2-4">
                          <div class="mb-3">
                              <div class="d-inline-block me-3">
                                  <i class="fa-solid fa-user me-2 text-primary"></i><a href="#">Admin</a>
                              </div>
                              <div class="d-inline-block"><a href="#" class="display-30">08 Comments</a></div>
                          </div>
                          <h3 class="h4 mb-0">
                              <a href="blog-details.html">How to Select the Right Fountain for Your Space</a>
                          </h3>
                      </div>
                  </article>
              </div>
              <!-- Blog 3 -->
              <div class="col-md-6 col-lg-4 mt-1-9 wow fadeIn" data-wow-delay="200ms">
                  <article class="card card-style06 border-0 rounded-0 h-100">
                      <div class="position-relative overflow-hidden">
                          <img src="<?php echo BASE_URL; ?>assets/img/blog/blog3.png" alt="...">
                          <div class="card-img-text"><a href="#">Inspiration</a></div>
                      </div>
                      <div class="card-body position-relative pt-2-6 pb-1-9 pb-xl-2-6 px-1-9 px-xl-2-4">
                          <div class="mb-3">
                              <div class="d-inline-block me-3">
                                  <i class="fa-solid fa-user me-2 text-primary"></i><a href="#">Admin</a>
                              </div>
                              <div class="d-inline-block"><a href="#" class="display-30">02 Comments</a></div>
                          </div>
                          <h3 class="h4 mb-0">
                              <a href="blog-details.html">Top Fountain Ideas for Hotels, Malls & Parks</a>
                          </h3>
                      </div>
                  </article>
              </div>
          </div>
      </div>
  </section>

  <!-- FOOTER
        ================================================== -->
  <?php require_once __DIR__."/inc/footer.php"; ?>