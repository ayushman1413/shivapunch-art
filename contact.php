      <?php
$page_description = "Contact Shiva Punch Art for punch, die and embossing solutions. Call or message us for a quote. Mon-Sat 9 AM to 7 PM.";
include "common/header.php";
?>

        <!-- Start Breadcrumb Area -->
        <div class="breadcrumb-area" style="background-image: url('assets/img/contact.png');">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content text-center">
                            <h2>Contact Us</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb Area -->
        <!-- Start Contact Area -->
        <section class="contact-area section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 align-self-center">
                        <div class="contact-form">
                            <h2>Get In Touch</h2>
                            <form id="contact-form" action="contact-submit.php" method="POST" novalidate>
                            <input type="hidden" name="service" value="General Enquiry"><input type="hidden" name="source" value="Contact-page">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <input type="text" name="name" placeholder="Your Name" required />
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <input type="email" name="email" placeholder="Your Email" required />
                                            <i class="far fa-envelope"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <div class="sp-phone-field"><span class="sp-phone-prefix">+91</span><input type="tel" name="phone" placeholder="10-digit mobile number" inputmode="numeric" autocomplete="tel-national" maxlength="10" pattern="[0-9]{10}" required /></div>
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <input type="text" name="subject" placeholder="Your Subjects" />
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="single-input">
                                            <textarea name="message" placeholder="Write Message" required></textarea>
                                            <i class="fas fa-pen"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="button-1">Send Message</button>
                                    </div>
                                </div>
                            </form>
                            <div class="shiva-form-loading" aria-hidden="true"><span class="shiva-spinner"></span><span>Submitting your enquiry...</span></div><p class="ajax-response"></p>
                        </div>
                    </div>
                    <div class="col-lg-4 align-self-center">
                        <div class="contact-form-info" style="background-image: url('assets/img/aboutimg.png');">
                            <h2>Don't hesitate to contact us</h2>
                            <div class="contact-info-list">
                                <div class="item mb-20">
                                    <div class="icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="content">
                                        <h4>Head Office</h4>
                                        <p>111, 1st Floor, Mahavir Ind.
Estate, Near The Khwaishh Presidency, Kanchpada, Malad West, Mumbai-400064 India</p>
                                    </div>
                                </div>
                                <div class="item mb-20">
                                    <div class="icon">
                                        <i class="far fa-envelope"></i>
                                    </div>
                                    <div class="content">
                                        <h4>Drop A Mail</h4>
                                        <p>info@shivapunchart.com</p>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="content">
                                        <h4>Call Us</h4>
                                        <p>+91 84335 38141</p>
                                        <p>+91 72089 30779</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Contact Area -->
        <!-- Start Subscriber Area -->
<div class="subscribe-area pt-70 pb-70" style="background-image: url('assets/img/work-about.png');">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-8">
                <div class="subscribe-content">
                    <h2>Why Businesses Trust Shiva Punch Art</h2>
                    <p>
                        We deliver precision-engineered punch tools and die solutions for the printing and packaging industry. 
                        Our commitment to quality, advanced technology, and timely delivery makes us a trusted manufacturing partner.
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <ul class="list-unstyled text-white">
    <li>✔ High-Precision Manufacturing</li>
    <li>✔ Advanced CNC & Laser Technology</li>
    <li>✔ Customized Die Solutions</li>
</ul>

                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled text-white">
    <li>✔ High-Precision Manufacturing</li>
    <li>✔ Advanced CNC & Laser Technology</li>
    <li>✔ Customized Die Solutions</li>
</ul>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                                            <a class="button-1" href="about.html">Read More About Us</a>

            </div>

        </div>
    </div>
</div>

        <!-- End Subscriber Area -->
       <?php include "common/footer.php" ?>

<script id="shiva-disable-context-menu">
(function(){
  document.addEventListener('contextmenu', function(e){ e.preventDefault(); }, {passive:false});
  document.addEventListener('dragstart', function(e){
    if (e.target && (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.tagName === 'A')) e.preventDefault();
  }, {passive:false});
})();
</script>
