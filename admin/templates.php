<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

$active = 'templates';
$title  = 'Packaging Templates';
$dir    = dirname(__DIR__) . '/templates-files/';
if (!is_dir($dir)) { mkdir($dir, 0777, true); }

ensure_template_schema($pdo);

$errors = [];
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

// Current category view: 'all', 0 (uncategorized) or a category id. Forms post back to this same URL.
$filter = 'all';
if (isset($_GET['cat']) && $_GET['cat'] !== 'all') { $filter = max(0, (int)$_GET['cat']); }

function back_to($filter, $msg) {
    header('Location: templates.php?' . http_build_query(['cat' => $filter, 'msg' => $msg]));
    exit;
}
function category_exists($pdo, $id) {
    if ($id < 1) return false;
    $st = $pdo->prepare('SELECT 1 FROM template_categories WHERE id=?');
    $st->execute([$id]);
    return (bool)$st->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'Files are too large for the server limit.';
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare('SELECT file FROM packaging_templates WHERE id=?');
        $st->execute([$id]);
        $f = $st->fetchColumn();
        if ($f) {
            if (strpos($f, '/') === false) { @unlink($dir . basename($f)); }
            $pdo->prepare('DELETE FROM packaging_templates WHERE id=?')->execute([$id]);
        }
        back_to($filter, 'deleted');
    } elseif (($_POST['action'] ?? '') === 'replace') {
        $id = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare('SELECT file FROM packaging_templates WHERE id=?');
        $st->execute([$id]);
        $old = $st->fetchColumn();
        $up  = $_FILES['image'] ?? null;
        if (!$old) {
            $errors[] = 'Image not found.';
        } elseif (!$up || $up['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Replace failed. Please choose an image.';
        } else {
            $ext = strtolower(pathinfo($up['name'], PATHINFO_EXTENSION));
           if (!in_array($ext, $allowed)) {
            $errors[] = 'File type not allowed.';
        } elseif ($up['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image is larger than 2 MB.';
        } else {
                $new = upload_name($dir, $up['name']);
                if (move_uploaded_file($up['tmp_name'], $dir . $new)) {
                   $pdo->prepare('UPDATE packaging_templates SET file=?, title=? WHERE id=?')->execute([$new, pathinfo($up['name'], PATHINFO_FILENAME), $id]);
                    if (strpos($old, '/') === false) { @unlink($dir . basename($old)); }
                    back_to($filter, 'replaced');
                } else {
                    $errors[] = 'Could not save the new file.';
                }
            }
        }
    } elseif (($_POST['action'] ?? '') === 'add_category') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Category name is required (max 100 characters).';
        } else {
            $st = $pdo->prepare('SELECT id FROM template_categories WHERE name=?');
            $st->execute([$name]);
            if ($st->fetchColumn()) {
                $errors[] = 'A category with this name already exists.';
            } else {
                $pdo->prepare('INSERT INTO template_categories (name) VALUES (?)')->execute([$name]);
                back_to((int)$pdo->lastInsertId(), 'category created');
            }
        }
    } elseif (($_POST['action'] ?? '') === 'delete_category') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare('UPDATE packaging_templates SET category_id=NULL WHERE category_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM template_categories WHERE id=?')->execute([$id]);
        back_to('all', 'category deleted');
    } elseif (($_POST['action'] ?? '') === 'move') {
        $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
        $to  = (int)($_POST['category_id'] ?? 0);
        if (!$ids) {
            $errors[] = 'Select at least one image to move.';
        } elseif ($to > 0 && !category_exists($pdo, $to)) {
            $errors[] = 'Category not found.';
        } else {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("UPDATE packaging_templates SET category_id=? WHERE id IN ($in)")
                ->execute(array_merge([$to > 0 ? $to : null], $ids));
            back_to($filter, 'moved ' . count($ids));
        }
    } elseif (($_POST['action'] ?? '') === 'upload') {
        $count = 0;
        $cid = (int)($_POST['category_id'] ?? 0);
        $cid = category_exists($pdo, $cid) ? $cid : null;
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) { $errors[] = "$name failed to upload."; continue; }
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) { $errors[] = "$name: file type not allowed."; continue; }
                if ($_FILES['images']['size'][$i] > 2 * 1024 * 1024) { $errors[] = "$name: image is larger than 2 MB."; continue; }
                $new = upload_name($dir, $name);
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dir . $new)) {
                    $t = trim($_POST['title'] ?? '');
                    if ($t === '') { $t = pathinfo($name, PATHINFO_FILENAME); }
                    $pdo->prepare("INSERT INTO packaging_templates (title,file,type,status,category_id) VALUES (?,?,'image','published',?)")
                        ->execute([$t, $new, $cid]);
                    $count++;
                }
            }
        } else {
            $errors[] = 'Please choose at least one image.';
        }
        if (!$errors) { back_to($filter, 'added'); }
    }
}

