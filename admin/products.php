<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        try {
            delete_product($pdo, $id);
            $msg = 'Product deleted successfully!';
        } catch (Exception $e) {
            $err = 'Failed to delete product: ' . $e->getMessage();
        }
    }
}

$rows = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM product_images i WHERE i.product_id = p.id) AS pc FROM products p ORDER BY p.id ASC")->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Products';
$active = 'products';
require 'header.php';
?>
<style>
.resp td img{width:120px!important;height:90px!important;object-fit:cover!important;border-radius:10px!important;display:block}
.resp td .vt{width:120px!important;height:90px!important}
.resp td.desc{max-width:380px;font-size:13px;line-height:1.5;color:#4a5578}
.vbadge{display:inline-block;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600}
.vbadge.yes{background:#dcfce7;color:#15803d}
.vbadge.no{background:#eef1f7;color:#8a94ad}
@media(max-width:767px){.resp td img{width:100%!important;max-width:220px;height:150px!important}}
</style>
<div class="head">
  <div>
    <h2>Products</h2>
    <p>All products shown on the website, in the same order as the Our Product page.</p>
  </div>
  <a class="pub" href="product-add.php" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px"><?= icon('plus') ?> Add Product</a>
</div>

<?php if (isset($msg) || isset($_GET['msg'])): ?>
  <div style="background:#dcfce7;color:#15803d;padding:12px;border-radius:8px;margin-bottom:20px;font-weight:500;">
    <?= h($msg ?? 'Action completed successfully.') ?>
  </div>
<?php endif; ?>
<?php if (isset($err)): ?>
  <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:8px;margin-bottom:20px;font-weight:500;">
    <?= h($err) ?>
  </div>
<?php endif; ?>

<div class="card">
  <table class="resp">
    <thead><tr><th>#</th><th>Thumbnail</th><th>Product Title</th><th>Description</th><th>Photos</th><th>Video</th><th>Status</th><th>Actions</th></tr></thead>
    <?php if (!$rows): ?>
      <tr><td colspan="8">No products yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $i => $p): ?>
    <tr>
      <td data-label="#"><?= $i + 1 ?></td>
      <td data-label="Thumbnail"><?php if ($p['image']): ?><img src="<?= h(img_src($p['image'])) ?>" alt=""><?php else: ?><span class="vt"><?= icon('image') ?></span><?php endif; ?></td>
      <td data-label="Product Title"><?= h($p['title']) ?></td>
      <td data-label="Description" class="desc"><?= h(mb_strimwidth((string)$p['description'], 0, 140, '...')) ?></td>
      <td data-label="Photos"><?= (int)$p['pc'] ?></td>
      <td data-label="Video"><?= !empty($p['video']) ? 1 : 0 ?></td>
      <td data-label="Status"><?php $st = $p['status'] ?? 'active'; ?><span class="pill <?= h($st) ?>"><?= h(ucfirst((string)$st)) ?></span></td>
      <td data-label="Actions"><div class="act">
        <a class="ib" title="Edit" href="product-edit.php?id=<?= (int)$p['id'] ?>"><?= icon('pencil') ?></a>
        <a class="ib" title="Replace photos / video" href="product-replace.php?id=<?= (int)$p['id'] ?>"><?= icon('cloud') ?></a>
        <a class="ib del" title="Delete" href="products.php?action=delete&id=<?= (int)$p['id'] ?>" onclick="return confirm('Delete this product and all its photos?')"><?= icon('trash') ?></a>
      </div></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require 'footer.php'; ?>