<?php
// /app/integrador/active/main_active.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDIntegrador.php';
require_once PROJECT_ROOT . '/db/CRUDActive.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id'])) { exit(); }

$correlativos = obtenerCorrelativosDeImportaciones($link);
$ultimaIntegracion = obtenerDatosUltimaIntegracionActive($link); 
?>
<link rel="stylesheet" href="/ec_chans/css/style_active.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item active" aria-current="page">Active</li>
        </ol>
    </nav>
    
    <h2 class="text-dark-blue mb-4">Integrador / Active</h2>
    <p class="lead">Seleccione una importación para migrar los datos del archivo "Active" a su tabla.</p>

    <div id="integrationMessages"></div>

    <div class="card p-4">
        <div class="row align-items-end">
            <div class="col-md-8">
                <label for="correlativoSelect" class="form-label fw-bold">1. Seleccione la Importación de Origen</label>
                <select id="correlativoSelect" class="form-select">
                    <option value="" selected disabled>Elija un correlativo...</option>
                    <?php foreach ($correlativos as $corr): ?>
                        <option value="<?php echo htmlspecialchars($corr['id']); ?>"><?php echo htmlspecialchars($corr['fecha_correlativa_display']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button id="btnIntegrarActive" class="btn btn-primary w-100" disabled>
                    <i class="fas fa-cogs me-2"></i>2. Integrar Datos de Active
                </button>
            </div>
        </div>
        <div id="progressContainer" class="mt-4" style="display: none;">
            <p id="progressText">Procesando archivo, por favor espere...</p>
            <div class="progress"><div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div></div>
        </div>
    </div>
    
    <?php if ($ultimaIntegracion): 
        $correlativo_usado = $ultimaIntegracion['import_correlativo'];
        $num_registros = contarRegistrosActivePorCorrelativo($correlativo_usado, $link);
    ?>
    <div id="ultimaIntegracionBox" class="card p-3 mt-4 shadow-sm">
        <h5 class="card-title text-dark-blue">Última Integración Realizada (Active)</h5>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="me-3"><strong>Fecha:</strong> <span id="lastIntegrationDate"><?php echo htmlspecialchars(date("Y-m-d", strtotime($ultimaIntegracion['import_fecha'] ?? 'now'))); ?></span></span>
                <span class="me-3"><strong>Hora:</strong> <span id="lastIntegrationTime"><?php echo htmlspecialchars(date("H:i:s", strtotime($ultimaIntegracion['import_fecha'] ?? 'now'))); ?></span></span>
                <span class="me-3"><strong>Correlativo Usado:</strong> <span id="lastIntegrationCorr"><?php echo htmlspecialchars($correlativo_usado); ?></span></span>
                <span class="me-3"><strong>Nro. Registros:</strong> <span id="lastIntegrationCount" class="fw-bold"><?php echo $num_registros; ?></span></span>
            </div>
            <div class="d-flex gap-2">
                <button id="btnVerDatosActive" class="btn btn-info" data-correlativo="<?php echo htmlspecialchars($correlativo_usado); ?>">
                    <i class="fas fa-eye me-2"></i>Ver Datos
                </button>
                <button id="btnVaciarActive" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Eliminar Todo
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<div class="modal fade" id="datosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="datosModalLabel">Datos de la Integración</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <div id="gridContainer">
                </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_active.js"></script>