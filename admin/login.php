<?php
require 'config.php';

if (!empty($_SESSION['admin_id'])) {
  header('Location: dashboard.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
  $stmt->execute([$email]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin && password_verify($pass, $admin['password'])) {
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    header('Location: dashboard.php'); exit;
  }
  $error = 'Invalid email or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | Shiva Punch Art</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;font-family:Poppins,Arial,sans-serif}
  body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#e9eef7;padding:20px}
  .panel{width:100%;max-width:440px;min-height:660px;border-radius:24px;overflow:hidden;position:relative;
    background:#dfe6f2 url('login-bg.jpg') center/cover no-repeat;box-shadow:0 20px 60px rgba(10,30,120,.15)}
  .overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.95) 0%,rgba(255,255,255,.88) 55%,rgba(255,255,255,.4) 100%)}
  .content{position:relative;padding:36px 32px;display:flex;flex-direction:column;min-height:660px}
  .brand{font-size:26px;font-weight:800;color:#0a1f8f;line-height:1}
  .brand span{color:#e63232}
  .brand small{display:block;font-size:9px;font-weight:600;color:#0a1f8f;letter-spacing:.5px;margin-top:6px}
  h1{margin-top:34px;font-size:30px;font-weight:700;color:#0a1f8f}
  h1 span{color:#e63232}
  p.sub{color:#556;font-size:13px;margin:8px 0 22px;line-height:1.5}
  .box{background:#fff;border-radius:16px;padding:20px;box-shadow:0 8px 30px rgba(10,30,120,.10)}
  .field{position:relative;margin-bottom:14px}
  .field svg.i{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;stroke:#0a1f8f;fill:none;stroke-width:2}
  .field input{width:100%;padding:14px 44px 14px 44px;border:1px solid #dbe2ef;border-radius:10px;font-size:14px;background:#f5f8fd;outline:none}
  .field input:focus{border-color:#1d3bd1;background:#fff}
  .eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;padding:4px;display:flex}
  .eye svg{width:18px;height:18px;stroke:#667;fill:none;stroke-width:2}
  .row{display:flex;justify-content:space-between;align-items:center;font-size:12px;margin:4px 0 16px}
  .row label{color:#334;display:flex;align-items:center;gap:6px}
  .row a{color:#1d3bd1;text-decoration:none}
  .btn{display:flex;width:100%;border:0;border-radius:10px;overflow:hidden;cursor:pointer;color:#fff;font-size:16px;font-weight:600}
  .btn .t{flex:1;padding:14px;background:linear-gradient(90deg,#0a1f8f,#1d3bd1);display:flex;align-items:center;justify-content:center;gap:8px}
  .btn .a{width:54px;background:#ff5a1f;display:flex;align-items:center;justify-content:center}
  .btn svg{width:18px;height:18px;stroke:#fff;fill:none;stroke-width:2.5}
  .secure{display:flex;align-items:center;justify-content:center;gap:6px;color:#667;font-size:11px;margin-top:16px}
  .secure svg{width:12px;height:12px;stroke:#667;fill:none;stroke-width:2}
  .err{background:#ffe9e9;color:#c62828;padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:13px}
  .tag{margin-top:auto;padding-top:30px;font-size:22px;font-weight:600;color:#0a1f8f;line-height:1.25}
  .tag:before{content:"";display:block;width:36px;height:3px;background:#ff5a1f;margin-bottom:12px}
  .tag span{color:#e63232;display:block}
</style>
</head>
<body>
  <div class="panel">
    <div class="overlay"></div>
    <div class="content">
<img src="../assets/img/logo.png" alt="Shiva Punch Art" style="width:230px;max-width:100%;height:auto;display:block">

      <h1>Admin <span>Login</span></h1>
      <p class="sub">Welcome back! Please login to your admin panel to manage products, packaging templates and website content.</p>

      <div class="box">
        <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
          <div class="field">
            <svg class="i" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            <input type="email" name="email" placeholder="Email address" required>
          </div>
          <div class="field">
            <svg class="i" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
            <input type="password" name="password" id="pw" placeholder="Password" required>
            <button type="button" class="eye" onclick="var p=document.getElementById('pw');p.type=p.type==='password'?'text':'password'">
              <svg viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="row">
            <label><input type="checkbox" checked> Remember Me</label>
            <a href="#">Forgot Password?</a>
          </div>
          <button type="submit" class="btn">
            <span class="t">Login</span>
            <span class="a"><svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
          </button>
          <div class="secure">
            <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
            Secure Access
          </div>
        </form>
      </div>

      <div class="tag">Precision Crafted for <span>Better Tomorrow</span></div>
    </div>
  </div>
</body>
</html>