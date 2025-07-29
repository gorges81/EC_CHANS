<?php
// /app/integrador/walmart_item/main_walmart_item.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
// Nombre del archivo CRUD corregido
require_once PROJECT_ROOT . '/db/CRUDWalmart_item.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php");
    exit();
}

$correlativos = obtenerCorrelativosConArchivo($link, 'url_walmart_item');
$ultimaIntegracion = obtenerDatosUltimaIntegracionWalmartItem($link);
?>
<!-- El resto del código HTML de la vista permanece igual -->
<link rel="stylesheet" href="/ec_chans/css/style_walmart_item.css">
<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item active" aria-current="page">Walmart Item</li>
        </ol>
    </nav>
    <h2 class="text-dark-blue mb-4">Integrador / Walmart Item</h2>
    <p class="lead">Seleccione una importación para migrar los datos del archivo de items de Walmart.</p>
    <div class="card p-4 shadow-sm">
        <div class="row align-items-end">
            <div class="col-md-8">
                <label for="correlativoSelect" class="form-label"><strong>Seleccionar Lote de Importación:</strong></label>
                <select id="correlativoSelect" class="form-select">
                    <option value="">-- Elija una importación --</option>
                    <?php foreach ($correlativos as $imp): ?>
                        <option value="<?php echo htmlspecialchars($imp['id']); ?>">
                            <?php echo htmlspecialchars($imp['fecha_correlativa_display']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button id="btnIntegrarWalmartItem" class="btn btn-primary w-100" disabled>
                    <i class="fas fa-cogs me-2"></i>Iniciar Integración
                </button>
            </div>
        </div>
    </div>
    <?php if ($ultimaIntegracion): ?>
    <?php
        $correlativo_usado = $ultimaIntegracion['import_correlativo'];
        $num_registros = contarRegistrosWalmartItemPorCorrelativo($correlativo_usado, $link);
    ?>
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Última Integración Realizada (Walmart Item)</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="me-3"><strong>Fecha:</strong> <span class="fw-bold"><?php echo date("d/m/Y H:i", strtotime($ultimaIntegracion['import_fecha'])); ?></span></span>
                    <span class="me-3"><strong>Correlativo Usado:</strong> <span class="fw-bold"><?php echo htmlspecialchars($correlativo_usado); ?></span></span>
                    <span class="me-3"><strong>Nro. Registros:</strong> <span class="fw-bold"><?php echo $num_registros; ?></span></span>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <button id="btnVerDatosWalmartItem" class="btn btn-info" data-correlativo="<?php echo htmlspecialchars($correlativo_usado); ?>">
                        <i class="fas fa-eye me-2"></i>Ver Datos
                    </button>
                    <button id="btnVaciarWalmartItem" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar Todo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<div class="modal fade" id="datosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Datos de la Integración</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="gridContainer" class="table-responsive"></div>
      </div>
    </div>
  </div>
</div>
<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_walmart_item.js"></script>
