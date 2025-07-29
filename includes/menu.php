<?php
// /includes/menu.php

// Define la página actual para marcar el menú como activo
$current_page = basename($_SERVER['PHP_SELF']);
// Obtiene el directorio actual
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// --- Lógica para mantener los menús abiertos ---
// Estamos en cualquier página del módulo Integrador
$is_integrador_page = in_array($current_dir, ['integrador', 'active', 'all', 'category', 'shopify_ph', 'shopify_ppb', 'ebay', 'walmart_inv', 'walmart_item', 'inv_shopify_ph', 'inv_shopify_ppb']);

// Estamos específicamente en una página de Raíces
$is_raices_page = in_array($current_page, [
    'main_category.php', 'main_active.php', 'main_all.php', 'main_shopify_ph.php', 
    'main_shopify_ppb.php', 'main_ebay.php', 'main_walmart_inv.php', 'main_walmart_item.php', 
    'main_inv_shopify_ph.php', 'main_inv_shopify_ppb.php'
]);

// Estamos específicamente en una página de Maestros (cuando existan)
$is_maestros_page = in_array($current_page, [
    // 'main_marketplaces.php', 'main_marcas.php', etc.
]);
?>
<div class="list-group list-group-flush">
    <a href="/ec_chans/dashboard.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-tachometer-alt me-2"></i>Dashboard
    </a>
    
    <a href="/ec_chans/app/importaciones/main_importaciones.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_importaciones.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-upload me-2"></i>Importaciones
    </a>

    <!-- Menú Nivel 1: Integrador -->
    <a href="#integradorSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo $is_integrador_page ? 'true' : 'false'; ?>" class="list-group-item list-group-item-action dropdown-toggle <?php echo $is_integrador_page ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-cogs me-2"></i>Integrador
    </a>
    <div class="collapse <?php echo $is_integrador_page ? 'show' : ''; ?>" id="integradorSubmenu">
        <div class="list-group list-group-flush">
            <a href="/ec_chans/app/integrador/main_integrador.php" class="list-group-item list-group-item-action submenu-item <?php echo ($current_page == 'main_integrador.php') ? 'active' : ''; ?>">Vista General</a>
            
            <!-- Menú Nivel 2: Raíces -->
            <a href="#raicesSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo $is_raices_page ? 'true' : 'false'; ?>" class="list-group-item list-group-item-action submenu-item dropdown-toggle submenu-header">
                Raíces
            </a>
            <div class="collapse <?php echo $is_raices_page ? 'show' : ''; ?>" id="raicesSubmenu">
                <div class="list-group list-group-flush">
                    <a href="/ec_chans/app/integrador/category/main_category.php" class="list-group-item list-group-item-action sub-submenu-item">Category</a>
                    <a href="/ec_chans/app/integrador/active/main_active.php" class="list-group-item list-group-item-action sub-submenu-item">Active</a>
                    <a href="/ec_chans/app/integrador/all/main_all.php" class="list-group-item list-group-item-action sub-submenu-item">All</a>
                    <a href="/ec_chans/app/integrador/shopify_ph/main_shopify_ph.php" class="list-group-item list-group-item-action sub-submenu-item">Shopify PH</a>
                    <a href="/ec_chans/app/integrador/shopify_ppb/main_shopify_ppb.php" class="list-group-item list-group-item-action sub-submenu-item">Shopify PPB</a>
                    <a href="/ec_chans/app/integrador/ebay/main_ebay.php" class="list-group-item list-group-item-action sub-submenu-item">eBay</a>
                    <a href="/ec_chans/app/integrador/walmart_inv/main_walmart_inv.php" class="list-group-item list-group-item-action sub-submenu-item">Walmart Inv</a>
                    <a href="/ec_chans/app/integrador/walmart_item/main_walmart_item.php" class="list-group-item list-group-item-action sub-submenu-item">Walmart Item</a>
                    <a href="/ec_chans/app/integrador/inv_shopify_ph/main_inv_shopify_ph.php" class="list-group-item list-group-item-action sub-submenu-item">Inv Shopify PH</a>
                    <a href="/ec_chans/app/integrador/inv_shopify_ppb/main_inv_shopify_ppb.php" class="list-group-item list-group-item-action sub-submenu-item">Inv Shopify PPB</a>
                </div>
            </div>

            <!-- Menú Nivel 2: Maestros -->
            <a href="#maestrosSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo $is_maestros_page ? 'true' : 'false'; ?>" class="list-group-item list-group-item-action submenu-item dropdown-toggle submenu-header">
                Maestros
            </a>
            <div class="collapse <?php echo $is_maestros_page ? 'show' : ''; ?>" id="maestrosSubmenu">
                 <div class="list-group list-group-flush">
                    <!-- ORDEN CORREGIDO Y NUEVO ITEM AÑADIDO -->
                    <a href="/ec_chans/app/integrador/maestros/marketplaces/main_marketplaces.php" class="list-group-item list-group-item-action sub-submenu-item">Marketplaces</a>
                    <a href="/ec_chans/app/integrador/maestros/marketplaces_integraciones/main_marketplaces_integraciones.php" class="list-group-item list-group-item-action sub-submenu-item">Importaciones por marketplaces</a>
                    <a href="/ec_chans/app/integrador/maestros/marcas/main_marcas.php" class="list-group-item list-group-item-action sub-submenu-item">Marcas</a>
                    <a href="#" class="list-group-item list-group-item-action sub-submenu-item disabled">Productos</a>
                    <a href="#" class="list-group-item list-group-item-action sub-submenu-item disabled">Imagenes</a>
                </div>
            </div>
        </div>
    </div>
    
    <a href="/ec_chans/app/analisis/main_analisis.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_analisis.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-chart-bar me-2"></i>Análisis
    </a>
    <a href="/ec_chans/app/auditorias/main_auditorias.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_auditorias.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-tasks me-2"></i>Auditorías
    </a>
    <a href="/ec_chans/app/eventos/main_eventos.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_eventos.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-bell me-2"></i>Eventos
    </a>
    <a href="/ec_chans/app/reportes/main_reportes.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_reportes.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-file-invoice me-2"></i>Reportes
    </a>
    <a href="/ec_chans/app/usuarios/main_usuarios.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'main_usuarios.php') ? 'active' : ''; ?>">
        <i class="fas fa-fw fa-users me-2"></i>Usuarios
    </a>
</div>