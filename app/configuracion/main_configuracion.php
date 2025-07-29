<?php
// /ec_chans/app/configuracion/main_configuracion.php

// Incluimos el archivo de arranque (bootstrap) que define constantes y la sesión.
require_once __DIR__ . '/../../includes/bootstrap.php';

// Incluimos el header para la estructura de la página
include_once PROJECT_ROOT . '/includes/header.php';

// Verificación de seguridad: si el usuario no está logueado, se le redirige.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}
?>

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Módulo de Configuración (En Construcción)</h2>
    <p class="lead">Aquí podrás ajustar las configuraciones generales del sistema.</p>
    
    </div>

<?php
include_once PROJECT_ROOT . '/includes/footer.php';
?>