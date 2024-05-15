<?php
class Authenticator {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($username, $password) {
        $userDTO = new UserDTO($this->conn);
        $user = $userDTO->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nomeutente'] = $user['nomeutente'];
            $_SESSION['ruolo'] = $user['ruolo']; // Memorizza il ruolo dell'utente in sessione
            return true;
        } else {
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_unset();
        session_destroy();
    }
}
?>
