<?php

require_once 'api/core/Response.php';

class RateLimit {

    private static $limit = 60; // requisições
    private static $window = 60; // segundos
    private static $path = __DIR__ . '/../storage/ratelimit/';

    public static function handle($key = null) {

        if (!file_exists(self::$path)) {
            mkdir(self::$path, 0777, true);
        }

        // identifica cliente
        $ip = $_SERVER['REMOTE_ADDR'];
        $identifier = $key ?? $ip;

        $file = self::$path . md5($identifier) . '.json';

        $data = [
            'count' => 0,
            'start' => time()
        ];

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
        }

        // reset janela
        if (time() - $data['start'] > self::$window) {
            $data = [
                'count' => 0,
                'start' => time()
            ];
        }

        $data['count']++;

        file_put_contents($file, json_encode($data));

        if ($data['count'] > self::$limit) {
            Response::json([
                'error' => 'Too many requests'
            ], 429);
        }
    }
}
