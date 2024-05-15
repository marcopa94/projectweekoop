<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once('database.php');
require_once('db_pdo.php');
require_once('userDTO.php');
$config = require_once('config.php');

use DB\DB_PDO as DB;


$PDOConn = DB::getInstance($config);
$conn = $PDOConn->getConnection();
$userDTO = new UserDTO($conn);

if (!isset($_SESSION['user_id'])) {

  header('Location: index.php');
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
  if (isset($_POST['nomeutente'], $_POST['password'], $_POST['ruolo'])) {
    $nomeutente = $_POST['nomeutente'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ruolo = $_POST['ruolo'];

    $userDTO->saveUser([
      'nomeutente' => $nomeutente,
      'password' => $password,
      'ruolo' => $ruolo
    ]);


    header('Location: pannello.php');
    exit;
  } elseif (isset($_POST['action']) && $_POST['action'] == 'edit' && isset($_POST['id'])) {

    $id = $_POST['id'];
    $nomeutente = $_POST['nomeutente'];
    $ruolo = $_POST['ruolo'];


    $res = $userDTO->updateUser([
      'id' => $id,
      'nomeutente' => $nomeutente,
      'ruolo' => $ruolo
    ]);

    header('Location: pannello.php');
    exit;
  }
}

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
 
  $id = $_GET['id'];
  $userDTO->deleteUser($id);
  header('Location: pannello.php');
  exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {

  $id = $_GET['id'];
  $user = $userDTO->getUserById($id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Pannello</title>
</head>

<body>
  <nav class="navbar bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Pannello admin</a>
      <a href="logout.php" class="btn btn-outline-success">Logout</a>
    </div>
  </nav>
  <div class="container">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h1 class="my-5">Lista utenti</h1>
      </div>
      <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">Aggiungi utente</button>
      </div>
    </div>
    <table class="table table-hover  my-5">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Nome utente</th>
          <th scope="col">Ruolo</th>
          <th scope="col">Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($res as $user) : ?>
          <tr>
            <th scope="row"><?= $user['id'] ?></th>
            <td><?= $user['nomeutente'] ?></td>
            <td><?= $user['ruolo'] ?></td>
            <td>
              <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editUser<?= $user['id'] ?>">Modifica</button>
              <a href="pannello.php?action=delete&id=<?= $user['id'] ?>" class="btn btn-danger">Elimina</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modale di aggiunta utenti -->
  <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi utente</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="pannello.php" method="POST">
            <div class="form-floating my-3">
              <input type="text" class="form-control" id="floatingInput" placeholder="Nome utente" name="nomeutente" value="" />
              <label for="floatingInput">Nome utente</label>
            </div>
            <div class="form-floating my-3">
              <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" value="" />
              <label for="floatingPassword">Password</label>
            </div>
            <div class="form-floating my-3">
              <select class="form-select" aria-label="Default select example" name="ruolo">
                <option selected value="admin">Admin</option>
                <option value="utente">Utente</option>
              </select>
              <label for="ruolo">Ruolo</label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Aggiungi</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modali di modifica utenti -->
  <?php foreach ($res as $user) : ?>
    <div class="modal fade" id="editUser<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUser<?= $user['id'] ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Modifica utente</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="pannello.php" method="POST">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" value="<?= $user['id'] ?>">
              <div class="form-floating my-3">
                <input type="text" class="form-control" id="floatingInput" placeholder="Nome utente" name="nomeutente" value="<?= $user['nomeutente'] ?>" />
                <label for="floatingInput">Nome utente</label>
              </div>
              <div class="form-floating my-3">
                <select class="form-select" aria-label="Default select example" name="ruolo">
                  <option value="admin" <?= ($user['ruolo'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                  <option value="utente" <?= ($user['ruolo'] == 'utente') ? 'selected' : '' ?>>Utente</option>
                </select>
                <label for="ruolo">Ruolo</label>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-warning">Modifica</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
