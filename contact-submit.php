<?php
// Shiva Punch Art - contact form Gmail SMTP handler
// Uses Gmail SMTP directly so no PHP mail() configuration is required.

$adminEmail = 'shivapunchart@gmail.com';
$siteName = 'Shiva Punch Art';
$configFile = __DIR__ . '/smtp-config.php';
$smtp = is_file($configFile) ? require $configFile : [];

function jsonResponse($ok, $message, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: contact.html');
    exit;
}

function clean($value) {
    return trim(preg_replace('/[\r\n]+/', ' ', (string)$value));
}

function smtpRead($socket) {
    $response = '';
    while (($line = fgets($socket, 2048)) !== false) {
        $response .= $line;
        // SMTP multi-line responses have '-' after the status code; the final line has a space.
        if (preg_match('/^\d{3} /', $line)) break;
    }
    if ($response === '') throw new RuntimeException('No response received from Gmail SMTP.');
    return $response;
}

function smtpCode($response) {
    return (int)substr(trim($response), 0, 3);
}

function smtpExpect($socket, $codes, $step = '') {
    $response = smtpRead($socket);
    $code = smtpCode($response);
    if (!in_array($code, (array)$codes, true)) {
        $label = $step !== '' ? $step . ': ' : '';
        throw new RuntimeException($label . 'SMTP ' . $code . ' — ' . trim(preg_replace('/\s+/', ' ', $response)));
    }
    return $response;
}

function smtpCommand($socket, $command, $codes, $step = '') {
    if (@fwrite($socket, $command . "\r\n") === false) {
        throw new RuntimeException(($step !== '' ? $step . ': ' : '') . 'Could not write to Gmail SMTP socket.');
    }
    return smtpExpect($socket, $codes, $step);
}

