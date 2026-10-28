<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) { delete_product($pdo, $id); }
header('Location: products.php?msg=deleted');
exit;