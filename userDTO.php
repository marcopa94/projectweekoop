<?php

class UserDTO
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM users';
        $res = $this->conn->query($sql, PDO::FETCH_ASSOC);

        if ($res) {
            return $res->fetchAll();
        }

        return null;
    }

    public function getUserByID(int $id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->execute(['id' => $id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByUsername(string $username)
    {
        $sql = 'SELECT * FROM users WHERE nomeutente = :nomeutente';
        $stm = $this->conn->prepare($sql);
        $stm->execute(['nomeutente' => $username]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function saveUser(array $user)
    {
        $sql = "INSERT INTO users (nomeutente, password, ruolo) VALUES (:username, :password, :ruolo)";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['username' => $user['nomeutente'], 'password' => $user['password'], 'ruolo' => $user['ruolo']]);
        return $this->conn->lastInsertId();
    }

    public function updateUser(array $user)
    {
        $sql = "UPDATE users SET nomeutente = :nomeutente,  ruolo = :ruolo WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['nomeutente' => $user['nomeutente'],  'ruolo' => $user['ruolo'], 'id' => $user['id']]);
        return $stm->rowCount();
    }

    public function deleteUser(int $id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['id' => $id]);
        return $stm->rowCount();
    }
}
