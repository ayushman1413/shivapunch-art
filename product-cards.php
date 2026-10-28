<?php
require_once __DIR__ . '/admin/config.php';
if (!function_exists('h')) { function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
$products = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$imgStmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY sort_order, id");
foreach ($products as $i => $p):
  if (isset($p['status']) && in_array(strtolower((string)$p['status']), ['0','inactive','hidden','draft','disabled'])) continue;
  $imgStmt->execute([$p['id']]);
  $imgs = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
  if (!$imgs && !empty($p['image'])) $imgs = [$p['image']];
  $cid = 'productCarouselFinal' . ($i + 1);
?>
<div class="col-lg-4 col-md-6 mb-30">
<div class="info-box-1<?= $p['title'] === 'Sandwich Dies' ? ' sandwich-product-card' : '' ?>"><div class="info-box-1-inner">
<div class="carousel slide" data-bs-interval="3000" data-bs-pause="hover" data-bs-ride="carousel" data-bs-touch="true" data-bs-wrap="true" id="<?= $cid ?>">
<div class="carousel-inner">
<?php foreach ($imgs as $n => $img): ?>
<div class="carousel-item<?= $n === 0 ? ' active' : '' ?>"><img alt="<?= h($p['title']) ?>" class="d-block w-100 product-img" src="<?= h($img) ?>"/><button aria-label="View <?= h($p['title']) ?>" class="product-image-click-target" data-image-alt="<?= h($p['title']) ?>" data-image-src="<?= h($img) ?>" type="button"></button></div>
<?php endforeach; ?>
<?php if (!empty($p['video'])): ?>
<div class="carousel-item<?= empty($imgs) ? ' active' : '' ?>" data-bs-interval="60000"><video class="d-block w-100" controls muted playsinline preload="metadata" style="max-height:320px;object-fit:contain;background:#000"><source src="<?= h($p['video']) ?>"></video></div>
<?php endif; ?>
</div>
<?php if (count($imgs) + (!empty($p['video']) ? 1 : 0) > 1): ?>
<button aria-label="Previous image" class="carousel-control-prev" data-bs-slide="prev" data-bs-target="#<?= $cid ?>" type="button"><span aria-hidden="true" class="carousel-control-prev-icon"></span></button>
<button aria-label="Next image" class="carousel-control-next" data-bs-slide="next" data-bs-target="#<?= $cid ?>" type="button"><span aria-hidden="true" class="carousel-control-next-icon"></span></button>
<?php endif; ?>
</div>
<div class="content mt-3"><h3><?= h($p['title']) ?></h3><p><?= h($p['description']) ?></p></div>
</div></div>
</div>
<script>
(function(){
  function playActive(c){
    var a=c.querySelector('.carousel-item.active video');
    if(a){ a.currentTime=0; var p=a.play(); if(p&&p.catch) p.catch(function(){}); }
  }
  document.addEventListener('slid.bs.carousel',function(e){
    var c=e.target;
    c.querySelectorAll('video').forEach(function(v){ v.pause(); });
    var v=e.relatedTarget && e.relatedTarget.querySelector('video');
    if(v){ v.currentTime=0; var p=v.play(); if(p&&p.catch) p.catch(function(){}); }
  });
  document.addEventListener('ended',function(e){
    if(!e.target || e.target.tagName!=='VIDEO') return;
    var c=e.target.closest('.carousel');
    if(!c) return;
    var next=c.querySelector('.carousel-control-next');
    if(next) next.click();
  },true);
  window.addEventListener('load',function(){
    document.querySelectorAll('.carousel').forEach(playActive);
  });
})();
</script>
<?php endforeach; ?>