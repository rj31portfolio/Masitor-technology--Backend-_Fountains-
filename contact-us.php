
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
    <h1 class="display-5 fw-bold mb-2 text-white">About Us</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">About Us</li>
      </ol>
    </nav>
  </div>
</section>
        <!-- CONTACT FORM
        ================================================== -->
        <section>
            <div class="container">
                <div class="contact-us rounded">
                    <div class="row mt-n2-9">
                        <div class="col-lg-7 mt-2-9">
                            <h2 class="text-secondary mb-4 h1">Write Us Something</h2>
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
                        <div class="col-lg-5 mt-2-9">
                            <div class="contact-details">
                                <div class="row">
                                    <div class="section-title mb-1-9">
                                        <h2 class="h4 text-white">Our Contact Detail</h2>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="contact-info">
                                            <div class="contacts-icon">
                                                <img src="<?php echo BASE_URL; ?>assets/img/icons/email.png" alt="...">
                                            </div>
                                            <div class="contacts-title">
                                                <h5 class="text-white font-weight-600 display-28">Send E-Mail</h5>
                                                <h6 class="text-white font-weight-400">info@masitortechnology.com</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="contact-info">
                                            <div class="contacts-icon upper">
                                                <img src="<?php echo BASE_URL; ?>assets/img/icons/call.png" alt="...">
                                            </div>
                                            <div class="contacts-title">
                                                <h5 class="text-white font-weight-600 display-28">Call Anytime</h5>
                                                <h6 class="text-white font-weight-400">+1</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="contact-info upper2">
                                            <div class="contacts-icon">
                                                <img src="<?php echo BASE_URL; ?>assets/img/icons/location.png" alt="...">
                                            </div>
                                            <div class="contacts-title">
                                                <h5 class="text-white font-weight-600 display-28"> Locations</h5>
                                                <h6 class="text-white font-weight-400">RZI 51, Mahavir Enclave,
                                                  Part 1 Palam Dabri Road, Dwarka, New Delhi - 110045, India</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="follow-company-icon2">
                                        <a href="#"> <i class="fab fa-facebook-f"></i> </a>
                                        <a href="#"> <i class="fa-brands fa-x-twitter"> </i> </a>
                                        <a href="#"> <i class="fab fa-linkedin-in"></i> </a>
                                        <a href="#"> <i class="fab fa-pinterest-p"></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAP
        ================================================== -->
        <section class="p-0">
            <div class="container-fuild">
               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.9478423671508!2d77.07964287457158!3d28.601341485498022!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1b4a2a4e8159%3A0x22058c06d32eef2a!2sMASITOR%20TECHNOLOGY%20COMPANIES!5e0!3m2!1sen!2sin!4v1758785622278!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>

<!-- FOOTER
================================================== -->
<?php require_once __DIR__."/inc/footer.php"; ?>