<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }

$integracion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$integracion = obtenerIntegracionPorId($integracion_id, $link);
if (!$integracion) { header("Location: main_marketplaces_integraciones.php"); exit(); }

$marketplaces = obtenerTodosLosMarketplaces($link);
$importaciones = obtenerTodasLasImportaciones($link);
?>
<link rel="stylesheet" href="/ec_chans/css/style_marketplace_integraciones.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="text-dark-blue mb-4">Modificar Integración</h2>
    <div class="card p-4 shadow-sm">
        <form id="formModificarIntegracion">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="id" value="<?php echo $integracion['id']; ?>">
            
            <div class="mb-3">
                <label for="id_marketplace" class="form-label">Marketplace</label>
                <select class="form-select" id="id_marketplace" name="id_marketplace" required>
                    <?php foreach ($marketplaces as $mp): ?>
                        <option value="<?php echo $mp['id']; ?>" <?php echo ($mp['id'] == $integracion['id_marketplace']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mp['marketplace']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_importacion" class="form-label">Importación</label>
                <select class="form-select" id="id_importacion" name="id_importacion" required>
                     <?php foreach ($importaciones as $imp): ?>
                        <option value="<?php echo $imp['id']; ?>" <?php echo ($imp['id'] == $integracion['id_importacion']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($imp['fecha_correlativa_display']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="url_select" class="form-label">URL</label>
                <select class="form-select" id="url_select" name="url_select" required>
                    <option value="<?php echo htmlspecialchars($integracion['url_select']); ?>"><?php echo htmlspecialchars($integracion['url_select']); ?></option>
                </select>
            </div>

            <div id="fileDisplayContainer" class="mb-3" style="display:none;">
                <label class="form-label">Archivo:</label>
                <input type="text" class="form-control" id="fileDisplay" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($integracion['description']); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="main_marketplaces_integraciones.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marketplace_integraciones.js"></script>