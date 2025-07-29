<?php
// /app/integrador/shopify_ppb/main_shopify_ppb.php

// Se incluyen todos los archivos necesarios para que la página funcione
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
require_once PROJECT_ROOT . '/db/CRUDShopifyPPB.php';
include_once PROJECT_ROOT . '/includes/header.php';

// Verificación de sesión de usuario
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php");
    exit();
}

// Se obtienen los datos necesarios para la vista
$correlativos = obtenerCorrelativosConArchivo($link, 'url_shopify_ppb');
$ultimaIntegracion = obtenerDatosUltimaIntegracionShopifyPPB($link);
?>
<link rel="stylesheet" href="/ec_chans/css/style_shopify_ppb.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <!-- Miga de Pan (Restaurada) -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopify PPB</li>
        </ol>
    </nav>
    
    <!-- Título Principal (Restaurado) -->
    <h2 class="text-dark-blue mb-4">Integrador / Shopify PPB</h2>
    <p class="lead">Seleccione una importación para migrar los datos del archivo de productos de Shopify PPB.</p>

    <!-- Cuadro de Selección de Lote (Restaurado) -->
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
                <button id="btnIntegrarShopifyPPB" class="btn btn-primary w-100" disabled>
                    <i class="fas fa-cogs me-2"></i>Iniciar Integración
                </button>
            </div>
        </div>
    </div>

    <!-- Cuadro de Última Integración (Restaurado) -->
    <?php if ($ultimaIntegracion): ?>
    <?php
        $correlativo_usado = $ultimaIntegracion['import_correlativo'];
        $num_registros = contarRegistrosShopifyPPBPorCorrelativo($correlativo_usado, $link);
    ?>
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Última Integración Realizada (Shopify PPB)</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="me-3"><strong>Fecha:</strong> <span class="fw-bold"><?php echo date("d/m/Y H:i", strtotime($ultimaIntegracion['import_fecha'])); ?></span></span>
                    <span class="me-3"><strong>Correlativo Usado:</strong> <span class="fw-bold"><?php echo htmlspecialchars($correlativo_usado); ?></span></span>
                    <span class="me-3"><strong>Nro. Registros:</strong> <span class="fw-bold"><?php echo $num_registros; ?></span></span>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <button id="btnVerDatosShopifyPPB" class="btn btn-info" data-correlativo="<?php echo htmlspecialchars($correlativo_usado); ?>">
                        <i class="fas fa-eye me-2"></i>Ver Datos
                    </button>
                    <button id="btnVaciarShopifyPPB" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar Todo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal para mostrar datos (Restaurado) -->
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
<script src="/ec_chans/js/script_shopify_ppb.js"></script>