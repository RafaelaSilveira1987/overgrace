<?php

require_once 'api/core/Response.php';
require_once 'api/services/AuthService.php';

class AuthController {

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        $token = AuthService::login($data['email'], $data['password']);

        if (!$token) {
            Response::json(['message'=>'Credenciais Inválidas'],401);
        }

        Response::json(['token'=>$token]);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = AuthService::register($data['email'], $data['password']);

        Response::json(['user_id'=>$id]);
    }
}

