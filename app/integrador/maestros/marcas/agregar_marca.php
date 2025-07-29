<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php'; // Para obtener la lista de marketplaces
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }

$marketplaces = obtenerTodosLosMarketplaces($link);
?>
<link rel="stylesheet" href="/ec_chans/css/style_marcas.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main_marcas.php">Marcas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
        </ol>
    </nav>
    
    <h2 class="text-dark-blue mb-4">Agregar Nueva Marca</h2>

    <div class="card p-4 shadow-sm">
        <form id="formAgregarMarca">
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
                <label for="siglas" class="form-label">Sigla</label>
                <input type="text" class="form-control" id="siglas" name="siglas">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Marca</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="main_marcas.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marcas.js"></script>