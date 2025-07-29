<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }
?>
<link rel="stylesheet" href="/ec_chans/css/style_marketplace.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item"><a href="main_marketplaces.php">Marketplaces</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
        </ol>
    </nav>
    
    <h2 class="text-dark-blue mb-4">Agregar Nuevo Marketplace</h2>

    <div class="card p-4 shadow-sm">
        <form id="formAgregarMarketplace">
            <input type="hidden" name="action" value="agregar">
            <div class="mb-3">
                <label for="marketplace" class="form-label">Marketplace</label>
                <input type="text" class="form-control" id="marketplace" name="marketplace" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input type="url" class="form-control" id="url" name="url">
            </div>
            <div class="mb-3">
                <label for="usuario_marketplace" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario_marketplace" name="usuario_marketplace">
            </div>
            <div class="mb-3">
                <label for="contrasena_marketplace" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena_marketplace" name="contrasena_marketplace">
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="main_marketplaces.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marketplace.js"></script>