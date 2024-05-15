<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Avvia la sessione

require_once('database.php');
require_once('db_pdo.php');
require_once('userDTO.php');
$config = require_once('config.php');

use DB\DB_PDO as DB;

class BookManager {
    private $conn;

    public function __construct($config) {
        $PDOConn = DB::getInstance($config);
        $this->conn = $PDOConn->getConnection();
    }

    public function getAllBooks($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertBook($title, $description, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO books (title, description, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $userId]);
        return $this->conn->lastInsertId();
    }

    public function updateBook($id, $title, $description) {
        $stmt = $this->conn->prepare("UPDATE books SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);
    }

    public function deleteBook($id) {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
    }
}

$bookManager = new BookManager($config);


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}


$books = $bookManager->getAllBooks($_SESSION['user_id']);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && isset($_POST['title']) && isset($_POST['description'])) {
        $bookManager->insertBook($_POST['title'], $_POST['description'], $_SESSION['user_id']);
        exit;
    } elseif ($_POST['action'] === 'edit' && isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description'])) {
        $bookManager->updateBook($_POST['id'], $_POST['title'], $_POST['description']);
        exit;
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $bookManager->deleteBook($_POST['id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Pagina utente</a>
            <a href="logout.php" class="btn btn-outline-success">Logout</a>
        </div>
    </nav>
    <div class="container my-5">
        <h1 class="text-center">Ciao <?php echo isset($_SESSION['nomeutente']) ? $_SESSION['nomeutente'] : "Ospite"; ?></h1>
        <p class="text-center">Grazie per esserti registrato presso Palaservice srls</p>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <div class="card-body">
                    <h5 class="card-title">Aggiungi nuovo libro</h5>
                    <form id="addBookForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titolo</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrizione</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Aggiungi</button>
                    </form>
                </div>
            </a>
            <?php foreach ($books as $book) { ?>
                <div class="list-group-item list-group-item-action">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['title']; ?></h5>
                        <p class="card-text"><?php echo $book['description']; ?></p>
                        <div id="editBookForm_<?php echo $book['id']; ?>" class="d-none">
                            <div class="mb-3">
                                <label for="editTitle_<?php echo $book['id']; ?>" class="form-label">Nuovo Titolo</label>
                                <input type="text" class="form-control" id="editTitle_<?php echo $book['id']; ?>" name="title" value="<?php echo $book['title']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="editDescription_<?php echo $book['id']; ?>" class="form-label">Nuova Descrizione</label>
                                <textarea class="form-control" id="editDescription_<?php echo $book['id']; ?>" name="description"><?php echo $book['description']; ?></textarea>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="saveBookChanges(<?php echo $book['id']; ?>)">Salva Modifiche</button>
                        </div>
                        <button class="btn btn-primary" onclick="showEditForm(<?php echo $book['id']; ?>)">Modifica</button>
                        <button class="btn btn-danger" onclick="deleteBook(<?php echo $book['id']; ?>)">Elimina</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function showEditForm(bookId) {
            var editForm = document.getElementById('editBookForm_' + bookId);
            editForm.classList.remove('d-none');
        }

        function saveBookChanges(bookId) {
            var editTitle = document.getElementById('editTitle_' + bookId).value;
            var editDescription = document.getElementById('editDescription_' + bookId).value;

            var formData = new FormData();
            formData.append('action', 'edit');
            formData.append('id', bookId);
            formData.append('title', editTitle);
            formData.append('description', editDescription);

            fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                method: 'POST',
                body: formData
            }).then(function(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.text();
            }).then(function(data) {
                window.location.reload();
            }).catch(function(error) {
                console.error(error);
            });
        }

        function deleteBook(bookId) {
            var formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', bookId);

            fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                method: 'POST',
                body: formData
            }).then(function(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.text();
            }).then(function(data) {
                window.location.reload();
            }).catch(function(error) {
                console.error(error);
            });
        }

        document.getElementById('addBookForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            formData.append('action', 'add');

            fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                method: 'POST',
                body: formData
            }).then(function(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.text();
            }).then(function(data) {
                window.location.reload();
            }).catch(function(error) {
                console.error(error);
            });
        });
    </script>
</body>

</html>
