<?php

require_once __DIR__ . '/../config/database.php';

class AdminService
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();

        $this->pdo = $db->connect();
    }

    public function getAll()
    {
        $sql = "
            SELECT
                id,
                nome,
                email,
                cargo,
                status,
                criado_em
            FROM users
            ORDER BY id DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "
            SELECT
                id,
                nome,
                email,
                cargo,
                status
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "
            INSERT INTO users
            (
                nome,
                email,
                password,
                cargo,
                status,
                criado_em
            )
            VALUES
            (
                :nome,
                :email,
                :password,
                :cargo,
                'ativo',
                NOW()
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $password = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':cargo', $data['cargo']);

        if (!$stmt->execute()) {
            die(json_encode($stmt->errorInfo()));
        }

        return true;
    }

    public function update($id, $data)
    {
        $sql = "
            UPDATE users
            SET
                nome = :nome,
                email = :email,
                cargo = :cargo
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':cargo', $data['cargo']);

        return $stmt->execute();
    }

    public function updatePassword($id, $password)
    {
        $sql = "
            UPDATE users
            SET password = :password
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $hash);

        return $stmt->execute();
    }

    public function changeStatus($id, $status)
    {
        $sql = "
            UPDATE users
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "
            DELETE FROM users
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}