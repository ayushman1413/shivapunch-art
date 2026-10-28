<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

$errors = [];
$savedId = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'Files are too large for the server limit. Increase post_max_size and upload_max_filesize in php.ini.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        if ($title === '' || $desc === '') {
            $errors[] = 'Title and description are required.';
        } else {
            $imgExt = ['jpg','jpeg','png','webp','gif'];
            $imgs = array_merge(save_files('images', $errors, $imgExt), save_files('image', $errors, $imgExt));
            $vid  = save_files('video', $errors, ['mp4','mov']);

            $cols = ['title','description','image']; $vals = [$title, $desc, ''];
            if ($vid) { $cols[] = 'video'; $vals[] = $vid[0]; }
            $stv = default_status($pdo);
            if ($stv !== null) { $cols[] = 'status'; $vals[] = $stv; }
            $sql = 'INSERT INTO products (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')';
            $pdo->prepare($sql)->execute($vals);
            $savedId = (int)$pdo->lastInsertId();
            add_photos($pdo, $savedId, $imgs);

           if (!$errors) { header('Location: products.php?msg=added'); exit; }
        }
    }
}

$pageTitle = 'Add Product';
$active = 'add';
require 'header.php';
?>
<div class="head">
  <div>
    <h2>Add Product</h2>
    <p>Add a new product with one or more photos.</p>
  </div>
</div>

<?php if ($errors): ?>
<div class="card" style="border-left:4px solid #e63232;padding:14px 18px">
  <?php if ($savedId): ?><p><b>Product saved, but some files had problems:</b></p><?php endif; ?>
  <?php foreach ($errors as $e): ?><p><?= h($e) ?></p><?php endforeach; ?>
  <?php if ($savedId): ?><p><a href="product-edit.php?id=<?= $savedId ?>">Open the product to fix photos</a></p><?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data" class="form">
    <label>Product Title <span style="color:#e63232">*</span></label>
    <input type="text" name="title" required value="<?= h($_POST['title'] ?? '') ?>">

    <label>Description <span style="color:#e63232">*</span></label>
    <textarea name="description" required><?= h($_POST['description'] ?? '') ?></textarea>

    <div class="drops">
      <label class="drop">
        <input type="file" name="images[]" accept="image/*" multiple>
        <b>Upload Photos</b><?= icon('cloud') ?>
      <small>Select one or many photos<br>JPG, PNG, WEBP · Max 2MB each</small>
      </label>
      <label class="drop">
        <input type="file" name="video" accept="video/mp4,video/quicktime">
        <b>Upload Video</b><?= icon('play') ?>
      <small>Optional<br>MP4, MOV · Max 20MB</small>
      </label>
    </div>
    <button class="pub" type="submit"><?= icon('send') ?> Publish Product</button>
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