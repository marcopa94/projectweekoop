<?PHP session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Avvenuta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h1 class="card-title">Registrazione Avvenuta!</h1>
                        <p class="card-text">Grazie per esserti registrato.</p>
                        <a href="index.php" class="btn btn-primary">Accedi</a>
                        <h1> HELLO WORLD </h1>
    <p> sei loggato!!!!! </p></div>
    
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
