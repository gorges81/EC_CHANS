<?php
// index.php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'db/config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EC_CHANS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ec_chans/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5 text-center">
                        <img src="/ec_chans/img/LOGO-BLK-1207x377.png" alt="EC_CHANS Logo" class="img-fluid mb-4" style="max-width: 200px;">
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                        <?php endif; ?>
                        
                        <form action="procesar_login.php" method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="username" placeholder="Usuario" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                            </div>
                            <button type="submit" class="btn btn-dark-blue w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>