<?php

class SmtpMailer
{
    public static function send(array $config, string $to, string $subject, string $html): void
    {
        $host = trim((string)($config['smtp_host'] ?? ''));
        $port = (int)($config['smtp_port'] ?? 587);
        $encryption = strtolower((string)($config['smtp_encryption'] ?? 'tls'));
        $username = (string)($config['smtp_username'] ?? '');
        $password = trim(
            (string) ($config['smtp_password'] ?? '')
        );

        if (strtolower($host) === 'smtp.gmail.com') {
            $password = preg_replace('/\s+/', '', $password);
        }
        $fromEmail = trim((string)($config['smtp_from_email'] ?? $username));
        $fromName = trim((string)($config['smtp_from_name'] ?? 'OverGrace'));

        if (!$host || !$port || !$fromEmail || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Configuração SMTP ou destinatário inválido.');
        }

        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;

        $context = stream_context_create([
            'ssl' => [
                'peer_name' => $host,
                'SNI_enabled' => true,
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $socket = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            20,
            STREAM_CLIENT_CONNECT,
            $context
        );
        if (!$socket) throw new RuntimeException("Não foi possível conectar ao SMTP: {$errstr} ({$errno})");
        stream_set_timeout($socket, 20);

        self::expect($socket, [220]);
        self::command($socket, 'EHLO overgrace.local', [250]);

        if ($encryption === 'tls') {
            self::command($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('Não foi possível iniciar TLS no SMTP.');
            }
            self::command($socket, 'EHLO overgrace.local', [250]);
        }

        if ($username !== '') {
            self::command($socket, 'AUTH LOGIN', [334]);
            self::command($socket, base64_encode($username), [334]);
            self::command($socket, base64_encode($password), [235]);
        }

        self::command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
        self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        self::command($socket, 'DATA', [354]);

        $boundary = 'b' . bin2hex(random_bytes(12));
        $headers = [
            'From: ' . self::encodeHeader($fromName) . ' <' . $fromEmail . '>',
            'To: <' . $to . '>',
            'Subject: ' . self::encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'Date: ' . date(DATE_RFC2822),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@overgrace.local>',
        ];

        $body = implode("\r\n", $headers) . "\r\n\r\n";
        $body .= '--' . $boundary . "\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode(strip_tags($html))) . "\r\n";
        $body .= '--' . $boundary . "\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($html)) . "\r\n--" . $boundary . "--\r\n";
        $body = preg_replace('/^\./m', '..', $body);
        fwrite($socket, $body . "\r\n.\r\n");
        self::expect($socket, [250]);
        self::command($socket, 'QUIT', [221]);
        fclose($socket);
    }

    private static function command($socket, string $command, array $codes): string
    {
        fwrite($socket, $command . "\r\n");
        return self::expect($socket, $codes);
    }

    private static function expect($socket, array $codes): string
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (preg_match('/^(\d{3})[ -]/', $line, $m) && substr($line, 3, 1) === ' ') {
                $code = (int)$m[1];
                if (!in_array($code, $codes, true)) {
                    throw new RuntimeException('SMTP respondeu: ' . trim($response));
                }
                return $response;
            }
        }
        throw new RuntimeException('SMTP encerrou a conexão inesperadamente.');
    }

    private static function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }
}
