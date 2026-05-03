<?php

require_once 'api/core/Response.php';
require_once 'api/services/AuthService.php';
require_once 'api/middleware/AuthMiddleware.php';

class AuthController {

    public function login() {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message'=>'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::login($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message'=>'Credenciais Inválidas'],401);
        }

        Response::json($auth);
    }

    public function loginClient() {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message'=>'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::loginClient($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message'=>'Credenciais Inválidas'],401);
        }

        Response::json($auth);
    }

    public function loginAdmin() {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message'=>'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::loginAdmin($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message'=>'Credenciais Inválidas'],401);
        }

        Response::json($auth);
    }

    public function me() {
        $payload = AuthMiddleware::handle();
        $user = AuthService::me($payload);

        if (!$user) {
            Response::json(['message'=>'Conta nao encontrada'], 404);
        }

        Response::json($user);
    }

    public function register() {
        $data = $this->getRequestData();

        if (!$data) {
            Response::json(['message'=>'Dados invalidos'], 422);
        }

        if (empty($data['password']) && !empty($data['senha'])) {
            $data['password'] = $data['senha'];
        }

        $required = ['nome', 'sobrenome', 'email', 'password', 'cpf', 'telefone'];

        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                Response::json(['message'=>'Preencha todos os campos obrigatorios'], 422);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(['message'=>'E-mail invalido'], 422);
        }

        if (strlen($data['password']) < 6) {
            Response::json(['message'=>'A senha deve ter pelo menos 6 caracteres'], 422);
        }

        $cpf = preg_replace('/\D/', '', $data['cpf']);
        $telefone = preg_replace('/\D/', '', $data['telefone']);

        if (strlen($cpf) !== 11) {
            Response::json(['message'=>'CPF invalido'], 422);
        }

        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            Response::json(['message'=>'Telefone invalido'], 422);
        }

        try {
            $id = AuthService::register($data);
        } catch (InvalidArgumentException $e) {
            Response::json(['message'=>$e->getMessage()], 422);
        }

        Response::json(['client_id'=>$id], 201);
    }

    private function getRequestData() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (is_array($data)) {
            return $data;
        }

        return $_POST ?: [];
    }
}

