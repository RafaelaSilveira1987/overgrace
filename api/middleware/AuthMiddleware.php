<?php

require_once 'api/config/jwt.php';
require_once 'api/core/Response.php';

class AuthMiddleware {

    public static function handle() {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            Response::json(['error'=>'token required'],401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $data = JWT::decode($token);

        if (!$data) {
            Response::json(['error'=>'invalid token'],401);
        }

        return $data;
    }
}

