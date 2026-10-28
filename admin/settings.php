<?php
require 'config.php';
require_login();
require_once __DIR__ . '/helpers.php';

const MAIL_FROM = 'no-reply@yourdomain.com'; // live par apne domain ka email likhna
const OTP_TTL = 600;      // 10 minute
const OTP_MAX_TRIES = 5;  // galat code ki limit
const OTP_COOLDOWN = 60;  // resend cooldown (seconds)

$adminId = (int)$_SESSION['admin_id'];
$s = $pdo->prepare('SELECT * FROM admins WHERE id=?');
$s->execute([$adminId]);
$admin = $s->fetch(PDO::FETCH_ASSOC);
if (!$admin) { header('Location: login.php'); exit; }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

function do_logout() {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

function send_otp($to, $code) {
    require_once __DIR__ . '/smtp-mail.php';
    $msg = "Your Shiva Punch Art admin verification code is: $code\nIt is valid for 10 minutes. If you did not request this, please ignore this email.";
    $html = <<<HTML
<!doctype html>
<html><body style="margin:0;padding:0;background:#eef1f7;font-family:Arial,Helvetica,sans-serif">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f7;padding:24px 12px"><tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;overflow:hidden">
<tr><td style="background:#1e40c8;padding:26px 24px;text-align:center">
<div style="color:#ffffff;font-size:24px;font-weight:bold;letter-spacing:1px">SHIVA PUNCH ART</div>
<div style="color:#bcd0ff;font-size:12px;margin-top:6px">Printing &amp; Packaging Industry Die Maker</div>
</td></tr>
<tr><td style="height:4px;background:#ff6b81;line-height:4px;font-size:0">&nbsp;</td></tr>
<tr><td style="padding:30px 26px 10px;text-align:center">
<div style="font-size:20px;font-weight:bold;color:#0b1f6b">Admin verification code</div>
<div style="font-size:14px;color:#5b6580;margin-top:10px;line-height:1.6">Use the code below to verify your identity and continue.</div>
</td></tr>
<tr><td style="padding:12px 26px" align="center">
<div style="display:inline-block;background:#eef3ff;border:2px dashed #1e40c8;border-radius:12px;padding:16px 26px;font-size:34px;font-weight:bold;letter-spacing:8px;color:#1e40c8">$code</div>
</td></tr>
<tr><td style="padding:10px 26px 28px;text-align:center">
<div style="font-size:14px;color:#0b1f6b;font-weight:bold">This code is valid for 10 minutes.</div>
<div style="font-size:13px;color:#8a94ad;margin-top:10px;line-height:1.6">If you did not request this, please ignore this email. Never share this code with anyone.</div>
</td></tr>
<tr><td style="background:#f6f8fd;padding:14px;text-align:center;font-size:12px;color:#8a94ad">&copy; Shiva Punch Art</td></tr>
</table>
</td></tr></table>
</body></html>
HTML;
    $sent = smtp_send_plain($to, 'Admin verification code', $msg, $html);
    if (!$sent && in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])) {
        file_put_contents(__DIR__ . '/otp-log.txt', date('Y-m-d H:i:s') . " OTP for $to: $code\n", FILE_APPEND);
        return true;
    }
    return $sent;
}

$err = '';
$ok = '';
$tab = (($_GET['tab'] ?? 'password') === 'id') ? 'id' : 'password';
$verified = !empty($_SESSION['otp_ok']) && $_SESSION['otp_ok'] > time();
$otp = $_SESSION['otp'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $err = 'Session expired. Please refresh the page and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'send') {
            $wait = OTP_COOLDOWN - (time() - (int)($_SESSION['otp_sent'] ?? 0));
            if ($wait > 0) {
                $err = "Please wait $wait seconds before requesting a new code.";
            } else {
                $code = (string)random_int(100000, 999999);
                $_SESSION['otp'] = [
                    'hash'  => password_hash($code, PASSWORD_DEFAULT),
                    'exp'   => time() + OTP_TTL,
                    'tries' => 0,
                ];
                $_SESSION['otp_sent'] = time();
                $otp = $_SESSION['otp'];
                if (send_otp($admin['email'], $code)) {
                    $ok = 'Verification code sent to your registered email.';
                } else {
                    $err = 'Could not send the email. Please try again.';
                }
            }

        } elseif ($action === 'verify') {
            if (!$otp || $otp['exp'] < time()) {
                $err = 'Code expired. Please request a new one.';
                unset($_SESSION['otp']); $otp = null;
            } elseif ($otp['tries'] >= OTP_MAX_TRIES) {
                $err = 'Too many wrong attempts. Please request a new code.';
                unset($_SESSION['otp']); $otp = null;
            } else {
                $_SESSION['otp']['tries']++;
                if (password_verify(trim($_POST['code'] ?? ''), $otp['hash'])) {
                    $_SESSION['otp_ok'] = time() + OTP_TTL;
                    unset($_SESSION['otp']); $otp = null;
                    $verified = true;
                    $ok = 'Email verified.';
                } else {
                    $err = 'Invalid code.';
                    $otp = $_SESSION['otp'];
                }
            }

        } elseif ($action === 'password' && $verified) {
            $p1 = $_POST['new'] ?? '';
            $p2 = $_POST['confirm'] ?? '';
            if (strlen($p1) < 8) {
                $err = 'Password must be at least 8 characters.';
            } elseif ($p1 !== $p2) {
                $err = 'Passwords do not match.';
            } else {
                $pdo->prepare('UPDATE admins SET password=? WHERE id=?')
                    ->execute([password_hash($p1, PASSWORD_DEFAULT), $adminId]);
                do_logout();
            }

        } elseif ($action === 'id' && $verified) {
            $cur = trim($_POST['current'] ?? '');
            $n1  = trim($_POST['new'] ?? '');
            $n2  = trim($_POST['confirm'] ?? '');
            $tab = 'id';
            if (strcasecmp($cur, $admin['email']) !== 0) {
                $err = 'Current Admin ID is incorrect.';
            } elseif (!filter_var($n1, FILTER_VALIDATE_EMAIL)) {
                $err = 'Please enter a valid email as the new Admin ID.';
            } elseif ($n1 !== $n2) {
                $err = 'New Admin ID and confirmation do not match.';
            } else {
                $c = $pdo->prepare('SELECT id FROM admins WHERE email=? AND id<>?');
                $c->execute([$n1, $adminId]);
                if ($c->fetch()) {
                    $err = 'This Admin ID cannot be used.';
                } else {
                    $pdo->prepare('UPDATE admins SET email=? WHERE id=?')->execute([$n1, $adminId]);
                    do_logout();
                }
            }
        }
    }
}

