<?php
// /app/integrador/main_integrador.php

require_once __DIR__ . '/../../includes/bootstrap.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

// --- Definición de los cuadros para Raíces ---
$raices = [
    'Category', 'Active', 'All', 'Shopify PH', 'Shopify PPB', 'eBay', 
    'Walmart Inv', 'Walmart Item', 'Inv Shopify PH', 'Inv Shopify PPB'
];

// --- Mapa de enlaces para los cuadros de Raíces ---
$raices_links = [
    'Category'       => '/ec_chans/app/integrador/category/main_category.php',
    'Active'         => '/ec_chans/app/integrador/active/main_active.php',
    'All'            => '/ec_chans/app/integrador/all/main_all.php',
    'Shopify PH'     => '/ec_chans/app/integrador/shopify_ph/main_shopify_ph.php',
    'Shopify PPB'    => '/ec_chans/app/integrador/shopify_ppb/main_shopify_ppb.php',
    'eBay'           => '/ec_chans/app/integrador/ebay/main_ebay.php',
    'Walmart Inv'    => '/ec_chans/app/integrador/walmart_inv/main_walmart_inv.php',
    'Walmart Item'   => '/ec_chans/app/integrador/walmart_item/main_walmart_item.php',
    'Inv Shopify PH' => '/ec_chans/app/integrador/inv_shopify_ph/main_inv_shopify_ph.php',
    'Inv Shopify PPB'=> '/ec_chans/app/integrador/inv_shopify_ppb/main_inv_shopify_ppb.php'
];

// --- Definición de los cuadros para Maestros en el orden solicitado ---
$maestros = [
    'Marketplaces',
    'Importaciones por marketplaces',
    'Marcas',
    'Productos',
    'Imagenes'
];

// --- Mapa de enlaces para los cuadros de Maestros ---
$maestros_links = [
    'Marketplaces' => '/ec_chans/app/integrador/maestros/marketplaces/main_marketplaces.php',
    'Importaciones por marketplaces' => '/ec_chans/app/integrador/maestros/marketplaces_integraciones/main_marketplaces_integraciones.php',
    'Marcas' => '/ec_chans/app/integrador/maestros/marcas/main_marcas.php', // <-- ENLACE ACTUALIZADO
    'Productos' => '#', // Enlace futuro
    'Imagenes' => '#' // Enlace futuro
];

?>
<link rel="stylesheet" href="/ec_chans/css/style_integrador.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="text-dark-blue mb-4">Módulo de Integración</h2>
    <p class="lead">Selecciona una entidad para administrar sus datos.</p>

    <h3 class="mt-4 mb-3">Raíces</h3>
    <div class="row">
        <?php foreach ($raices as $raiz): 
            $link = $raices_links[$raiz] ?? '#';
        ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-title">
                        <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($raiz); ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">Gestionar los datos base de <?php echo strtolower(htmlspecialchars($raiz)); ?>.</p>
                        <a href="<?php echo $link; ?>" class="btn mt-auto <?php echo ($link === '#') ? 'disabled' : ''; ?>">Administrar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr class="my-4">

    <h3 class="mt-4 mb-3">Maestros</h3>
    <div class="row">
        <?php foreach ($maestros as $maestro): 
            $link = $maestros_links[$maestro] ?? '#';
        ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-title">
                        <i class="fas fa-database me-2"></i><?php echo htmlspecialchars($maestro); ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">Administrar el maestro de <?php echo strtolower(htmlspecialchars($maestro)); ?>.</p>
                        <a href="<?php echo $link; ?>" class="btn mt-auto <?php echo ($link === '#') ? 'disabled' : ''; ?>">Administrar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php
include_once PROJECT_ROOT . '/includes/footer.php';
?>
<script src="/ec_chans/js/script_integrador.js"></script>