<?php
// /app/eventos/main_eventos.php

// --- BLOQUE DE ARRANQUE CORREGIDO ---
// Se carga primero el bootstrap que define las constantes, la sesión y la conexión a BD.
require_once __DIR__ . '/../../includes/bootstrap.php';
// Ahora cargamos las dependencias específicas de esta página.
require_once PROJECT_ROOT . '/db/CRUDEventos.php';
// Finalmente, incluimos el header.
include_once PROJECT_ROOT . '/includes/header.php';

// Verificación de seguridad
if (!isset($_SESSION['usuario_id'])) {
    // Redirige al login si no hay sesión activa
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

// Verificamos si hay un término de búsqueda enviado desde el formulario
$search_term = isset($_GET['search_event']) ? trim($_GET['search_event']) : '';

// Obtenemos los eventos desde la base de datos
$eventos = obtenerTodosLosLosEventos($search_term);
?>

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Consola de Eventos del Sistema</h2>
    <p class="lead">Registro de todas las actividades importantes, ordenadas por fecha y hora (la más reciente primero).</p>

    <div class="card mb-4 p-3 shadow-sm">
        <form action="main_eventos.php" method="GET" class="row g-3 align-items-center">
            <div class="col-md-9">
                <label for="searchEventInput" class="visually-hidden">Buscar eventos</label>
                <input type="text" class="form-control" id="searchEventInput" name="search_event" placeholder="Buscar por título, descripción o tipo..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-warning w-100">
                    <i class="fas fa-search me-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <div class="event-console-container mt-4">
        <?php if (empty($eventos)): ?>
            <div class="text-center p-3 text-muted">
                No se encontraron eventos para mostrar.
            </div>
        <?php else: ?>
            <?php foreach ($eventos as $evento): 
                $tipo_evento_clase = str_replace(' ', '_', htmlspecialchars($evento['tipo_evento']));
            ?>
                <div class="event-console-item">
                    <span class="event-timestamp"><?php echo htmlspecialchars($evento['fecha_evento']); ?></span>
                    <span class="event-title"><?php echo htmlspecialchars($evento['titulo']); ?></span>
                    
                    <span class="event-type event-type-<?php echo $tipo_evento_clase; ?>">
                        [<?php echo htmlspecialchars($evento['tipo_evento']); ?>]
                    </span>
                    
                    <p class="event-description"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>