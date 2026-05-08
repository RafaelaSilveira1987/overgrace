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

    public static function encode(array $payload, int $expiresIn = 900, string $type = 'access')
    {
        $header = self::base64url_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload['type'] = $type;
        $payload['iat']  = time();
        $payload['exp']  = time() + $expiresIn;

        $payloadEncoded = self::base64url_encode(
            json_encode($payload)
        );

        $signature = hash_hmac(
            'sha256',
            "$header.$payloadEncoded",
            self::getKey(),
            true
        );

        $signatureEncoded = self::base64url_encode($signature);

        return "$header.$payloadEncoded.$signatureEncoded";
    }

    public static function decode(string $token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = self::base64url_encode(
            hash_hmac(
                'sha256',
                "$header.$payload",
                self::getKey(),
                true
            )
        );

        if (!hash_equals($validSignature, $signature)) {
            return false;
        }

        $payloadDecoded = json_decode(
            self::base64url_decode($payload),
            true
        );

        if (!$payloadDecoded) {
            return false;
        }

        if (
            isset($payloadDecoded['exp']) &&
            $payloadDecoded['exp'] < time()
        ) {
            return false;
        }

        return $payloadDecoded;
    }

    public static function generateAccessToken(array $data)
    {
        return self::encode(
            $data,
            15,
            'access'
        );
    }

    public static function generateRefreshToken(array $data)
    {
        return self::encode(
            $data,
            60 * 60 * 24 * 30,
            'refresh'
        );
    }

}
