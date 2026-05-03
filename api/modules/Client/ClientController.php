<?php

require_once 'api/core/Response.php';
require_once 'api/services/ClientService.php';


class ClientController
{
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        try {

            $token = ClientService::login(
                $data['email'], 
                $data['senha']
            );

            if (!$token) {
                Response::json(['message' => 'Credenciais inválidas'], 401);
            }

            Response::json(['token' => $token]);

        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        try {

            $token = ClientService::register($data);

            Response::json([
                'success' => true,
                'token' => $token
            ]);

        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }
}
