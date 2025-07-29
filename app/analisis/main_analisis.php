<?php
// /ec_chans/app/analisis/main_analisis.php

// Incluimos el archivo de arranque (bootstrap) que define constantes y la sesión.
require_once __DIR__ . '/../../includes/bootstrap.php';

// Aunque la pantalla es 'en blanco', mantenemos la inclusión del header/footer
// para que el layout general de la aplicación se cargue correctamente.
include_once PROJECT_ROOT . '/includes/header.php';

// Verificación de seguridad: si el usuario no está logueado, se le redirige.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

// Aquí es donde irá tu contenido específico del módulo de Análisis.
// Por ahora, solo un título.
?>

<link rel="stylesheet" href="/ec_chans/css/style_analisis.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Módulo de Análisis (Pantalla en Blanco)</h2>
    <p class="lead">Aquí podrás agregar tus funciones y visualizaciones de análisis.</p>
    
    </div>

<?php
include_once PROJECT_ROOT . '/includes/footer.php';
?>