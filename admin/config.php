<?php
session_start();
$pdo = new PDO(
  'mysql:host=localhost;dbname=u606581843_shivapunch;charset=utf8mb4',
  'u606581843_shivapunch', 'TWsTs6aJ:FE#$%$^^',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

function require_login() {
  if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
  }
}