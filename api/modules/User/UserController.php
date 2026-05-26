<?php

require_once 'api/services/UserService.php';

class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function me($userId)
    {
        $user = $this->userService->findById($userId);

        if (!$user) {

            http_response_code(404);

            echo json_encode([
                'error' => 'Usuário não encontrado'
            ]);

            return;
        }

        echo json_encode($user);
    }

    public function changePassword($userId, $novaSenha)
    {
        $updated = $this->userService
            ->updatePassword(
                $userId,
                $novaSenha
            );

        if (!$updated) {

            http_response_code(500);

            echo json_encode([
                'error' => 'Erro ao alterar senha'
            ]);

            return;
        }

        echo json_encode([
            'success' => true
        ]);
    }
}