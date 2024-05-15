<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('database.php');
require_once('db_pdo.php');
require_once('userDTO.php');
$config = require_once('config.php');

use DB\DB_PDO as DB;

//mi connetto al database
$PDOConn = DB::getInstance($config); 
$conn = $PDOConn->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeutente = $_POST['nomeutente'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Le password non corrispondono.";
        header("Location: register.php");
        exit();
    }

    // Hash della password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Inserisci l'utente nel database
    try {
        $stmt = $conn->prepare("INSERT INTO users (nomeutente, password) VALUES (:nomeutente, :password)");
        $stmt->bindParam(':nomeutente', $nomeutente);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        
        // Reindirizza alla pagina di successo o alla home page
        header("Location: successo.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Errore durante l'inserimento dell'utente nel database: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors" />
    <meta name="generator" content="Hugo 0.122.0" />
    <title>Registrati</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <meta name="theme-color" content="#712cf9" />

    <link rel="stylesheet" href="css/style.css" />
    <link href="https://getbootstrap.com/docs/5.3/examples/sign-in/sign-in.css" rel="stylesheet" />
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <form action="register.php" method="POST">
            <h1 class="h3 mb-3 fw-normal text-center">REGISTRAZIONE</h1>

            <div class="form-floating">
                <input type="text" class="form-control" id="floatingInput" placeholder="Nome utente" name="nomeutente"
                    value="" />
                <label for="floatingInput">Nome utente</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password"
                    value="" />
                <label for="floatingPassword">Password</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="confirmPassword" placeholder="Conferma Password" name="confirmPassword"
                    value="" />
                <label for="confirmPassword">Conferma Password</label>
            </div>

           
            <button class="btn btn-primary w-100 py-2" type="submit">
                Registrati
            </button>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger my-3" role="alert">' . $_SESSION['error'] . '</div>';
            }
            ?>
        </form>
        <div class="mt-3">Hai già un account? <a href="index.php">Accedi</a></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>