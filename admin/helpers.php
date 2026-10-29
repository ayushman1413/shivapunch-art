<?php
if (!function_exists('h')) { function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }

// Upload size limits
if (!defined('MAX_IMAGE_BYTES')) { define('MAX_IMAGE_BYTES', 2 * 1024 * 1024); }   // 2 MB
if (!defined('MAX_VIDEO_BYTES')) { define('MAX_VIDEO_BYTES', 20 * 1024 * 1024); }  // 20 MB

function img_src($p){
    $p = (string)$p;
    if ($p === '') return '';
    if (preg_match('#^(https?:)?//#', $p)) return $p;
    if (strpos($p, 'assets/') === 0 || strpos($p, 'uploads/') === 0) return '../' . $p;
    return '../uploads/products/' . $p;
}

function remove_file($p){
    if (strpos((string)$p, 'uploads/products/') === 0) {
        $f = dirname(__DIR__) . '/' . $p;
        if (is_file($f)) @unlink($f);
    }
}

function default_status($pdo){
    $v = null;
    foreach ($pdo->query("SHOW COLUMNS FROM products LIKE 'status'") as $c) {
        if ($c['Default'] === null && $c['Null'] === 'NO') {
            if (preg_match("/^enum\((.*)\)$/i", $c['Type'], $m)) {
                $o = array_map(function($x){ return trim($x, "'"); }, explode(',', $m[1]));
                $v = $o[0];
                foreach ($o as $x) { if (strtolower($x) === 'active') { $v = $x; break; } }
            } else { $v = 1; }
        }
    }
    return $v;
}

// Keeps the name the admin uploaded (made URL-safe); adds -2, -3... only if that name is already taken.
function upload_name($dir, $original, $keep = null){
    $ext  = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $base = preg_replace('/[^A-Za-z0-9._-]+/', '-', pathinfo($original, PATHINFO_FILENAME));
    $base = trim(preg_replace('/\.{2,}/', '.', $base), '-._');
    if ($base === '') { $base = 'image'; }
    $base = substr($base, 0, 100);
    $name = "$base.$ext";
    for ($n = 2; $name !== $keep && file_exists(rtrim($dir, '/') . '/' . $name); $n++) { $name = "$base-$n.$ext"; }
    return $name;
}

function save_files($field, &$errors, $allowed){
    if (empty($_FILES[$field])) return [];
    $f = $_FILES[$field];
    $names = (array)$f['name']; $tmps = (array)$f['tmp_name']; $errs = (array)$f['error'];
    $dir = dirname(__DIR__) . '/uploads/products';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $out = [];
    foreach ($names as $i => $name) {
        if ($name === '') continue;
        if ($errs[$i] === UPLOAD_ERR_INI_SIZE || $errs[$i] === UPLOAD_ERR_FORM_SIZE) {
            $errors[] = "$name: file is too large. Images max 2 MB, video max 20 MB.";
            continue;
        }
        if ($errs[$i] !== UPLOAD_ERR_OK) { $errors[] = "$name: upload failed."; continue; }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) { $errors[] = "$name: file type not allowed."; continue; }

        $isImage = in_array($ext, ['jpg','jpeg','png','webp','gif']);
        $size = (int)@filesize($tmps[$i]);
        if ($isImage && $size > MAX_IMAGE_BYTES) { $errors[] = "$name: image is larger than 2 MB."; continue; }
        if (!$isImage && $size > MAX_VIDEO_BYTES) { $errors[] = "$name: video is larger than 20 MB."; continue; }

        if ($isImage && !@getimagesize($tmps[$i])) { $errors[] = "$name: not a valid image."; continue; }
        $new = upload_name($dir, $name);
        if (move_uploaded_file($tmps[$i], $dir . '/' . $new)) { $out[] = 'uploads/products/' . $new; }
        else { $errors[] = "$name: could not save file."; }
    }
    return $out;
}

function sync_cover($pdo, $pid){
    $s = $pdo->prepare('SELECT image FROM product_images WHERE product_id=? ORDER BY sort_order, id LIMIT 1');
    $s->execute([$pid]);
    $c = $s->fetchColumn();
    $u = $pdo->prepare('UPDATE products SET image=? WHERE id=?');
    $u->execute([$c ?: '', $pid]);
}

function add_photos($pdo, $pid, $paths){
    $s = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM product_images WHERE product_id=?');
    $s->execute([$pid]);
    $n = (int)$s->fetchColumn();
    $ins = $pdo->prepare('INSERT INTO product_images (product_id, image, sort_order) VALUES (?,?,?)');
    foreach ($paths as $p) { $ins->execute([$pid, $p, $n++]); }
    sync_cover($pdo, $pid);
}

function delete_product($pdo, $pid){
    $s = $pdo->prepare('SELECT image FROM product_images WHERE product_id=?');
    $s->execute([$pid]);
    foreach ($s->fetchAll(PDO::FETCH_COLUMN) as $img) remove_file($img);
    $pdo->prepare('DELETE FROM product_images WHERE product_id=?')->execute([$pid]);
    $v = $pdo->prepare('SELECT video FROM products WHERE id=?');
    $v->execute([$pid]);
    remove_file($v->fetchColumn());
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$pid]);
}

// Template categories: created on first use, so no manual DB migration is needed.
function ensure_template_schema($pdo){
    $pdo->exec("CREATE TABLE IF NOT EXISTS template_categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    if (!$pdo->query("SHOW COLUMNS FROM packaging_templates LIKE 'category_id'")->fetch()) {
        $pdo->exec('ALTER TABLE packaging_templates ADD COLUMN category_id INT UNSIGNED NULL, ADD KEY (category_id)');
    }
}
