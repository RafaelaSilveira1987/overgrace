<?php

require_once 'api/config/jwt.php';

 
class AuthMiddleware
{

    public static function handle()
    {

        $headers = getallheaders();

        $authorization =
            $headers['Authorization']
            ?? $headers['authorization']
            ?? $_SERVER['HTTP_AUTHORIZATION']
            ?? null;

        if (!$authorization) {
            Response::json(['error' => 'token requerido'], 401);
        }

        $token = str_replace('Bearer ', '', $authorization);

        $data = JWT::decode($token);

        if (!$data) {
            Response::json(['error' => 'Token expirado'], 401);
        }

        return $data;
    }
}
