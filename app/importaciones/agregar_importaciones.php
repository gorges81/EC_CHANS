<?php
// /app/importaciones/agregar_importaciones.php

require_once __DIR__ . '/../../includes/bootstrap.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

// Mapa con los nombres de archivo para asegurar consistencia
$file_inputs = [
    'Category 1'      => 'category1', 'Category 2'      => 'category2',
    'Active'          => 'active', 'All'             => 'all',
    'Shopify PH'      => 'shopify_ph', 'Shopify PPB'     => 'shopify_ppb',
    'eBay'            => 'ebay', 'Walmart Inv'     => 'walmart_inv',
    'Walmart Item'    => 'walmart_item', 'Inv Shopify PH'  => 'inv_shopify_ph',
    'Inv Shopify PPB' => 'inv_shopify_ppb'
];

$backUrl = 'main_importaciones.php';
?>
<link rel="stylesheet" href="/ec_chans/css/style_importaciones.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="main_importaciones.php">Importaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agregar Nueva Importación</li>
        </ol>
    </nav>

    <h2 class="mb-4 text-dark-blue">Agregar Nueva Importación</h2>
    <p class="lead">Cargue uno o más archivos para registrar una nueva importación.</p>
    
    <div class="card p-4 shadow-sm">
        <div class="card-body">
            <form id="formNuevaImportacion" action="procesar_nueva_importacion.php" method="POST" enctype="multipart/form-data">
                <h4 class="mb-3 text-dark-blue">Archivos Documento</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Nombre</th>
                                <th style="width: 20%;">Acción</th>
                                <th style="width: 55%;">Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($file_inputs as $displayName => $fieldNameSuffix):
                                $input_id = 'file_' . $fieldNameSuffix;
                                $display_id = 'display_' . $fieldNameSuffix;
                                $form_field_name = 'url_' . $fieldNameSuffix;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($displayName); ?></td>
                                    <td>
                                        <!-- Botón azul que el usuario ve y usa -->
                                        <button type="button" class="btn btn-dark-blue btn-select-file" data-target-input="<?php echo $input_id; ?>">
                                            <i class="fas fa-search me-2"></i>Buscar
                                        </button>
                                        <!-- Input de archivo real, ahora oculto con d-none -->
                                        <input type="file" id="<?php echo $input_id; ?>" name="<?php echo $form_field_name; ?>" class="d-none">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="<?php echo $display_id; ?>" readonly placeholder="Ningún archivo seleccionado">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?php echo $backUrl; ?>" class="btn btn-secondary btn-uniform-width">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <button type="submit" class="btn btn-primary btn-uniform-width" id="btnGuardarImportacion" disabled>
                        <i class="fas fa-upload me-2"></i>Guardar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<!-- Se llama al script unificado -->
<script src="/ec_chans/js/script_importaciones.js"></script>