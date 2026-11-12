<?php

function ni_mail_env(string $key, string $default = ''): string
{
    static $envFileValues = null;
    if ($envFileValues === null) {
        $envFileValues = [];
        $envPath = dirname(__DIR__) . '/.env';
        if (is_readable($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines ?: [] as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                    continue;
                }
                [$envKey, $envValue] = explode('=', $line, 2);
                $envFileValues[trim($envKey)] = ni_mail_unquote(trim($envValue));
            }
        }
    }

    if (array_key_exists($key, $envFileValues)) {
        return $envFileValues[$key];
    }

    $value = getenv($key);
    return $value === false ? $default : ni_mail_unquote((string) $value);
}

function ni_mail_unquote(string $value): string
{
    if (strlen($value) >= 2) {
        $first = $value[0];
        $last = $value[strlen($value) - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }
    }

    return $value;
}

function ni_mail_config(): array
{
    $appName = ni_mail_env('APP_NAME', 'Neural Invoice');
    $fromName = ni_mail_env('MAIL_FROM_NAME', $appName);
    if ($fromName === '${APP_NAME}') {
        $fromName = $appName;
    }

    return [
        'mailer' => ni_mail_env('MAIL_MAILER', 'smtp'),
        'host' => ni_mail_env('MAIL_HOST', 'smtp.gmail.com'),
        'port' => (int) ni_mail_env('MAIL_PORT', '587'),
        'username' => ni_mail_env('MAIL_USERNAME', 'pragya.neural@gmail.com'),
        'password' => preg_replace('/\s+/', '', ni_mail_env('MAIL_PASSWORD', '')),
        'encryption' => strtolower(ni_mail_env('MAIL_ENCRYPTION', 'tls')),
        'from_address' => ni_mail_env('MAIL_FROM_ADDRESS', 'pragya.neural@gmail.com'),
        'from_name' => $fromName,
        'debug' => in_array(strtolower(ni_mail_env('MAIL_DEBUG', 'false')), ['1', 'true', 'yes'], true),
    ];
}

function ni_smtp_read($socket): array
{
    $data = '';
    while (($line = fgets($socket, 515)) !== false) {
        $data .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    $code = (int) substr($data, 0, 3);
    return [$code, $data];
}

function ni_smtp_command($socket, string $command, array $expectedCodes): array
{
    fwrite($socket, $command . "\r\n");
    [$code, $response] = ni_smtp_read($socket);
    if (!in_array($code, $expectedCodes, true)) {
        return [false, trim($response)];
    }

    return [true, trim($response)];
}

function ni_smtp_address(string $email): string
{
    return '<' . str_replace(["\r", "\n", '<', '>'], '', $email) . '>';
}

function ni_smtp_header_value(string $value): string
{
    return str_replace(["\r", "\n"], ' ', $value);
}

function ni_smtp_dot_stuff(string $body): string
{
    $body = str_replace(["\r\n", "\r"], "\n", $body);
    $body = preg_replace('/^\./m', '..', $body);
    return str_replace("\n", "\r\n", $body);
}

function ni_send_smtp_mail(string $to, string $subject, string $body, array $config): array
{
    if (($config['mailer'] ?? 'smtp') !== 'smtp') {
        return ['ok' => false, 'error' => 'Only SMTP mailer is configured for this project.'];
    }
    if (empty($config['password'])) {
        return ['ok' => false, 'error' => 'Gmail SMTP password/app password missing. Set MAIL_PASSWORD first.'];
    }

    $host = (string) ($config['host'] ?? 'smtp.gmail.com');
    $port = (int) ($config['port'] ?? 587);
    $timeout = 20;
    $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        return ['ok' => false, 'error' => "SMTP connection failed: {$errstr}"];
    }
    stream_set_timeout($socket, $timeout);

    [$code, $response] = ni_smtp_read($socket);
    if ($code !== 220) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP greeting failed: ' . trim($response)];
    }

    [$ok, $error] = ni_smtp_command($socket, 'EHLO localhost', [250]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP EHLO failed: ' . $error];
    }

    if (($config['encryption'] ?? 'tls') === 'tls') {
        [$ok, $error] = ni_smtp_command($socket, 'STARTTLS', [220]);
        if (!$ok) {
            fclose($socket);
            return ['ok' => false, 'error' => 'SMTP STARTTLS failed: ' . $error];
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return ['ok' => false, 'error' => 'SMTP TLS negotiation failed.'];
        }
        [$ok, $error] = ni_smtp_command($socket, 'EHLO localhost', [250]);
        if (!$ok) {
            fclose($socket);
            return ['ok' => false, 'error' => 'SMTP EHLO after TLS failed: ' . $error];
        }
    }

    [$ok, $error] = ni_smtp_command($socket, 'AUTH LOGIN', [334]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP auth start failed: ' . $error];
    }
    [$ok, $error] = ni_smtp_command($socket, base64_encode((string) $config['username']), [334]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP username rejected: ' . $error];
    }
    [$ok, $error] = ni_smtp_command($socket, base64_encode((string) $config['password']), [235]);
    if (!$ok) {
        fclose($socket);
        error_log('Gmail SMTP password rejected for ' . (string) $config['username'] . ': ' . $error);
        return ['ok' => false, 'error' => 'SMTP password rejected. Use a Gmail App Password.'];
    }

    $fromAddress = (string) ($config['from_address'] ?? $config['username']);
    $fromName = (string) ($config['from_name'] ?? 'Neural Invoice');

    [$ok, $error] = ni_smtp_command($socket, 'MAIL FROM:' . ni_smtp_address($fromAddress), [250]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP sender rejected: ' . $error];
    }
    [$ok, $error] = ni_smtp_command($socket, 'RCPT TO:' . ni_smtp_address($to), [250, 251]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP recipient rejected: ' . $error];
    }
    [$ok, $error] = ni_smtp_command($socket, 'DATA', [354]);
    if (!$ok) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP DATA failed: ' . $error];
    }

    $headers = [
        'From: ' . ni_smtp_header_value($fromName) . ' <' . ni_smtp_header_value($fromAddress) . '>',
        'To: ' . ni_smtp_header_value($to),
        'Subject: ' . ni_smtp_header_value($subject),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    fwrite($socket, implode("\r\n", $headers) . "\r\n\r\n" . ni_smtp_dot_stuff($body) . "\r\n.\r\n");
    [$code, $response] = ni_smtp_read($socket);
    ni_smtp_command($socket, 'QUIT', [221]);
    fclose($socket);

    if ($code !== 250) {
        return ['ok' => false, 'error' => 'SMTP send failed: ' . trim($response)];
    }

    return ['ok' => true, 'error' => ''];
}
