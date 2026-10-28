<?php
require_once __DIR__ . '/admin/config.php';
require_once __DIR__ . '/admin/helpers.php';
ensure_template_schema($pdo);
$stmt = $pdo->query("SELECT id, title, file, category_id FROM packaging_templates WHERE status='published' ORDER BY id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$allCount = count($rows);
$cats = $pdo->query("SELECT c.id, c.name, COUNT(t.id) AS n FROM template_categories c
                     JOIN packaging_templates t ON t.category_id = c.id AND t.status='published'
                     GROUP BY c.id, c.name ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC);
$cat = (int)($_GET['cat'] ?? 0);
if (!in_array($cat, array_map('intval', array_column($cats, 'id')), true)) { $cat = 0; }
if ($cat) { $rows = array_values(array_filter($rows, function ($r) use ($cat) { return (int)$r['category_id'] === $cat; })); }
$total = count($rows);
$seoTitle = 'Packaging Templates &amp; Die Designs | Shiva Punch Art';
$canonical = 'https://shivapunchart.com/packaging-templates.html';
foreach ($cats as $c) {
    if ((int)$c['id'] === $cat) {
        $seoTitle = htmlspecialchars($c['name']) . ' – Packaging Templates | Shiva Punch Art';
        $canonical .= '?cat=' . $cat;
    }
}
?>
 <!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $seoTitle ?></title>
<meta name="description" content="Gallery of packaging templates and die work by Shiva Punch Art, Mumbai. Browse our punch, emboss and foil block designs.">
<link rel="canonical" href="<?= $canonical ?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="Shiva Punch Art">
<meta property="og:title" content="<?= $seoTitle ?>"><meta property="og:description" content="Gallery of packaging templates and die work by Shiva Punch Art, Mumbai. Browse our punch, emboss and foil block designs.">
<meta property="og:url" content="<?= $canonical ?>"><meta property="og:image" content="https://shivapunchart.com/assets/img/logo.png">
<meta name="twitter:card" content="summary_large_image">
<link href="assets/img/favicon (3).png" rel="icon" type="image/png"/>
<link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
<link href="assets/css/fontawesome.all.min.css" rel="stylesheet"/>
<link href="assets/css/owl.carousel.min.css" rel="stylesheet"/>
<link href="assets/css/owl.theme.default.min.css" rel="stylesheet"/>
<link href="assets/css/animate.css" rel="stylesheet"/>
<link href="assets/css/magnific-popup.css" rel="stylesheet"/>
<link href="assets/css/normalize.css" rel="stylesheet"/>
<link href="style.css" rel="stylesheet"/>
<link href="assets/css/responsive.css" rel="stylesheet"/>
<link href="assets/css/shiva-custom.css?v=1793118085" rel="stylesheet"/>
</head>
<body class="shiva-compact-trust-gallery">
<header class="header shiva-header">
<div class="header-bottom shiva-navbar">
<div class="container">
<div class="shiva-nav-row">
<div class="logo shiva-logo"><a href="index.html"><img alt="Shiva Punch Art" src="assets/img/logo.png"/></a></div>
<div class="shiva-nav-center">
<div class="menu"><nav><ul>
<li><a href="index.html">Home</a></li><li><a href="about.html">About</a></li><li><a href="our-product.html">Our Product</a></li><li><a href="packaging-templates.html">Packaging  Templates</a></li><li><a href="exhibitions.html">Exhibitions</a></li><li><a href="contact.html">Contact Us</a></li>
</ul></nav></div>
</div>
<div class="shiva-nav-right">
<a class="shiva-follow-btn" href="https://whatsapp.com/channel/0029VbCCeCcEVccFQmXFQ00V" rel="noopener" target="_blank"><i class="fab fa-whatsapp"></i><span>Follow Channel</span></a>
          <a class="shiva-quality-logo" href="about.html" aria-label="24/7 Service"><img src="assets/img/client-logos/247-service-pdf.png" alt="24/7 Service" /></a>
          <a class="shiva-guarantee-logo" href="about.html" aria-label="Quality Guaranteed"><img src="assets/img/client-logos/quality-guaranteed.png" alt="Quality Guaranteed" /></a>
          <a class="shiva-bombay-logo" href="about.html" aria-label="Bombay Master Printers Association"><img src="assets/img/client-logos/bombay-master-printers.png" alt="Bombay Master Printers Association" /></a>
<div class="canvas_open"><a href="#" aria-label="Open menu"><span></span><span></span><span></span></a></div>
</div>
</div>
</div>
</div>
</header>
<div class="mobile-menu-area">
<div class="off_canvars_overlay"></div>
<div class="offcanvas_menu"><div class="offcanvas_menu_wrapper">
<div class="canvas_close"><a href="#"><i class="fas fa-times"></i></a></div>
<div class="mobile-logo"><a href="index.html"><img alt="Shiva Punch Art" src="assets/img/logo.png"/></a></div>
<a class="shiva-mobile-follow" href="https://whatsapp.com/channel/0029VbCCeCcEVccFQmXFQ00V" rel="noopener" target="_blank"><i class="fab fa-whatsapp"></i> Follow WhatsApp Channel</a>
    <div class="shiva-mobile-brand-logos"><a class="shiva-mobile-quality-logo" href="about.html" aria-label="24/7 Service"><img src="assets/img/client-logos/247-service-new.png" alt="24/7 Service" /></a><a class="shiva-mobile-guarantee-logo" href="about.html" aria-label="Quality Guaranteed"><img src="assets/img/client-logos/quality-guaranteed.png" alt="Quality Guaranteed" /></a><a class="shiva-mobile-bombay-logo" href="about.html" aria-label="Bombay Master Printers Association"><img src="assets/img/client-logos/bombay-master-printers.png" alt="Bombay Master Printers Association" /></a></div>
<div class="text-left" id="menu"><ul class="offcanvas_main_menu"><li><a href="index.html">Home</a></li><li><a href="about.html">About</a></li><li><a href="our-product.html">Our Product</a></li><li><a href="packaging-templates.html">Packaging  Templates</a></li><li><a href="exhibitions.html">Exhibitions &amp; Trade Shows</a></li><li><a href="contact.html">Contact Us</a></li></ul></div>
<div class="shiva-mobile-menu-addresses">
<a class="shiva-mobile-address" href="https://www.google.com/maps/search/?api=1&amp;query=Sr.No%3A223%2C+Cts-2675%2C+Hissa-687%2C+Kharodi+Marve+Road%2C+Near+Chheda+Complex%2C+Malwani%2C+Malad%28West%29%2C+Mumbai-400095" rel="noopener" target="_blank"><span>Malad Unit</span>Sr.No:223, Cts-2675, Hissa-687, Kharodi Marve Road, Near Chheda Complex, Malwani, Malad(West), Mumbai-400095</a>
<div class="shiva-mobile-address"><span>Vasai Unit</span>Gala No:C-2, Ganesh Ind Estate, Behind Sagar Hotel, Chinchoti, Devdal, Kaman Village, Vasai(East), Palghar-401208</div>
</div>
</div></div>
</div>
<!-- Start Breadcrumb Area -->
<div class="breadcrumb-area" style="background-image: url('assets/img/tooling\ solutions.png');">
<div class="container">
<div class="row">
<div class="col-12">
<div class="breadcrumb-content text-center">
<h1>Packaging Templates</h1>
</div>
</div>
</div>
</div>
</div>
<!-- End Breadcrumb Area -->
<!-- Start Pinterest-style Gallery Area -->
<?php
$pins = [];
foreach ($rows as $r) {
    $rel = (strpos($r['file'], '/') === false ? 'templates-files/' : '') . $r['file'];
    $dim = @getimagesize(__DIR__ . '/' . $rel);
    $pins[] = ['src' => $rel, 'title' => $r['title'], 'w' => $dim[0] ?? 4, 'h' => $dim[1] ?? 3];
}
?>
<section class="pin-gallery">
<div class="pin-wrap">
<?php if ($cats): ?><nav class="pin-cats" aria-label="Template categories"><a href="packaging-templates.html"<?= $cat ? '' : ' class="on"' ?>>All <small><?= $allCount ?></small></a><?php foreach ($cats as $c): ?><a href="packaging-templates.html?cat=<?= (int)$c['id'] ?>"<?= (int)$c['id'] === $cat ? ' class="on"' : '' ?>><?= htmlspecialchars($c['name']) ?> <small><?= (int)$c['n'] ?></small></a><?php endforeach; ?></nav><?php endif; ?>
<p class="pin-count"><strong><?= $total ?></strong> packaging templates <span>&middot; click any design to view it full size</span></p>
<div class="pin-grid" id="pinGrid"><?php foreach ($pins as $i => $p): ?><a aria-label="Open <?= htmlspecialchars($p['title']) ?>" class="pin" data-index="<?= $i + 1 ?>" href="<?= htmlspecialchars($p['src']) ?>" style="aspect-ratio:<?= $p['w'] ?>/<?= $p['h'] ?>"><img alt="<?= htmlspecialchars($p['title']) ?>" decoding="async" height="<?= $p['h'] ?>" loading="lazy" src="<?= htmlspecialchars($p['src']) ?>" width="<?= $p['w'] ?>"><span class="pin-view"><i class="fas fa-expand-alt"></i> View</span></a><?php endforeach; ?></div>
</div>
</section>
<style>
.pin-gallery{--pin-gap:16px;--pin-red:#ff2838;--pin-blue:#2818d0;background:#f4f5f8;padding:30px 0 70px}
.pin-wrap{max-width:1880px;margin:0 auto;padding:0 clamp(12px,2.4vw,40px)}
.pin-cats{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 18px}.pin-cats a{padding:8px 16px;border-radius:999px;background:#fff;color:#1d2746;font-size:13px;font-weight:700;box-shadow:0 0 0 1px rgba(20,24,40,.08)}.pin-cats a small{opacity:.6;font-weight:600}.pin-cats a.on{background:var(--pin-red);color:#fff;box-shadow:none}
.pin-count{margin:0 0 22px;font-size:14px;color:#6b7280}.pin-count strong{color:#1d2746;font-weight:800}.pin-count span{color:#9ca3af}
.pin-grid{column-count:5;column-gap:var(--pin-gap)}
.pin{position:relative;display:block;break-inside:avoid;margin:0 0 var(--pin-gap);border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 0 0 1px rgba(20,24,40,.07);cursor:zoom-in}
.pin img{display:block;width:100%;height:auto;transition:opacity .35s ease,transform .5s ease}
.pin::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.28) 0%,rgba(0,0,0,0) 38%,rgba(0,0,0,.0) 100%);opacity:0;transition:opacity .25s;pointer-events:none}
.pin-view{position:absolute;top:10px;right:10px;z-index:2;padding:8px 14px;border-radius:999px;background:var(--pin-red);color:#fff;font-size:12px;font-weight:700;line-height:1;opacity:0;transform:translateY(-4px);transition:.25s;pointer-events:none}
.pin:focus-visible{outline:3px solid var(--pin-blue);outline-offset:2px}
.pin:focus-visible::after,.pin:focus-visible .pin-view{opacity:1;transform:none}
@media(hover:hover){.pin:hover::after,.pin:hover .pin-view{opacity:1;transform:none}.pin:hover img{transform:scale(1.03)}}
.pin-grid.is-masonry{column-count:auto;display:flex;align-items:flex-start;gap:var(--pin-gap)}
.pin-col{flex:1 1 0;min-width:0;display:flex;flex-direction:column;gap:var(--pin-gap)}
.pin-grid.is-masonry .pin{margin:0}
.pin-grid.is-masonry .pin img:not(.is-loaded){opacity:0}
@media(max-width:1399px){.pin-grid{column-count:4}}
@media(max-width:991px){.pin-grid{column-count:3}}
@media(max-width:575px){.pin-gallery{--pin-gap:10px;padding-top:20px}.pin-grid{column-count:2}.pin{border-radius:12px}.pin-count span{display:none}.pin-view{display:none}}
@media(prefers-reduced-motion:reduce){.pin img,.pin::after,.pin-view{transition:none}}
</style>
<!-- End Pinterest-style Gallery Area -->
<!-- Start Subscriber Area -->
<div class="subscribe-area pt-70 pb-70" style="background-image: url('assets/img/sub.jpg');">
<div class="container">
<div class="row align-items-center">
<div class="col-lg-8">
<div class="subscribe-content">
<h2>Why Businesses Trust Shiva Punch Art</h2>
<p>
                        We deliver precision-engineered punch tools and die solutions for the printing and packaging industry. 
                        Our commitment to quality, advanced Shiva Punch Art, and timely delivery makes us a trusted manufacturing partner.
                    </p>
<div class="row mt-4">
<div class="col-md-6">
<ul class="list-unstyled text-white">
<li>✔ High-Precision Manufacturing</li>
<li>✔ Advanced CNC &amp; Laser Shiva Punch Art</li>
<li>✔ Customized Die Solutions</li>
</ul>
</div>
<div class="col-md-6">
<ul class="list-unstyled text-white">
<li>✔ High-Precision Manufacturing</li>
<li>✔ Advanced CNC &amp; Laser Shiva Punch Art</li>
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
<!-- Start Footer Area -->
<footer class="footer shiva-footer">
<div class="shiva-footer-main"><div class="container">
<div class="shiva-footer-brand">
<a class="shiva-footer-logo" href="index.html"><img alt="Shiva Punch Art" src="assets/img/logo-footer.png"/></a>
<p>Precision punch, die and embossing solutions for printing, packaging and converting applications.</p>
<a class="shiva-support" href="tel:+917208930779"><span class="support-icon"><i class="fas fa-phone-volume"></i></span><span><small>Talk To Our Support</small><strong>+91 72089 30779</strong></span><i class="fas fa-arrow-right"></i></a>
<div aria-label="Social links" class="shiva-footer-social">
<a aria-label="Facebook" href="https://www.facebook.com/p/Shiva-Punch-Art-100063900795995/" rel="noopener" target="_blank"><i class="fab fa-facebook-f"></i></a>
<a aria-label="LinkedIn" href="https://www.linkedin.com/in/shiva-punch-20215b313/" rel="noopener" target="_blank"><i class="fab fa-linkedin-in"></i></a>
<a aria-label="Instagram" href="https://www.instagram.com/shivapunchart1989" rel="noopener" target="_blank"><i class="fab fa-instagram"></i></a>
<a aria-label="Pinterest" href="https://www.pinterest.com/shivapunchart/" rel="noopener" target="_blank"><i class="fab fa-pinterest-p"></i></a>
        <a href="https://x.com/shivapunchart" target="_blank" rel="noopener noreferrer" aria-label="X.com" title="X.com" class="footer-x-link"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817-5.965 6.817H1.681l7.73-8.835L1.254 2.25H8.08l4.713 6.231L18.244 2.25Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z"/></svg></a>
        <a href="https://www.youtube.com/@shivapunchart9844" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
</div>
</div>
<div aria-label="Mobile footer navigation" class="shiva-mobile-footer-nav">
<a class="shiva-mobile-footer-row shiva-mobile-direct" href="about.html"><span>ABOUT</span><i aria-hidden="true" class="fas fa-plus"></i></a>
<a class="shiva-mobile-footer-row shiva-mobile-direct" href="packaging-templates.html"><span>PACKAGING TEMPLATES</span><i aria-hidden="true" class="fas fa-plus"></i></a>
      <a class="shiva-mobile-footer-row shiva-mobile-direct" href="exhibitions.html"><span>EXHIBITIONS</span><i class="fas fa-plus" aria-hidden="true"></i></a>
      <a class="shiva-mobile-footer-row shiva-mobile-direct" href="contact.html"><span>CONTACT US</span><i class="fas fa-plus" aria-hidden="true"></i></a>
      <details class="shiva-mobile-footer-row shiva-mobile-expand">
<summary><span>OUR PRODUCTS</span><i aria-hidden="true" class="fas fa-plus"></i></summary>
<div class="shiva-mobile-footer-content shiva-mobile-product-list">
<a href="our-product.html">01 · 3D Emboss</a><a href="our-product.html">02 · Laser Dies</a><a href="our-product.html">03 · Pre Rubber Dies</a>
<a href="our-product.html">04 · Punch + Emboss Online</a><a href="our-product.html">05 · Label Dies</a><a href="our-product.html">06 · Sandwich Dies</a>
<a href="our-product.html">07 · Stripping Die Male Female</a><a href="our-product.html">08 · Blanking Die Male Female</a>
<a href="our-product.html">09 · Creasing Pertinex</a><a href="our-product.html">10 · Creasing Counter Plate</a>
<a href="our-product.html">11 · Elecro Etg Foil Block</a><a href="our-product.html">12 · Mag Etg Foil Block</a>
<a href="our-product.html">13 · Texter Foil Block</a><a href="our-product.html">14 · CNC Foil Block</a>
<a href="our-product.html">15 · Foil + Emboss One Shot</a><a href="our-product.html">16 · Emboss Dies</a>
<a href="our-product.html">17 · Micro Effect Dies</a><a href="our-product.html">18 · Step Emboss</a><a href="our-product.html">19 · Braille Emboss</a>
</div>
</details>
</div>
<div class="shiva-footer-grid shiva-footer-grid-no-contact">
<section class="shiva-footer-card"><h3>Quick Links</h3><ul class="shiva-footer-links"><li><a href="about.html">About Us</a></li><li><a href="our-product.html">Our Products</a></li><li><a href="packaging-templates.html">Packaging  Templates</a></li><li><a href="exhibitions.html">Exhibitions</a></li><li><a href="contact.html">Contact Us</a></li></ul></section>
<section class="shiva-footer-card"><h3>Our Products <span>19 services</span></h3><div class="shiva-product-scroll"><a href="our-product.html"><span>01</span>3D Emboss</a><a href="our-product.html"><span>02</span>Laser Dies</a><a href="our-product.html"><span>03</span>Pre Rubber Dies</a><a href="our-product.html"><span>04</span>Punch + Emboss Online</a><a href="our-product.html"><span>05</span>Label Dies</a><a href="our-product.html"><span>06</span>Sandwich Dies</a><a href="our-product.html"><span>07</span>Stripping Die Male Female</a><a href="our-product.html"><span>08</span>Blanking Die Male Female</a><a href="our-product.html"><span>09</span>Creasing Pertinex</a><a href="our-product.html"><span>10</span>Creasing Counter Plate</a><a href="our-product.html"><span>11</span>Elecro Etg Foil Block</a><a href="our-product.html"><span>12</span>Mag Etg Foil Block</a><a href="our-product.html"><span>13</span>Texter Foil Block</a><a href="our-product.html"><span>14</span>CNC Foil Block</a><a href="our-product.html"><span>15</span>Foil + Emboss One Shot</a><a href="our-product.html"><span>16</span>Emboss Dies</a><a href="our-product.html"><span>17</span>Micro Effect Dies</a><a href="our-product.html"><span>18</span>Step Emboss</a><a href="our-product.html"><span>19</span>Braille Emboss</a></div></section>
<section class="shiva-footer-card shiva-footer-mobile-contact"><h3>Contact Us</h3><div class="shiva-footer-contact-links"><a href="tel:+917208930779"><i class="fas fa-phone"></i> +91 72089 30779</a><a href="mailto:shivapunchart@gmail.com"><i class="far fa-envelope"></i> shivapunchart@gmail.com</a><a href="contact.html"><i class="fas fa-arrow-right"></i> Contact Us</a></div></section>
</div>
</div></div>
<div class="footer-bottom shiva-footer-bottom"><div class="container"><p>© 2013–2026 <a href="https://shivapunchart.com/" rel="noopener" target="_blank">Shiva Punch Art</a><span>•</span> Developed &amp; Managed by <a href="https://tcongsinfotech.com/" rel="noopener" target="_blank">Tcongs Infotech</a></p></div></div>
</footer>
<div class="shiva-chatbot" id="shivaChatbot">
<button aria-expanded="false" aria-label="Open Shiva Punch Assistant" class="shiva-chatbot-launcher" id="shivaChatLauncher" type="button"><img alt="Shiva Punch Assistant" src="assets/img/shiva-bot-robot.svg"/><span class="shiva-chat-pulse"></span></button>
<div aria-hidden="true" class="shiva-chat-panel" id="shivaChatPanel">
<div class="shiva-chat-head"><div class="shiva-chat-avatar"><img alt="" src="assets/img/shiva-bot-robot.svg"/></div><div><strong>Shiva Punch Assistant</strong><small><span></span> Online · Quick Support</small></div><button aria-label="Close" id="shivaChatClose" type="button">×</button></div>
<div class="shiva-chat-body" id="shivaChatBody"><div class="shiva-bubble bot">Hi! 👋 I’m Shiva Punch Assistant. How can I help you today?</div></div>
<div class="shiva-chat-actions">
<button data-chat="products" type="button"><i class="fas fa-cubes"></i> Products</button>
<button data-chat="quote" type="button"><i class="fas fa-file-invoice"></i> Get a Quote</button>
<button data-chat="office" type="button"><i class="fas fa-map-marker-alt"></i> Office</button>
<button data-chat="factory" type="button"><i class="fas fa-industry"></i> Factory</button>
<button data-chat="contact" type="button"><i class="fas fa-phone"></i> Contact</button>
<button data-chat="whatsapp" type="button"><i class="fab fa-whatsapp"></i> WhatsApp</button>
</div>
<form class="shiva-chat-input" id="shivaChatForm"><input autocomplete="off" id="shivaChatInput" placeholder="Ask about products, quote..." type="text"/><button aria-label="Send message" type="submit"><i class="fas fa-paper-plane"></i></button></form>
</div>
</div>
<script src="assets/js/modernizr.min.js"></script><script src="assets/js/jquery-3.6.0.min.js"></script><script src="assets/js/popper.min.js"></script><script src="assets/js/bootstrap.min.js"></script><script src="assets/js/owl.carousel.min.js"></script><script src="assets/js/jquery.waypoints.min.js"></script><script src="assets/js/jquery.counterup.min.js"></script><script src="assets/js/jquery.magnific-popup.min.js"></script><script src="assets/js/mixitup.min.js"></script><script src="assets/js/wow.min.js"></script><script src="assets/js/script.js"></script><script src="assets/js/mobile-menu.js"></script><script src="assets/js/shiva-chatbot.js"></script>
<div aria-hidden="true" class="sp-lightbox" id="spLightbox">
<button aria-label="Close" class="sp-lightbox-close" id="spLbClose"><i class="fas fa-times"></i></button>
<button aria-label="Previous" class="sp-lightbox-prev" id="spLbPrev"><i class="fas fa-arrow-left"></i></button>
<img alt="Gallery image" id="spLbImage" src=""/>
<button aria-label="Next" class="sp-lightbox-next" id="spLbNext"><i class="fas fa-arrow-right"></i></button>
<div class="sp-lightbox-counter" id="spLbCounter"></div>
<button aria-label="Share this image" class="sp-lightbox-share" id="spLbShare" type="button"><i class="fas fa-share-alt"></i></button>
<a aria-label="Enquire about this product on WhatsApp" class="sp-lightbox-enquire" href="https://wa.me/918433538141" id="spLbEnquire" rel="noopener" target="_blank"><i class="fab fa-whatsapp"></i></a>
<div class="sp-lightbox-share-menu" id="spLbShareMenu" hidden>
<a data-share="whatsapp" href="#" rel="noopener" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
<a data-share="facebook" href="#" rel="noopener" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a>
<a data-share="telegram" href="#" rel="noopener" target="_blank"><i class="fab fa-telegram-plane"></i> Telegram</a>
<button data-share="copy" type="button"><i class="far fa-copy"></i> Copy link (Instagram)</button>
</div>
</div>
<script>
(function(){
  function init(){
    var grid=document.getElementById('pinGrid');
    if(!grid) return;
    var cards=[].slice.call(grid.querySelectorAll('.pin'));
    var ratios=cards.map(function(a){var i=a.querySelector('img');return i.getAttribute('height')/i.getAttribute('width');});

    /* Masonry: each pin goes into the currently shortest column, so newest templates stay at the top, row by row */
    var built=0;
    function colCount(){
      var w=grid.clientWidth;
      if(w<576) return 2;
      return Math.max(2,Math.min(6,Math.floor((w+16)/256)));
    }
    function layout(){
      var n=colCount();
      if(n===built) return;
      built=n;
      var cols=[],heights=[],i;
      for(i=0;i<n;i++){var c=document.createElement('div');c.className='pin-col';cols.push(c);heights.push(0);}
      cards.forEach(function(a,k){
        var m=0;for(i=1;i<n;i++){if(heights[i]<heights[m])m=i;}
        cols[m].appendChild(a);heights[m]+=ratios[k]+0.04;
      });
      grid.textContent='';
      cols.forEach(function(c){grid.appendChild(c);});
      grid.classList.add('is-masonry');
    }
    cards.forEach(function(a){
      var img=a.querySelector('img');
      function done(){img.classList.add('is-loaded');}
      if(img.complete) done(); else{img.addEventListener('load',done);img.addEventListener('error',done);}
    });
    layout();
    var t;window.addEventListener('resize',function(){clearTimeout(t);t=setTimeout(layout,120);});

    /* Lightbox */
    var lb=document.getElementById('spLightbox'),img=document.getElementById('spLbImage'),counter=document.getElementById('spLbCounter'),idx=0;
    function pad(x){return x<10?'0'+x:String(x);}
    function open(i){idx=i;var a=cards[idx];if(!a)return;img.src=a.href;img.alt=a.querySelector('img').alt||'Packaging template';counter.textContent=pad(idx+1)+' / '+pad(cards.length);lb.classList.add('is-open');lb.setAttribute('aria-hidden','false');var m=document.getElementById('spLbShareMenu');if(m)m.hidden=true;if(window.spUpdateEnquire)window.spUpdateEnquire();document.body.style.overflow='hidden';}
    function close(){lb.classList.remove('is-open');lb.setAttribute('aria-hidden','true');document.body.style.overflow='';img.src='';}
    function move(d){open((idx+d+cards.length)%cards.length);}
    cards.forEach(function(a,i){a.addEventListener('click',function(e){e.preventDefault();open(i);});});
    document.getElementById('spLbClose').addEventListener('click',close);
    document.getElementById('spLbPrev').addEventListener('click',function(){move(-1);});
    document.getElementById('spLbNext').addEventListener('click',function(){move(1);});
    lb.addEventListener('click',function(e){if(e.target===lb)close();});
    document.addEventListener('keydown',function(e){if(!lb.classList.contains('is-open'))return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')move(-1);if(e.key==='ArrowRight')move(1);});
    /* Share: native share sheet where available (WhatsApp, Instagram, ...), otherwise a link menu */
    var shareBtn=document.getElementById('spLbShare'),menu=document.getElementById('spLbShareMenu');
    function shareUrl(){return cards[idx].href;}
    var enquire=document.getElementById('spLbEnquire');
    function updateEnquire(){enquire.href='https://wa.me/918433538141?text='+encodeURIComponent('Hello Shiva Punch Art, I am interested in this product and would like a quote:\n'+shareUrl());}
    function hideMenu(){menu.hidden=true;}
    shareBtn.addEventListener('click',function(e){
      e.stopPropagation();
      var url=shareUrl(),title='Shiva Punch Art - '+(img.alt||'Packaging template');
      if(navigator.share){navigator.share({title:title,text:title,url:url}).catch(function(){});return;}
      var u=encodeURIComponent(url),t=encodeURIComponent(title);
      menu.querySelector('[data-share=whatsapp]').href='https://wa.me/?text='+t+'%20'+u;
      menu.querySelector('[data-share=facebook]').href='https://www.facebook.com/sharer/sharer.php?u='+u;
      menu.querySelector('[data-share=telegram]').href='https://t.me/share/url?url='+u+'&text='+t;
      menu.hidden=!menu.hidden;
    });
    menu.querySelector('[data-share=copy]').addEventListener('click',function(e){
      var b=e.currentTarget,url=shareUrl();
      function ok(){b.textContent='Link copied!';setTimeout(function(){b.innerHTML='<i class="far fa-copy"></i> Copy link (Instagram)';hideMenu();},1200);}
      if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(url).then(ok,function(){window.prompt('Copy this link',url);});}
      else window.prompt('Copy this link',url);
    });
    window.spUpdateEnquire=updateEnquire;
    lb.addEventListener('click',function(e){if(!menu.contains(e.target)&&e.target!==shareBtn)hideMenu();});
    var sx=0;
    lb.addEventListener('touchstart',function(e){sx=e.changedTouches[0].clientX;},{passive:true});
    lb.addEventListener('touchend',function(e){var dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>50)move(dx<0?1:-1);},{passive:true});
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>
<script id="shiva-mobile-footer-fix">
(function(){
  function initMobileFooter(){
    document.querySelectorAll('.shiva-mobile-footer-nav details.shiva-mobile-expand > summary').forEach(function(summary){
      if(summary.dataset.shivaBound==='1') return;
      summary.dataset.shivaBound='1';
      summary.addEventListener('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        var details=summary.parentElement;
        details.open=!details.open;
      });
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',initMobileFooter);
  else initMobileFooter();
})();
</script>

<script id="shiva-disable-context-menu">
(function(){
  document.addEventListener('contextmenu', function(e){ e.preventDefault(); }, {passive:false});
  document.addEventListener('dragstart', function(e){
    if (e.target && (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.tagName === 'A')) e.preventDefault();
  }, {passive:false});
})();
</script>
</body></html>
