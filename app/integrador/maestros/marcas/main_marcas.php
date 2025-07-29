<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
include_once PROJECT_ROOT . '/includes/header.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: /ec_chans/index.php"); exit(); }
?>
<link rel="stylesheet" href="/ec_chans/css/style_marcas.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item active" aria-current="page">Maestro de Marcas</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark-blue">Maestro de Marcas</h2>
        <a href="agregar_marca.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Agregar Marca
        </a>
    </div>

    <div class="card p-4 shadow-sm">
        <table id="marcasGrid" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sigla</th>
                    <th>Marca</th>
                    <th>Marketplace</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_marcas.js"></script>