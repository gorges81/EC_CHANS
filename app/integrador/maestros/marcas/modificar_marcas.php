<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarcas.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php';
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }

$marca_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$marca = obtenerMarcaPorId($marca_id, $link);
if (!$marca) { header("Location: main_marcas.php"); exit(); }

$marketplaces = obtenerTodosLosMarketplaces($link);
?>
<link rel="stylesheet" href="/ec_chans/css/style_marcas.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="text-dark-blue mb-4">Modificar Marca</h2>
    <div class="card p-4 shadow-sm">
        <form id="formModificarMarca">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="id" value="<?php echo $marca['id']; ?>">
            
            <div class="mb-3">
                <label for="id_marketplace" class="form-label">Marketplace</label>
                <select class="form-select" id="id_marketplace" name="id_marketplace" required>
                    <?php foreach ($marketplaces as $mp): ?>
                        <option value="<?php echo $mp['id']; ?>" <?php echo ($mp['id'] == $marca['id_marketplace']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mp['marketplace']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="siglas" class="form-label">Sigla</label>
                <input type="text" class="form-control" id="siglas" name="siglas" value="<?php echo htmlspecialchars($marca['siglas']); ?>">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Marca</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($marca['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($marca['description']); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="main_marcas.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marcas.js"></script>