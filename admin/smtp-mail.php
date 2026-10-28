<?php
function smtp_send_plain($to, $subject, $body, $html = null) {
    $cfg = require __DIR__ . '/../smtp-config.php';
    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);
    $ctx = stream_context_create(['ssl' => ['verify_peer' => !$isLocal, 'verify_peer_name' => !$isLocal]]);
    $sock = @stream_socket_client('tcp://' . $cfg['host'] . ':' . (int)$cfg['port'], $en, $es, 20, STREAM_CLIENT_CONNECT, $ctx);
    if (!$sock) return false;
    stream_set_timeout($sock, 20);
    $read = function () use ($sock) {
        $r = '';
        while (($l = fgets($sock, 2048)) !== false) {
            $r .= $l;
            if (preg_match('/^\d{3} /', $l)) break;
        }
        return $r;
    };
    $cmd = function ($c, $code) use ($sock, $read) {
        fwrite($sock, $c . "\r\n");
        return strpos($read(), (string)$code) === 0;
    };
    if (strpos($read(), '220') !== 0) return false;
    if (!$cmd('EHLO localhost', 250)) return false;
    if (!$cmd('STARTTLS', 220)) return false;
    if (!@stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return false;
    if (!$cmd('EHLO localhost', 250)) return false;
    if (!$cmd('AUTH LOGIN', 334)) return false;
    if (!$cmd(base64_encode($cfg['username']), 334)) return false;
    if (!$cmd(base64_encode($cfg['password']), 235)) return false;
    if (!$cmd('MAIL FROM:<' . $cfg['username'] . '>', 250)) return false;
    if (!$cmd('RCPT TO:<' . $to . '>', 250)) return false;
    if (!$cmd('DATA', 354)) return false;
    $body = preg_replace('/\r\n|\r|\n/', "\r\n", $body);
    $head = 'From: Shiva Punch Art <' . $cfg['username'] . ">\r\n"
          . 'To: <' . $to . ">\r\n"
          . 'Subject: ' . $subject . "\r\n"
          . "MIME-Version: 1.0\r\n";
    if ($html === null) {
        $plain = preg_replace('/^\./m', '..', $body);
        $msg = $head . "Content-Type: text/plain; charset=UTF-8\r\n\r\n" . $plain . "\r\n.";
    } else {
        $b = 'sp_' . bin2hex(random_bytes(8));
        $msg = $head . "Content-Type: multipart/alternative; boundary=\"$b\"\r\n\r\n"
             . "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
             . chunk_split(base64_encode($body)) . "\r\n"
             . "--$b\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
             . chunk_split(base64_encode($html)) . "\r\n"
             . "--$b--\r\n.";
    }
    $ok = $cmd($msg, 250);
    $cmd('QUIT', 221);
    fclose($sock);
    return $ok;
}