$pageTitle = 'Settings';
$active = 'settings';
require 'header.php';
?>
<style>
.tabs{display:flex;gap:10px;margin:0 0 16px;flex-wrap:wrap}
.tabs a{padding:9px 18px;border-radius:999px;background:#eef1f7;color:#0a1f8f;text-decoration:none;font-size:14px;font-weight:600}
.tabs a.on{background:#0a1f8f;color:#fff}
.tabs a.out{background:#ffe9e9;color:#c62828;margin-left:auto}
.msg{padding:12px 16px;border-radius:10px;margin-bottom:14px;font-size:14px}
.msg.e{background:#ffe9e9;color:#c62828}
.msg.s{background:#dcfce7;color:#15803d}
.set-row{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:10px}
.card .form input[type=text],.card .form input[type=email],.card .form input[type=password]{display:block;width:100%;max-width:420px;padding:12px 14px;border:1px solid #c9d3ea;border-radius:10px;background:#fff;font-size:15px;margin:6px 0 14px;outline:none}
.card .form input:focus{border-color:#1d3bd1}
</style>

<div class="head">
  <div>
    <h2>Settings</h2>
    <p>Change your login details securely.</p>
  </div>
</div>

<div class="tabs">
  <a href="settings.php?tab=password" class="<?= $tab === 'password' ? 'on' : '' ?>">Change Password</a>
  <a href="settings.php?tab=id" class="<?= $tab === 'id' ? 'on' : '' ?>">Change Admin ID</a>
  <a href="logout.php" class="out">Logout</a>
</div>

<?php if ($err): ?><div class="msg e"><?= h($err) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="msg s"><?= h($ok) ?></div><?php endif; ?>

<div class="card">
<?php if (!$verified): ?>

  <?php if ($otp && $otp['exp'] > time()): ?>
    <p style="margin-bottom:12px">Enter the 6-digit code sent to your registered email. It is valid for 10 minutes.</p>
    <form method="post" class="form" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
      <input type="hidden" name="action" value="verify">
      <label>Verification Code</label>
      <input type="text" name="code" inputmode="numeric" maxlength="6" required>
      <div class="set-row">
        <button class="pub" type="submit">Verify</button>
      </div>
    </form>
    <form method="post" style="margin-top:12px">
      <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
      <input type="hidden" name="action" value="send">
      <button type="submit" style="background:none;border:0;color:#1d3bd1;cursor:pointer;font-size:13px;padding:0">Resend code</button>
    </form>
  <?php else: ?>
    <p style="margin-bottom:12px">To change your login details, first verify your identity. We will send a code to your registered email.</p>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
      <input type="hidden" name="action" value="send">
      <button class="pub" type="submit">Send Verification Code</button>
    </form>
  <?php endif; ?>

<?php elseif ($tab === 'id'): ?>

  <form method="post" class="form" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
    <input type="hidden" name="action" value="id">
    <label>Current Admin ID</label>
    <input type="email" name="current" required>
    <label>New Admin ID (email)</label>
    <input type="email" name="new" required>
    <label>Confirm New Admin ID</label>
    <input type="email" name="confirm" required>
    <button class="pub" type="submit">Save Changes</button>
  </form>

<?php else: ?>

  <form method="post" class="form" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
    <input type="hidden" name="action" value="password">
    <label>New Password (minimum 8 characters)</label>
    <input type="password" name="new" minlength="8" required>
    <label>Confirm New Password</label>
    <input type="password" name="confirm" minlength="8" required>
    <button class="pub" type="submit">Save Password</button>
  </form>

<?php endif; ?>
</div>
<style>
.pw-wrap{position:relative;display:block}
.pw-wrap input{padding-right:46px!important;width:100%;box-sizing:border-box}
.pw-eye{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;padding:0;color:#64748b;display:flex}
.pw-eye svg{width:20px;height:20px}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var eye='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>';
  var eyeOff='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19.8 19.8 0 0 1 5.1-5.9M9.9 4.2A10.9 10.9 0 0 1 12 4c7 0 11 8 11 8a19.7 19.7 0 0 1-3.2 4.2M1 1l22 22"/></svg>';
  document.querySelectorAll('input[type=password]').forEach(function(inp){
  var w=document.createElement('span'); w.className='pw-wrap'; w.style.width=inp.offsetWidth+'px'; w.style.maxWidth='100%';
    inp.parentNode.insertBefore(w,inp); w.appendChild(inp);
    var b=document.createElement('button'); b.type='button'; b.className='pw-eye'; b.innerHTML=eye;
    b.onclick=function(){var s=inp.type==='password'; inp.type=s?'text':'password'; b.innerHTML=s?eyeOff:eye;};
    w.appendChild(b);
  });
});
</script>
<?php require 'footer.php'; ?>