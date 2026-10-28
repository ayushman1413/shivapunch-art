<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

$n = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$w = "created_at >= NOW() - INTERVAL 7 DAY";
$totalProducts = $n("SELECT COUNT(*) FROM products");
$prodWeek   = $n("SELECT COUNT(*) FROM products WHERE $w");
$tplImages  = $n("SELECT COUNT(*) FROM packaging_templates WHERE type='image'");
$tplImagesW = $n("SELECT COUNT(*) FROM packaging_templates WHERE type='image' AND $w");
$tplVideos  = $n("SELECT COUNT(*) FROM packaging_templates WHERE type='video'");
$tplVideosW = $n("SELECT COUNT(*) FROM packaging_templates WHERE type='video' AND $w");
$recentCount = $prodWeek + $tplImagesW + $tplVideosW;
$recent = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Dashboard';
$active = 'dashboard';
$bell = $recentCount;
require 'header.php';
?>
<div class="head">
  <div>
    <h2>Dashboard</h2>
    <p>Manage your products, packaging templates and website content from here.</p>
  </div>
  <div class="date"><?= icon('cal') ?> Today, <?= date('j M Y') ?></div>
</div>

<div class="stats">
  <div class="stat"><div class="ico c1"><?= icon('box') ?></div><div><span>Total Products</span><b><?= $totalProducts ?></b><small>+<?= $prodWeek ?> this week</small></div></div>
  <div class="stat"><div class="ico c2"><?= icon('image') ?></div><div><span>Template Images</span><b><?= $tplImages ?></b><small>+<?= $tplImagesW ?> this week</small></div></div>
  <div class="stat"><div class="ico c3"><?= icon('play') ?></div><div><span>Template Videos</span><b><?= $tplVideos ?></b><small>+<?= $tplVideosW ?> this week</small></div></div>
  <div class="stat"><div class="ico c4"><?= icon('cloud') ?></div><div><span>Recent Uploads</span><b><?= $recentCount ?></b><small>last 7 days</small></div></div>
</div>

<div class="card">
  <div class="card-h">
    <div class="pl" style="background:#eaf0ff;color:#1d3bd1"><?= icon('box') ?></div>
    <div><b>Recent Products</b><small>Latest products added to your website.</small></div>
    <a class="vall" href="products.php">View All Products <?= icon('arrow') ?></a>
  </div>
  <table class="resp">
    <thead><tr><th>#</th><th>Thumbnail</th><th>Product Title</th><th>Media Type</th><th>Status</th><th>Date Added</th><th>Actions</th></tr></thead>
    <?php if (!$recent): ?>
      <tr><td colspan="7">No products yet. Use the Products page to add your first product.</td></tr>
    <?php endif; ?>
    <?php foreach ($recent as $i => $p): ?>
    <tr>
      <td data-label="#"><?= $i + 1 ?></td>
      <td data-label="Thumbnail">
        <?php if ($p['image']): ?><img src="<?= htmlspecialchars(img_src($p['image'])) ?>" alt="">
        <?php else: ?><span class="vt"><?= icon('play') ?></span><?php endif; ?>
      </td>
      <td data-label="Product Title"><?= htmlspecialchars($p['title']) ?></td>
      <td data-label="Media Type"><?= $p['video'] ? 'Video' : 'Image' ?></td>
      <td data-label="Status"><span class="pill <?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
      <td data-label="Date Added"><?= date('d M Y, h:i A', strtotime($p['created_at'])) ?></td>
      <td data-label="Actions"><div class="act">
        <a class="ib" title="Edit" href="product-edit.php?id=<?= $p['id'] ?>"><?= icon('pencil') ?></a>
        <a class="ib del" title="Delete" href="product-delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')"><?= icon('trash') ?></a>
      </div></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require 'footer.php'; ?>