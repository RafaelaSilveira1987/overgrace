<?php

require_once 'api/core/Response.php';
require_once 'api/services/AuthService.php';
require_once 'api/middleware/AuthMiddleware.php';

class AuthController
{

    public function login()
    {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message' => 'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::login($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message' => 'Credenciais Inválidas'], 401);
        }

        Response::json($auth);
    }

    public function loginClient()
    {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message' => 'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::loginClient($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message' => 'Credenciais Inválidas'], 401);
        }

        Response::json($auth);
    }

    public function loginAdmin()
    {
        $data = $this->getRequestData();

        if (empty($data['email']) || empty($data['password'])) {
            Response::json(['message' => 'Informe e-mail e senha'], 422);
        }

        $auth = AuthService::loginAdmin($data['email'], $data['password']);

        if (!$auth) {
            Response::json(['message' => 'Credenciais Inválidas'], 401);
        }

        Response::json($auth);
    }

    public function me()
    {
        $payload = AuthMiddleware::handle();
        $userService = AuthService::me($payload);

        if (!$userService) {
            Response::json(['message' => 'Conta nao encontrada'], 404);
        }

        Response::json($userService);
    }

    public function register()
    {
        $data = $this->getRequestData();

        if (!$data) {
            Response::json(['message' => 'Dados invalidos'], 422);
        }

        if (empty($data['password']) && !empty($data['senha'])) {
            $data['password'] = $data['senha'];
        }

        $required = ['nome', 'sobrenome', 'email', 'password', 'cpf', 'telefone'];

        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                Response::json(['message' => 'Preencha todos os campos obrigatorios'], 422);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(['message' => 'E-mail invalido'], 422);
        }

        if (strlen($data['password']) < 6) {
            Response::json(['message' => 'A senha deve ter pelo menos 6 caracteres'], 422);
        }

        $cpf = preg_replace('/\D/', '', $data['cpf']);
        $telefone = preg_replace('/\D/', '', $data['telefone']);

        if (strlen($cpf) !== 11) {
            Response::json(['message' => 'CPF invalido'], 422);
        }

        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            Response::json(['message' => 'Telefone invalido'], 422);
        }

        try {
            $id = AuthService::register($data);
        } catch (InvalidArgumentException $e) {
            Response::json(['message' => $e->getMessage()], 422);
        }

        Response::json(['client_id' => $id], 201);
    }

    private function getRequestData()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (is_array($data)) {
            return $data;
        }

        return $_POST ?: [];
    }

    public function refresh()
    {

        $input = json_decode(file_get_contents('php://input'), true);

        $refreshToken = $input['refresh_token'] ?? null;

        if (!$refreshToken) {
            Response::json(['error' => 'refresh token required'], 401);
        }

        $payload = JWT::decode($refreshToken);

        if (!$payload || ($payload['type'] ?? null) !== 'refresh') {
            Response::json(['error' => 'invalid refresh token'], 401);
        }

        $newAccessToken = JWT::encode([
            'id' => $payload['id'],
            'role' => $payload['role'],
            'exp' => time() + (60 * 15) // 15 min
        ]);

        Response::json([
            'token' => $newAccessToken
        ]);
    }
}