$where = '';
$args  = [];
if ($filter === 0) { $where = ' WHERE category_id IS NULL'; }
elseif ($filter !== 'all') { $where = ' WHERE category_id=?'; $args[] = $filter; }
$st = $pdo->prepare('SELECT * FROM packaging_templates' . $where . ' ORDER BY id DESC');
$st->execute($args);
$rows = $st->fetchAll();

$cats = $pdo->query('SELECT c.id, c.name, COUNT(t.id) AS n FROM template_categories c
                     LEFT JOIN packaging_templates t ON t.category_id = c.id GROUP BY c.id, c.name ORDER BY c.name')->fetchAll();
$totalAll = (int)$pdo->query('SELECT COUNT(*) FROM packaging_templates')->fetchColumn();
$totalNone = (int)$pdo->query('SELECT COUNT(*) FROM packaging_templates WHERE category_id IS NULL')->fetchColumn();
$curName = null;
foreach ($cats as $c) { if ($c['id'] == $filter) { $curName = $c['name']; } }
function cat_options($cats, $selected, $noneLabel) {
    $o = '<option value="0">' . h($noneLabel) . '</option>';
    foreach ($cats as $c) {
        $o .= '<option value="' . (int)$c['id'] . '"' . ($c['id'] == $selected ? ' selected' : '') . '>' . h($c['name']) . '</option>';
    }
    return $o;
}
require 'header.php';
?>
<h1>Packaging Templates</h1>
<p>Images added here appear on the website Packaging Templates page.</p>

<?php if (isset($_GET['msg'])): ?>
  <p style="color:green">Done: <?= htmlspecialchars($_GET['msg']) ?>.</p>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<style>
.tc-tabs{display:flex;flex-wrap:wrap;gap:8px;margin:14px 0}
.tc-tabs a{padding:7px 14px;border-radius:999px;background:#fff;color:#1c2340;font-size:13px;font-weight:600;box-shadow:0 0 0 1px #e6ecf7}
.tc-tabs a.on{background:#2f5bff;color:#fff;box-shadow:none}
.tc-tabs small{opacity:.7;margin-left:4px}
.tc-box{background:#fff;border-radius:12px;padding:14px;margin:12px 0;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.tc-box input[type=text],.tc-box select{padding:9px 12px;border:1px solid #e6ecf7;border-radius:8px;font-size:13px}
.tc-btn{padding:9px 20px;border:0;border-radius:10px;color:#fff;font-weight:700;cursor:pointer;background:linear-gradient(135deg,#f97316,#1261c9)}
.tc-card{position:relative;background:#fff;border-radius:12px;padding:8px}
.tc-card .pick{position:absolute;top:14px;left:14px;width:20px;height:20px;cursor:pointer;z-index:2}
</style>

<div class="tc-tabs">
  <a href="templates.php" class="<?= $filter === 'all' ? 'on' : '' ?>">All<small><?= $totalAll ?></small></a>
  <a href="templates.php?cat=0" class="<?= $filter === 0 ? 'on' : '' ?>">Uncategorized<small><?= $totalNone ?></small></a>
  <?php foreach ($cats as $c): ?>
    <a href="templates.php?cat=<?= (int)$c['id'] ?>" class="<?= $c['id'] == $filter ? 'on' : '' ?>"><?= h($c['name']) ?><small><?= (int)$c['n'] ?></small></a>
  <?php endforeach; ?>
</div>

<form method="post" class="tc-box">
  <input type="hidden" name="action" value="add_category">
  <input type="text" name="name" placeholder="New category name" maxlength="100" required>
  <button type="submit" class="tc-btn">Create category</button>
  <?php if ($curName !== null): ?>
    <button type="submit" form="delCat" class="tc-btn" style="background:#dc2626;margin-left:auto">Delete “<?= h($curName) ?>”</button>
  <?php endif; ?>
</form>
<?php if ($curName !== null): ?>
<form method="post" id="delCat" onsubmit="return confirm('Delete this category? Its images are kept and become Uncategorized.')">
  <input type="hidden" name="action" value="delete_category">
  <input type="hidden" name="id" value="<?= (int)$filter ?>">
</form>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" style="margin:16px 0">
  <input type="hidden" name="action" value="upload">
  <select name="category_id" style="padding:9px 12px;border:1px solid #e6ecf7;border-radius:8px;font-size:13px;margin-bottom:10px">
    <?= cat_options($cats, $filter, 'Uncategorized') ?>
  </select><br>
  <input type="file" name="images[]" multiple accept="image/*">
  <small style="display:block;margin:6px 0 10px;color:#64748b;font-size:13px;line-height:1.4">Max 2MB per image. Uploaded into the category selected above.</small>
  <button type="submit" class="tc-btn" style="padding:10px 26px">Upload</button>
</form>

<p><b><?= count($rows) ?></b> templates<?= $curName !== null ? ' in ' . h($curName) : ($filter === 0 ? ' uncategorized' : '') ?></p>

<?php if ($rows): ?>
<form method="post" id="moveForm" class="tc-box">
  <input type="hidden" name="action" value="move">
  <label style="font-weight:600"><input type="checkbox" id="selAll"> Select all</label>
  <span>Move selected to</span>
  <select name="category_id"><?= cat_options($cats, 0, 'Uncategorized') ?></select>
  <button type="submit" class="tc-btn">Move selected</button>
</form>
<?php endif; ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px">
<?php foreach ($rows as $r): ?>
  <div class="tc-card">
    <input type="checkbox" class="pick" name="ids[]" value="<?= (int)$r['id'] ?>" form="moveForm" aria-label="Select image">
    <img src="<?= htmlspecialchars((strpos($r['file'], '/') === false ? '../templates-files/' : '../') . $r['file']) ?>" style="width:100%;height:140px;object-fit:cover;border-radius:8px">
    <div style="display:flex;gap:6px;margin-top:6px">
      <form method="post" enctype="multipart/form-data" style="flex:1;margin:0">
        <input type="hidden" name="action" value="replace">
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <input type="file" name="image" accept="image/*" id="rep<?= (int)$r['id'] ?>" style="display:none" onchange="this.form.submit()">
        <label for="rep<?= (int)$r['id'] ?>" style="display:block;text-align:center;padding:8px 0;border-radius:8px;color:#fff;font-weight:700;cursor:pointer;background:linear-gradient(135deg,#1261c9,#0b3d91)">Replace</label>
      </form>
      <form method="post" onsubmit="return confirm('Delete this image?')" style="flex:1;margin:0">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button type="submit" style="width:100%;padding:8px 0;border:0;border-radius:8px;color:#fff;font-weight:700;cursor:pointer;background:#dc2626">Delete</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
</div>
<script>
(function(){
  var all=document.getElementById('selAll');
  if(all) all.addEventListener('change',function(){
    document.querySelectorAll('input.pick').forEach(function(c){c.checked=all.checked;});
  });
})();
</script>
<script>
(function(){
  var input=document.querySelector('input[type=file][name="images[]"]');
  if(!input) return;
  var store=new DataTransfer();
  var box=document.createElement('div'); box.className='sp-prev';
  input.insertAdjacentElement('afterend',box);
  function key(f){return f.name+'|'+f.size+'|'+f.lastModified;}
  function render(){
    box.innerHTML='';
    Array.prototype.forEach.call(store.files,function(f,i){
      var d=document.createElement('div'); d.className='it';
      var im=document.createElement('img'); im.src=URL.createObjectURL(f);
      var b=document.createElement('button'); b.type='button'; b.textContent='✕';
      b.onclick=function(){
        var n=new DataTransfer();
        Array.prototype.forEach.call(store.files,function(x,j){ if(j!==i) n.items.add(x); });
        store=n; render();
      };
      d.appendChild(im); d.appendChild(b); box.appendChild(d);
    });
    input.files=store.files;
  }
  input.addEventListener('change',function(){
    var have={};
    Array.prototype.forEach.call(store.files,function(f){ have[key(f)]=1; });
    Array.prototype.forEach.call(input.files,function(f){ if(!have[key(f)]) store.items.add(f); });
    render();
  });
})();
</script>
<?php require 'footer.php'; ?>