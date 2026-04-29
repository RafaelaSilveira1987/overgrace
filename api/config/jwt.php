<?php

class JWT
{

    private static function getKey()
    {
        if (empty($_ENV['JWT_SECRET'])) {
            throw new Exception('JWT_SECRET não configurado');
        }
        return $_ENV['JWT_SECRET'];
    }

    private static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function encode($payload)
    {
        $header = self::base64url_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload['exp'] = time() + 3600;

        $payload = self::base64url_encode(json_encode($payload));

        $signature = hash_hmac('sha256', "$header.$payload", self::getKey(), true);
        $signature = self::base64url_encode($signature);

        return "$header.$payload.$signature";
    }

    public static function decode($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) return false;

        [$header, $payload, $signature] = $parts;

        $validSignature = self::base64url_encode(
            hash_hmac('sha256', "$header.$payload", self::getKey(), true)
        );

        if (!hash_equals($validSignature, $signature)) {
            return false;
        }

        $payload = json_decode(self::base64url_decode($payload), true);

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