function smtpHeaderEncode($value) {
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function openGmailSocket($config) {
    $host = trim((string)($config['host'] ?? 'smtp.gmail.com'));
    $preferredPort = (int)($config['port'] ?? 587);
    $preferredEncryption = strtolower((string)($config['encryption'] ?? 'tls'));

    $attempts = ($preferredPort === 465 || $preferredEncryption === 'ssl')
        ? [[465, 'ssl'], [587, 'tls']]
        : [[587, 'tls'], [465, 'ssl']];

    $lastError = '';
    foreach ($attempts as [$port, $mode]) {
        $errno = 0; $errstr = '';
        $target = ($mode === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $context = stream_context_create(['ssl' => [
            // Needed for many local XAMPP/PHP installations that have no CA bundle configured.
            // This is for localhost testing; use a proper CA bundle in production.
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
            'SNI_enabled' => true,
            'peer_name' => $host,
        ]]);
        $socket = @stream_socket_client($target, $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            $lastError = $errstr !== '' ? $errstr : ('connection error ' . $errno);
            continue;
        }
        stream_set_timeout($socket, 30);
        return [$socket, $mode, $host, $port];
    }
    throw new RuntimeException('Could not connect to Gmail SMTP on ports 587/465. ' . $lastError);
}

function smtpSendHtml($config, $to, $subject, $html, $replyTo, $siteName) {
    $username = trim((string)($config['username'] ?? ''));
    $password = preg_replace('/\s+/', '', (string)($config['password'] ?? ''));
    if ($username === '') throw new RuntimeException('SMTP username is missing in smtp-config.php.');
    if ($password === '' || $password === 'PASTE_YOUR_16_CHARACTER_APP_PASSWORD_HERE') {
        throw new RuntimeException('Gmail App Password is missing in smtp-config.php.');
    }
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('SMTP username is not a valid Gmail address.');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Recipient email is invalid.');

    [$socket, $mode, $host, $port] = openGmailSocket($config);
    try {
        smtpExpect($socket, 220, 'Gmail greeting');
        smtpCommand($socket, 'EHLO localhost', 250, 'EHLO');

        if ($mode === 'tls') {
            smtpCommand($socket, 'STARTTLS', 220, 'STARTTLS');
            $cryptoMethod = defined('STREAM_CRYPTO_METHOD_TLS_CLIENT') ? STREAM_CRYPTO_METHOD_TLS_CLIENT : STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $crypto = @stream_socket_enable_crypto($socket, true, $cryptoMethod);
            if ($crypto !== true) throw new RuntimeException('TLS negotiation failed on Gmail port 587. Check that PHP OpenSSL is enabled.');
            smtpCommand($socket, 'EHLO localhost', 250, 'EHLO after STARTTLS');
        }

        // AUTH PLAIN is supported by Gmail and avoids parsing differences in AUTH LOGIN.
        $auth = base64_encode("\0" . $username . "\0" . $password);
        smtpCommand($socket, 'AUTH PLAIN ' . $auth, 235, 'Gmail authentication');
        smtpCommand($socket, 'MAIL FROM:<' . $username . '>', 250, 'MAIL FROM');
        smtpCommand($socket, 'RCPT TO:<' . $to . '>', [250, 251], 'RCPT TO');
        smtpCommand($socket, 'DATA', 354, 'DATA');

        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . smtpHeaderEncode($siteName) . ' <' . $username . '>',
            'To: <' . $to . '>',
            'Reply-To: <' . $replyTo . '>',
            'Subject: ' . smtpHeaderEncode($subject),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
        ];
        $body = chunk_split(base64_encode($html), 76, "\r\n");
        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        $data = preg_replace('/(^|\r\n)\./', '$1..', $data);
        if (@fwrite($socket, $data . "\r\n.\r\n") === false) {
            throw new RuntimeException('Could not send email DATA to Gmail.');
        }
        smtpExpect($socket, 250, 'Gmail message acceptance');
        @fwrite($socket, "QUIT\r\n");
        @fclose($socket);
    } catch (Throwable $e) {
        @fclose($socket);
        throw $e;
    }
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$phone = preg_replace('/\D+/', '', (string)($_POST['phone'] ?? ''));
$subject = clean($_POST['subject'] ?? 'General Enquiry');
$message = trim((string)($_POST['message'] ?? ''));
$service = clean($_POST['service'] ?? '');
$source = clean($_POST['source'] ?? 'Contact-page');

$errors = [];
if ($name === '') $errors[] = 'Please enter your name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Please enter a valid 10-digit Indian mobile number.';
if ($message === '') $errors[] = 'Please enter your message.';
if (strlen($message) > 5000) $errors[] = 'Message is too long.';
if ($errors) jsonResponse(false, implode(' ', $errors), 422);

$phoneDisplay = '+91 ' . substr($phone, 0, 5) . ' ' . substr($phone, 5);
$subjectLine = '🚀 New Lead: ' . ($subject !== '' ? $subject : 'General Enquiry') . ' — ' . $name;

$adminHtml = '<!doctype html><html><body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#16213a"><div style="max-width:800px;margin:25px auto;background:#fff;border:1px solid #dbe3ef;border-radius:12px;overflow:hidden"><div style="background:#2f6095;color:#fff;padding:30px;text-align:center"><h1 style="margin:0;font-size:28px">🚀 New Lead — ' . htmlspecialchars($source, ENT_QUOTES, 'UTF-8') . '</h1></div><div style="padding:30px"><table style="width:100%;border-collapse:collapse;font-size:16px"><tr><td style="padding:12px 0;font-weight:700;width:180px">Full Name:</td><td>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td></tr><tr><td style="padding:12px 0;font-weight:700">Phone:</td><td>' . htmlspecialchars($phoneDisplay, ENT_QUOTES, 'UTF-8') . '</td></tr><tr><td style="padding:12px 0;font-weight:700">Email:</td><td>' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</td></tr><tr><td style="padding:12px 0;font-weight:700">Subject:</td><td>' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</td></tr>' . ($service !== '' ? '<tr><td style="padding:12px 0;font-weight:700">Service:</td><td>' . htmlspecialchars($service, ENT_QUOTES, 'UTF-8') . '</td></tr>' : '') . '<tr><td style="padding:12px 0;font-weight:700;vertical-align:top">Message:</td><td style="white-space:pre-wrap">' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</td></tr></table></div></div></body></html>';

$customerSubject = 'Thank You for Contacting Shiva Punch Art';
$customerHtml = '<!doctype html><html><body style="margin:0;background:#f7f9fd;font-family:Arial,sans-serif;color:#16213a"><div style="max-width:680px;margin:30px auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0"><div style="background:linear-gradient(135deg,#172b63,#2448b8);padding:28px;text-align:center"><img src="https://shivapunchart.com/assets/img/logo.png" alt="Shiva Punch Art" style="max-width:230px;height:auto;background:#fff;border-radius:8px;padding:8px"></div><div style="padding:34px"><h2 style="margin-top:0;color:#183b91">Dear ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</h2><p style="font-size:16px;line-height:1.7">Thank you for contacting Shiva Punch Art and for sharing your requirements with us. We have successfully received your enquiry, and our team will review the details and get back to you shortly, usually within one business day.</p><p style="font-size:16px;line-height:1.7">We appreciate your interest in our printing and packaging solutions. If you have any urgent requirements, please feel free to contact us directly at +91 91367 30776 or shivapunchart@gmail.com.</p><p style="font-size:16px;line-height:1.7">Warm regards,<br><strong>Team Shiva Punch Art</strong><br>Printing &amp; Packaging Industry Die Maker</p></div></div></body></html>';

try {
    smtpSendHtml($smtp, $adminEmail, $subjectLine, $adminHtml, $email, $siteName);
} catch (Throwable $e) {
    error_log('Shiva Punch Art SMTP error: ' . $e->getMessage());
    $isLocal = in_array(strtolower($_SERVER['SERVER_NAME'] ?? ''), ['localhost', '127.0.0.1', '::1'], true);
    jsonResponse(false, $isLocal ? 'SMTP ERROR: ' . $e->getMessage() : 'We could not send your enquiry right now. Please try again or contact us directly.', 500);
}

// Do not lose the lead if the acknowledgement fails; admin mail is already delivered.
try {
    smtpSendHtml($smtp, $email, $customerSubject, $customerHtml, $adminEmail, $siteName);
} catch (Throwable $e) {
    error_log('Shiva Punch Art customer acknowledgement error: ' . $e->getMessage());
}

jsonResponse(true, 'Your enquiry has been sent successfully.');
