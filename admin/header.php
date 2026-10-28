<?php
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/helpers.php';
$active = $active ?? '';
$pageTitle = $pageTitle ?? 'Dashboard';
$bell = $bell ?? 0;
$adminEmail = $_SESSION['admin_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> | Shiva Punch Art Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
<link rel="icon" href="../assets/img/favicon%20(3).png" type="image/png">
</head>
<body>
<header class="top">
  <button class="burger" onclick="toggleMenu()"><?= icon('menu') ?></button>
  <a class="brand" href="dashboard.php"><img src="../assets/img/logo.png" alt="Shiva Punch Art"></a>
  <div class="search"><?= icon('search') ?><input type="text" placeholder="Search products, templates, etc..."></div>
  <div class="right">
    <div class="bell"><?= icon('bell') ?><?php if ($bell > 0): ?><em><?= (int)$bell ?></em><?php endif; ?></div>
    <div class="me">
      <div class="av">A</div>
      <div><b>Admin</b><small>Super Admin</small></div>
    </div>
  </div>
</header>

<aside class="side" id="side">
  <a href="dashboard.php" class="<?= $active==='dashboard'?'on':'' ?>"><?= icon('home') ?> Dashboard</a>
  <a href="products.php" class="<?= $active==='products'?'on':'' ?>"><?= icon('box') ?> Our Products</a>
  <a href="templates.php" class="<?= $active==='templates'?'on':'' ?>"><?= icon('layers') ?> Packaging Templates</a>
  <a href="settings.php" class="<?= $active==='settings'?'on':'' ?>"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg> Settings</a>
  <hr>
  <a href="logout.php"><?= icon('logout') ?> Logout</a>
  <div class="side-foot"><b>Shiva Punch Art</b>Manufacturing Excellence<br>in Every Detail</div>
</aside>
<div class="ov" id="ov" onclick="toggleMenu()"></div>

<main class="main">