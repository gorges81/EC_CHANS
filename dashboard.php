<?php
// /ec_chans/dashboard.php

// Incluimos el nuevo archivo de arranque. ¡Esto es todo lo que necesitamos!
require_once __DIR__ . '/includes/bootstrap.php';

// Verificación de seguridad: si el usuario no está logueado, se le redirige.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

// Ahora que todo está cargado, incluimos el header.
include_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Bienvenido a tu Dashboard, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
    <p>Selecciona un módulo para comenzar.</p>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Importaciones
                </div>
                <div class="card-body">
                    <p class="card-text">Carga y gestiona nuevos archivos de datos de importación.</p>
                    <a href="/ec_chans/app/importaciones/main_importaciones.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="bi bi-share-fill me-2"></i>Integrador
                </div>
                <div class="card-body">
                    <p class="card-text">Conecta y sincroniza datos entre diferentes plataformas.</p>
                    <a href="/ec_chans/app/integrador/main_integrador.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-chart-pie me-2"></i>Análisis
                </div>
                <div class="card-body">
                    <p class="card-text">Visualiza y analiza los datos importados para obtener insights.</p>
                    <a href="/ec_chans/app/analisis/main_analisis.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-person-check me-2"></i>Auditorías
                </div>
                <div class="card-body">
                    <p class="card-text">Revisa los registros detallados de actividad del sistema.</p>
                    <a href="/ec_chans/app/auditorias/main_auditorias.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-file-invoice me-2"></i>Reportes
                </div>
                <div class="card-body">
                    <p class="card-text">Genera informes y documentos basados en tus datos.</p>
                    <a href="/ec_chans/app/reportes/main_reportes.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-history me-2"></i>Eventos
                </div>
                <div class="card-body">
                    <p class="card-text">Revisa el log cronológico de toda la actividad del sistema.</p>
                    <a href="/ec_chans/app/eventos/main_eventos.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-users-cog me-2"></i>Usuarios
                    </div>
                    <div class="card-body">
                        <p class="card-text">Gestiona los usuarios y sus permisos de acceso.</p>
                        <a href="/ec_chans/app/usuarios/main_usuarios.php" class="btn">Ir al Módulo</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-gear me-2"></i>Configuración
                </div>
                <div class="card-body">
                    <p class="card-text">Ajusta las opciones y parámetros generales del sistema.</p>
                    <a href="/ec_chans/app/configuracion/main_configuracion.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-question-circle me-2"></i>Ayuda
                </div>
                <div class="card-body">
                    <p class="card-text">Encuentra guías, tutoriales y soporte para el uso de la aplicación.</p>
                    <a href="/ec_chans/app/ayuda/main_ayuda.php" class="btn">Ir al Módulo</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>