<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$s = $pdo->prepare('SELECT * FROM products WHERE id=?');
$s->execute([$id]);
$p = $s->fetch(PDO::FETCH_ASSOC);
if (!$p) { header('Location: products.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST)) {
        $errors[] = 'Files are too large for the server limit.';
    } else {
        $new = save_files('images', $errors, ['jpg','jpeg','png','webp','gif']);
        if ($new) {
            $q = $pdo->prepare('SELECT id, image FROM product_images WHERE product_id=?');
            $q->execute([$id]);
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $old) {
                remove_file($old['image']);
                $pdo->prepare('DELETE FROM product_images WHERE id=?')->execute([$old['id']]);
            }
            add_photos($pdo, $id, $new);
            sync_cover($pdo, $id);
        }
        $v = save_files('video', $errors, ['mp4','mov']);
        if ($v) {
            remove_file($p['video'] ?? '');
            $pdo->prepare('UPDATE products SET video=? WHERE id=?')->execute([$v[0], $id]);
        }
        if (!$errors) {
            if (!$new && !$v) {
                $errors[] = 'Please choose new photos or a video to replace.';
            } else {
                header('Location: products.php?msg=updated'); exit;
            }
        }
    }
}

$pageTitle = 'Replace Media';
$active = 'products';
require 'header.php';
?>
<div class="head">
  <div>
    <h2>Replace Photos / Video</h2>
    <p><?= h($p['title']) ?></p>
  </div>
  <a href="products.php">Back to Products</a>
</div>

<?php if ($errors): ?>
<div class="card" style="border-left:4px solid #e63232;padding:14px 18px">
  <?php foreach ($errors as $e): ?><p><?= h($e) ?></p><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data" class="form" onsubmit="return confirm('Old photos/video will be deleted permanently. Continue?')">
    <input type="hidden" name="go" value="1">
    <p style="font-size:13px;color:#4a5578">Choose new photos to replace ALL old photos, and/or a new video to replace the old video. Leave a box empty to keep it as it is.</p>
    <div class="drops">
      <label class="drop">
        <input type="file" name="images[]" accept="image/*" multiple>
        <b>New Photos</b><?= icon('cloud') ?>
        <small>Replaces all old photos<br>JPG, PNG, WEBP · Max 2MB each</small>
      </label>
      <label class="drop">
        <input type="file" name="video" accept="video/mp4,video/quicktime">
        <b>New Video</b><?= icon('play') ?>
        <small>Replaces old video<br>MP4, MOV · Max 20MB</small>
      </label>
    </div>
    <button class="pub" type="submit"><?= icon('send') ?> Replace Now</button>
  </form>
</div>
<style>
.rp-grid{display:flex;flex-wrap:wrap;gap:12px;margin:14px 0}
.rp-item{position:relative;width:110px;height:85px}
.rp-item img{width:100%;height:100%;object-fit:cover;border-radius:8px;display:block}
.rp-item button{position:absolute;top:-6px;right:-6px;width:22px;height:22px;border:0;border-radius:50%;background:#e63232;color:#fff;cursor:pointer;padding:0;font-size:12px;line-height:22px}
</style>
<script>
(function(){
  var pin=document.querySelector('input[name="images[]"]');
  var vin=document.querySelector('input[name="video"]');
  var drops=document.querySelector('.drops');
  if(!pin||!vin||!drops) return;

  var grid=document.createElement('div'); grid.className='rp-grid';
  var vwrap=document.createElement('div'); vwrap.style.margin='14px 0';
  drops.insertAdjacentElement('afterend',vwrap);
  drops.insertAdjacentElement('afterend',grid);

  var files=[];
  function sync(){
    var dt=new DataTransfer();
    files.forEach(function(f){dt.items.add(f);});
    pin.files=dt.files;
  }
  function draw(){
    grid.innerHTML='';
    files.forEach(function(f,i){
      var d=document.createElement('div'); d.className='rp-item';
      var im=document.createElement('img'); im.src=URL.createObjectURL(f);
      var b=document.createElement('button'); b.type='button'; b.textContent='✕';
      b.onclick=function(){files.splice(i,1);sync();draw();};
      d.appendChild(im); d.appendChild(b); grid.appendChild(d);
    });
  }
  pin.addEventListener('change',function(){
    Array.prototype.forEach.call(pin.files,function(f){files.push(f);});
    sync(); draw();
  });

  var url=null;
  vin.addEventListener('change',function(){
    if(url){URL.revokeObjectURL(url);url=null;}
    vwrap.innerHTML=''; vwrap.style.color='';
    var f=vin.files[0];
    if(!f) return;
    if(f.size>20*1024*1024){
      vwrap.textContent='Video is larger than 20 MB.';
      vwrap.style.color='#dc2626';
      vin.value='';
      return;
    }
    url=URL.createObjectURL(f);
    var el=document.createElement('video');
    el.src=url; el.controls=true;
    el.style.cssText='width:100%;max-width:420px;border-radius:12px;display:block;background:#000';
    vwrap.appendChild(el);
  });
})();
</script>
<?php require 'footer.php'; ?>