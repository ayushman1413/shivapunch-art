<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$s = $pdo->prepare('SELECT * FROM products WHERE id=?');
$s->execute([$id]);
$p = $s->fetch(PDO::FETCH_ASSOC);
if (!$p) { header('Location: products.php'); exit; }

$opts = ['published', 'draft'];
foreach ($pdo->query("SHOW COLUMNS FROM products LIKE 'status'") as $c) {
    if (preg_match("/^enum\((.*)\)$/i", $c['Type'], $m)) {
        $opts = array_map(function($x){ return trim($x, "'"); }, explode(',', $m[1]));
    }
}

$errors = [];
$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'Files are too large for the server limit. Increase post_max_size and upload_max_filesize in php.ini.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        if ($title === '' || $desc === '') {
            $errors[] = 'Title and description are required.';
        } else {
            $pdo->prepare('UPDATE products SET title=?, description=? WHERE id=?')->execute([$title, $desc, $id]);
            if (array_key_exists('status', $p) && !empty($_POST['status']) && in_array($_POST['status'], $opts)) {
                $pdo->prepare('UPDATE products SET status=? WHERE id=?')->execute([$_POST['status'], $id]);
            }

            // delete selected photos
            $del = array_map('intval', $_POST['del'] ?? []);
            foreach ($del as $did) {
                $q = $pdo->prepare('SELECT image FROM product_images WHERE id=? AND product_id=?');
                $q->execute([$did, $id]);
                $img = $q->fetchColumn();
                if ($img !== false) {
                    remove_file($img);
                    $pdo->prepare('DELETE FROM product_images WHERE id=?')->execute([$did]);
                }
            }

            // new photos
            $new = save_files('images', $errors, ['jpg','jpeg','png','webp','gif']);
            if ($new) add_photos($pdo, $id, $new);

            // cover photo
            $cover = (int)($_POST['cover'] ?? 0);
            if ($cover && !in_array($cover, $del)) {
                $min = $pdo->prepare('SELECT COALESCE(MIN(sort_order),0) FROM product_images WHERE product_id=?');
                $min->execute([$id]);
                $pdo->prepare('UPDATE product_images SET sort_order=? WHERE id=? AND product_id=?')
                    ->execute([((int)$min->fetchColumn()) - 1, $cover, $id]);
            }
            sync_cover($pdo, $id);

            // video
            if (!empty($_POST['remove_video'])) {
                remove_file($p['video'] ?? '');
                $pdo->prepare("UPDATE products SET video='' WHERE id=?")->execute([$id]);
            }
            $v = save_files('video', $errors, ['mp4','mov']);
            if ($v) {
                remove_file($p['video'] ?? '');
                $pdo->prepare('UPDATE products SET video=? WHERE id=?')->execute([$v[0], $id]);
            }

            $saved = true;
            $s->execute([$id]);
            $p = $s->fetch(PDO::FETCH_ASSOC);
            if (!$errors) { header('Location: product-edit.php?id=' . $id . '&msg=updated'); exit; }
        }
    }
}

$ph = $pdo->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order, id');
$ph->execute([$id]);
$photos = $ph->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Edit Product';
$active = 'products';
require 'header.php';
?>
<style>
.pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin:8px 0 18px}
.ptile{border:1px solid #dfe5f2;border-radius:12px;padding:8px;background:#fff;text-align:center}
.ptile img{width:100%;height:110px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px}
.ptile label{display:flex;align-items:center;gap:6px;font-size:12px;margin:3px 0;cursor:pointer}
.ptile input{width:auto!important;margin:0!important}
</style>

<div class="head">
  <div>
    <h2>Edit Product</h2>
    <p>Update details and manage photos.</p>
  </div>
  <a href="products.php">Back to Products</a>
</div>

<?php if ($errors): ?>
<div class="card" style="border-left:4px solid #e63232;padding:14px 18px">
  <?php foreach ($errors as $e): ?><p><?= h($e) ?></p><?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($saved && !$errors): ?>
<div class="card" style="border-left:4px solid #1aa64a;padding:14px 18px"><p>Saved successfully.</p></div>
<?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data" class="form">
    <label>Product Title</label>
    <input type="text" name="title" required value="<?= h($p['title']) ?>">

    <label>Description</label>
    <textarea name="description" required><?= h($p['description']) ?></textarea>

    <?php if (array_key_exists('status', $p)): ?>
    <label>Status</label>
    <select name="status">
      <?php foreach ($opts as $o): ?><option value="<?= h($o) ?>" <?= $p['status'] === $o ? 'selected' : '' ?>><?= h(ucfirst($o)) ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>

    <label>Photos (<?= count($photos) ?>)</label>
    <div class="pgrid">
      <?php foreach ($photos as $i => $ph2): ?>
      <div class="ptile">
        <img src="<?= h(img_src($ph2['image'])) ?>" alt="">
        <label><input type="radio" name="cover" value="<?= (int)$ph2['id'] ?>" <?= $i === 0 ? 'checked' : '' ?>> Cover photo</label>
        <label><input type="checkbox" name="del[]" value="<?= (int)$ph2['id'] ?>"> Delete</label>
      </div>
      <?php endforeach; ?>
      <?php if (!$photos): ?><p>No photos yet.</p><?php endif; ?>
    </div>

    <div class="drops">
      <label class="drop">
        <input type="file" name="images[]" accept="image/*" multiple>
        <b>Add Photos</b><?= icon('cloud') ?>
       <small>Select one or many photos<br>JPG, PNG, WEBP · Max 2MB each</small>
      </label>
      <label class="drop">
        <input type="file" name="video" accept="video/mp4,video/quicktime">
        <b><?= !empty($p['video']) ? 'Replace Video' : 'Upload Video' ?></b><?= icon('play') ?>
       <small>MP4, MOV · Max 20MB</small>
      </label>
    </div>
    <?php if (!empty($p['video'])): ?>
      <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="remove_video" value="1" style="width:auto"> Remove current video</label>
    <?php endif; ?>

    <button class="pub" type="submit"><?= icon('send') ?> Save Changes</button>
  </form>
</div>
<script>
(function(){
  var v=document.querySelector('input[type=file][name="video"]');
  if(!v) return;
  var wrap=document.createElement('div');
  wrap.style.cssText='margin:12px 0';
  var anchor=v.closest('.drops')||v.closest('label')||v.parentElement;
  anchor.insertAdjacentElement('afterend',wrap);
  var url=null;
  v.addEventListener('change',function(){
    if(url){URL.revokeObjectURL(url);url=null;}
    wrap.innerHTML='';
    wrap.style.color='';
    var f=v.files[0];
    if(!f) return;
    if(f.size>20*1024*1024){
      wrap.textContent='Video is larger than 20 MB.';
      wrap.style.color='#dc2626';
      v.value='';
      return;
    }
    url=URL.createObjectURL(f);
    var el=document.createElement('video');
    el.src=url; el.controls=true;
    el.style.cssText='width:100%;max-width:420px;border-radius:12px;display:block;background:#000';
    wrap.appendChild(el);
  });
})();
</script>
<?php require 'footer.php'; ?>