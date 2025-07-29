<?php
// /ec_chans/app/usuarios/main_usuarios.php

// AÑADIDO/MODIFICADO: Primero incluimos el bootstrap.php
// Esto define PROJECT_ROOT y otras configuraciones esenciales.
require_once __DIR__ . '/../../includes/bootstrap.php';

// Luego incluimos el header.php, que ahora podrá usar PROJECT_ROOT.
include_once PROJECT_ROOT . '/includes/header.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}
?>
<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Módulo de Usuarios (En Construcción)</h2>
    <p class="lead">Aquí podrás gestionar los usuarios del sistema.</p>
</div>
<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>