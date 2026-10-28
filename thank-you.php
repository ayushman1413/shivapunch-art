<?php
// Prevent stale cached versions of the Thank You page on hosting/browser caches.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
include "common/header.php"; ?>
<section class="shiva-thankyou">
  <div class="shiva-thankyou-inner">
    <div class="shiva-success-icon"><i class="fas fa-check"></i></div>
    <div class="shiva-thankyou-grid">
      <div class="shiva-thankyou-copy">
        <span class="shiva-thankyou-kicker">MESSAGE RECEIVED</span>
        <h1>Thank <span>You!</span></h1>
        <h3>Your message has been submitted successfully.</h3>
        <p>We’ve received your enquiry and our team will get back to you as soon as possible, usually within one business day.</p>
        <a class="shiva-thankyou-btn" href="index.php"><i class="fas fa-home"></i> Back to Home</a>
      </div>
      <div class="shiva-thankyou-art">
        <img src="assets/img/thank-you-envelope.png?v=20260909" alt="Thank You" class="shiva-thankyou-envelope-art">
      </div>
    </div>
    <div class="shiva-thankyou-info">
      <div><i class="fas fa-headset"></i><strong>Need Immediate Help?</strong><small>Call us: +91 91367 30776</small></div>
      <div><i class="far fa-envelope"></i><strong>Email Us</strong><small>shivapunchart@gmail.com</small></div>
      <div><i class="fas fa-map-marker-alt"></i><strong>Our Office</strong><small>Malad West, Mumbai</small></div>
      <div><i class="far fa-clock"></i><strong>Business Hours</strong><small>Mon - Sat · 09:00 AM - 08:00 PM</small></div>
    </div>
  </div>
</section>
<style>
.shiva-thankyou{padding:45px 15px 70px;background:linear-gradient(135deg,#f7fbff 0%,#eef4ff 100%);min-height:650px;display:flex;align-items:center;overflow:hidden}.shiva-thankyou-inner{max-width:1120px;width:100%;margin:auto;text-align:center}.shiva-success-icon{width:92px;height:92px;border-radius:50%;margin:0 auto 20px;background:#25c66f;color:#fff;display:flex;align-items:center;justify-content:center;font-size:42px;box-shadow:0 0 0 14px rgba(37,198,111,.12)}.shiva-thankyou-grid{display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:25px}.shiva-thankyou-copy{text-align:center}.shiva-thankyou-kicker{font-weight:800;letter-spacing:2px;color:#263bb6;font-size:13px}.shiva-thankyou h1{font-size:64px;line-height:1;margin:12px 0;color:#13265a;font-weight:800}.shiva-thankyou h1 span{color:#2464e8}.shiva-thankyou h3{font-size:21px;color:#17336f;margin:15px 0;font-weight:800}.shiva-thankyou p{max-width:540px;margin:0 auto 26px;color:#5e6c85;line-height:1.7;font-size:16px}.shiva-thankyou-btn{display:inline-flex;align-items:center;gap:9px;padding:13px 24px;border-radius:999px;background:linear-gradient(90deg,#f32d38,#ff9418);color:#fff!important;font-weight:700;text-decoration:none;box-shadow:0 8px 20px rgba(243,45,56,.18)}.shiva-thankyou-art{min-height:330px;display:flex;align-items:center;justify-content:center;position:relative}.shiva-thankyou-envelope-art{display:block;width:min(100%,540px);height:auto;object-fit:contain;mix-blend-mode:multiply}.shiva-thankyou-info{margin-top:26px;background:rgba(255,255,255,.72);border:1px solid #dce6f4;border-radius:15px;display:grid;grid-template-columns:repeat(4,1fr);padding:18px 12px;box-shadow:0 10px 30px rgba(32,70,130,.04)}.shiva-thankyou-info div{display:flex;flex-direction:column;gap:4px;padding:0 16px;border-right:1px solid #dbe4f0}.shiva-thankyou-info div:last-child{border:0}.shiva-thankyou-info i{font-size:22px;color:#243fce;margin-bottom:5px}.shiva-thankyou-info strong{font-size:13px;color:#203a72}.shiva-thankyou-info small{color:#6c7890;font-size:12px}@media(max-width:767px){.shiva-thankyou{padding:38px 15px 55px}.shiva-thankyou-grid{grid-template-columns:1fr;gap:12px}.shiva-thankyou-art{min-height:230px}.shiva-thankyou h1{font-size:46px}.shiva-thankyou h3{font-size:17px}.shiva-thankyou p{font-size:14px}.shiva-thankyou-info{grid-template-columns:1fr 1fr;gap:18px}.shiva-thankyou-info div{border-right:0;padding:0 8px}.shiva-thankyou-info small{font-size:11px}.shiva-thankyou-envelope-art{width:95%}}
</style>
<?php include "common/footer.php"; ?>
