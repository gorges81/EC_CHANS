<?php
// /app/importaciones/modificar_importacion.php

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: /ec_chans/index.php");
    exit();
}

$importacion_id = intval($_GET['id']);
$importacion = obtenerImportacionPorId($importacion_id, $link);

if (!$importacion) {
    echo "<div class='alert alert-danger'>Importación no encontrada.</div>";
    include_once PROJECT_ROOT . '/includes/footer.php';
    exit();
}

$file_inputs = [
    'Category 1' => 'url_category1', 'Category 2' => 'url_category2', 'Active' => 'url_active', 'All' => 'url_all',
    'Shopify PH' => 'url_shopify_ph', 'Shopify PPB' => 'url_shopify_ppb', 'eBay' => 'url_ebay',
    'Walmart Inv' => 'url_walmart_inv', 'Walmart Item' => 'url_walmart_item',
    'Inv Shopify PH' => 'url_inv_shopify_ph', 'Inv Shopify PPB' => 'url_inv_shopify_ppb'
];

$backUrl = 'main_importaciones.php';
?>
<link rel="stylesheet" href="/ec_chans/css/style_importaciones.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Modificar Importación</h2>

    <div class="alert alert-secondary">
        <span class="me-3"><strong>ID:</strong> <span class="text-danger"><?php echo htmlspecialchars($importacion['id']); ?></span></span>
        <span class="me-3"><strong>Fecha:</strong> <span class="text-danger"><?php echo htmlspecialchars($importacion['fecha_importacion']); ?></span></span>
        <span class="me-3"><strong>Correlativo:</strong> <span class="text-danger"><?php echo htmlspecialchars($importacion['fecha_correlativa_display']); ?></span></span>
    </div>

    <div id="importMessages"></div>
    <div class="card p-4 shadow-sm">
        <div class="card-body">
            <form id="formModificarImportacion" action="procesar_modificacion_importacion.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="importacion_id" value="<?php echo $importacion_id; ?>">

                <h4 class="mb-3 text-dark-blue">Archivos Documento</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <?php foreach ($file_inputs as $displayName => $fieldName):
                                $current_file = isset($importacion[$fieldName]) ? basename($importacion[$fieldName]) : '';
                                $input_id = 'file_' . $fieldName;
                                $display_id = 'display_' . $fieldName;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($displayName); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-dark-blue btn-select-file" data-target-input="<?php echo $input_id; ?>">
                                            <i class="fas fa-search me-2"></i>Buscar
                                        </button>
                                        <input type="file" id="<?php echo $input_id; ?>" name="<?php echo $fieldName; ?>" class="d-none">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="<?php echo $display_id; ?>" value="<?php echo htmlspecialchars($current_file); ?>" readonly placeholder="Ningún archivo seleccionado">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?php echo $backUrl; ?>" class="btn btn-secondary btn-uniform-width"><i class="fas fa-arrow-left me-2"></i>Volver</a>
                    <button type="submit" class="btn btn-primary btn-uniform-width" id="btnGuardarCambios">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_importaciones.js"></script>