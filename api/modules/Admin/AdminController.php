<?php

require_once __DIR__ . '/../../services/AdminService.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../core/Response.php';

class AdminController
{
    /*
    |--------------------------------------------------------------------------
    | LISTAR ADMINISTRADORES
    |--------------------------------------------------------------------------
    */
    public function users()
    {
        try {
            AuthMiddleware::handle();
            $adminService = new AdminService();
            $users = $adminService->getAll();
            Response::json($users);
        } catch (\Throwable $e) {
            Response::json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DADOS DO ADMIN LOGADO
    |--------------------------------------------------------------------------
    */
    public function me()
    {
        $auth = AuthMiddleware::handle();

        $adminService = new AdminService();

        $user = $adminService->getById($auth->id);

        if (!$user) {

            Response::json([
                'error' => 'Usuário não encontrado'
            ], 404);
        }

        Response::json($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRIAR ADMIN
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        AuthMiddleware::handle();

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (
            empty($data['nome']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['cargo'])
        ) {

            Response::json([
                'error' => 'Preencha todos os campos'
            ], 400);
        }

        $adminService = new AdminService();

        $adminService->create([
            'nome'  => $data['nome'],
            'email' => $data['email'],
            'senha' => $data['password'],
            'cargo' => $data['cargo']
        ]);

        Response::json([
            'success' => true,
            'message' => 'Administrador criado'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR ADMIN
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        AuthMiddleware::handle();

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (
            empty($data['nome']) ||
            empty($data['email']) ||
            empty($data['cargo'])
        ) {

            Response::json([
                'error' => 'Dados inválidos'
            ], 400);
        }

        $adminService = new AdminService();

        $adminService->update($id, [
            'nome'  => $data['nome'],
            'email' => $data['email'],
            'cargo' => $data['cargo']
        ]);

        /*
        |--------------------------------------------------------------------------
        | ALTERAR SENHA (SE INFORMADA)
        |--------------------------------------------------------------------------
        */
        if (!empty($data['password'])) {

            $adminService->updatePassword(
                $id,
                $data['password']
            );
        }

        Response::json([
            'success' => true,
            'message' => 'Administrador atualizado'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | INATIVAR ADMIN
    |--------------------------------------------------------------------------
    */
    public function inactive($id)
    {
        AuthMiddleware::handle();

        $adminService = new AdminService();

        $adminService->changeStatus(
            $id,
            'inativo'
        );

        Response::json([
            'success' => true,
            'message' => 'Administrador inativado'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ATIVAR ADMIN
    |--------------------------------------------------------------------------
    */
    public function active($id)
    {
        AuthMiddleware::handle();

        $adminService = new AdminService();

        $adminService->changeStatus(
            $id,
            'ativo'
        );

        Response::json([
            'success' => true,
            'message' => 'Administrador ativado'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVER ADMIN
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        AuthMiddleware::handle();

        $adminService = new AdminService();

        $adminService->delete($id);

        Response::json([
            'success' => true,
            'message' => 'Administrador removido'
        ]);
    }
}