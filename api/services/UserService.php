<?php

require_once 'api/config/database.php';

class UserService
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();

        $this->pdo = $db->connect();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdmins()
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "
            INSERT INTO users
            (
                nome,
                email,
                senha,
                cargo,
                status,
                criado_em
            )
            VALUES
            (
                :nome,
                :email,
                :senha,
                :cargo,
                'ativo',
                NOW()
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $senha = password_hash(
            $data['senha'],
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':cargo', $data['cargo']);

        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "
            UPDATE users
            SET
                nome = :nome,
                email = :email,
                cargo = :cargo,
                status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':cargo', $data['cargo']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function updatePerfil($id, $data)
    {
        $sql = "
            UPDATE users
            SET
                nome = :nome,
                email = :email,
                telefone = :telefone
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':telefone', $data['telefone']);

        return $stmt->execute();
    }

    public function updatePassword($id, $senha)
    {
        $sql = "
            UPDATE users
            SET senha = :senha
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $hash = password_hash(
            $senha,
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':senha', $hash);

        return $stmt->execute();
    }

    public function findById($id)
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

    public function findByEmail($email)
    {
        $sql = "
        SELECT *
        FROM users
        WHERE email = :email
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}