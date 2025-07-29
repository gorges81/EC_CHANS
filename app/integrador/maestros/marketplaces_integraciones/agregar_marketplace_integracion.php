<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }

$marketplaces = obtenerTodosLosMarketplaces($link);
$importaciones = obtenerTodasLasImportaciones($link);
?>
<link rel="stylesheet" href="/ec_chans/css/style_marketplace_integraciones.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main_marketplaces_integraciones.php">Integraciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
        </ol>
    </nav>
    
    <h2 class="text-dark-blue mb-4">Agregar Nueva Integración</h2>

    <div class="card p-4 shadow-sm">
        <form id="formAgregarIntegracion">
            <input type="hidden" name="action" value="agregar">
            <div class="mb-3">
                <label for="id_marketplace" class="form-label">Marketplace</label>
                <select class="form-select" id="id_marketplace" name="id_marketplace" required>
                    <option value="">-- Seleccione un Marketplace --</option>
                    <?php foreach ($marketplaces as $mp): ?>
                        <option value="<?php echo $mp['id']; ?>"><?php echo htmlspecialchars($mp['marketplace']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_importacion" class="form-label">Importación</label>
                <select class="form-select" id="id_importacion" name="id_importacion" required>
                    <option value="">-- Seleccione una Importación --</option>
                    <?php foreach ($importaciones as $imp): ?>
                        <option value="<?php echo $imp['id']; ?>"><?php echo htmlspecialchars($imp['fecha_correlativa_display']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="url_select" class="form-label">URL</label>
                <select class="form-select" id="url_select" name="url_select" required disabled>
                    <option value="">-- Seleccione una importación primero --</option>
                </select>
            </div>
            
            <div id="fileDisplayContainer" class="mb-3" style="display:none;">
                <label class="form-label">Archivo:</label>
                <input type="text" class="form-control" id="fileDisplay" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="main_marketplaces_integraciones.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marketplace_integraciones.js"></script>