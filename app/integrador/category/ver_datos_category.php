<?php
// /app/integrador/category/ver_datos_category.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDIntegrador.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['correlativo'])) {
    header("Location: /ec_chans/index.php");
    exit();
}

$correlativo = trim($_GET['correlativo']);
$datos_category = obtenerDatosCategoryPorCorrelativo($correlativo, $link);

// Mapeo de nombres de columna de la BD a encabezados amigables para el usuario
$header_map = [ 'status' => 'Status', 'title' => 'Title', 'sku' => 'SKU', 'product_type' => 'Product Type', 'item_name' => 'Item Name', 'brand_name' => 'Brand Name', 'product_id_type' => 'Product ID Type', 'product_id' => 'Product ID', 'item_type_keyword' => 'Item Type Keyword', 'model_number' => 'Model Number', 'model_name' => 'Model Name', 'manufacturer' => 'Manufacturer', 'item_condition' => 'Item Condition', 'list_price' => 'List Price', 'merchant_release_date' => 'Release Date', 'maximum_order_quantity' => 'Max Order Qty', 'offering_can_be_gift_messaged' => 'Gift Message', 'is_gift_wrap_available' => 'Gift Wrap', 'import_designation' => 'Import Designation', 'your_price_usd' => 'Price USD', 'product_description' => 'Product Description', 'bullet_point_1' => 'Bullet Point 1', 'bullet_point_2' => 'Bullet Point 2', 'bullet_point_3' => 'Bullet Point 3', 'bullet_point_4' => 'Bullet Point 4', 'bullet_point_5' => 'Bullet Point 5', 'generic_keywords' => 'Generic Keywords', 'special_features_1' => 'Special Feature 1', 'special_features_2' => 'Special Feature 2', 'special_features_3' => 'Special Feature 3', 'special_features_4' => 'Special Feature 4', 'special_features_5' => 'Special Feature 5', 'style' => 'Style', 'department_name' => 'Department', 'target_gender' => 'Target Gender', 'age_range_description' => 'Age Range', 'material_1' => 'Material 1', 'material_2' => 'Material 2', 'material_3' => 'Material 3', 'number_of_items' => 'Number of Items', 'color' => 'Color', 'color_map' => 'Color Map', 'size' => 'Size', 'size_map' => 'Size Map', 'part_number' => 'Part Number', 'item_shape' => 'Item Shape', 'parentage_level' => 'Parentage', 'relationship_type' => 'Relationship', 'parent_sku' => 'Parent SKU', 'variation_theme_name' => 'Variation Theme', 'country_of_origin' => 'Country', 'warranty_description' => 'Warranty', 'are_batteries_required' => 'Batteries?', 'dangerous_goods_regulations' => 'Dangerous Goods', 'mandatory_cautionary_statement' => 'Cautionary Statement', 'main_image_url' => 'Main Image URL', 'other_image_url_1' => 'Other Image 1', 'other_image_url_2' => 'Other Image 2', 'other_image_url_3' => 'Other Image 3', 'other_image_url_4' => 'Other Image 4', 'other_image_url_5' => 'Other Image 5', 'other_image_url_6' => 'Other Image 6', 'other_image_url_7' => 'Other Image 7', 'other_image_url_8' => 'Other Image 8', 'swatch_image_url' => 'Swatch Image URL' ];
$image_columns = [ 'main_image_url', 'other_image_url_1', 'other_image_url_2', 'other_image_url_3', 'other_image_url_4', 'other_image_url_5', 'other_image_url_6', 'other_image_url_7', 'other_image_url_8', 'swatch_image_url' ];
?>
<div class="container dashboard-section p-4 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/ec_chans/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/main_integrador.php">Integrador</a></li>
            <li class="breadcrumb-item"><a href="/ec_chans/app/integrador/category/main_category.php">Category</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ver Datos</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark-blue mb-0">Datos Integrados del Correlativo: <?php echo htmlspecialchars($correlativo); ?></h2>
        <a href="main_category.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
    <div class="card p-3 shadow-sm">
        <div class="table-responsive data-grid-container">
            <table class="table table-striped table-hover datatable" style="width:100%">
                <thead>
                    <tr>
                        <?php foreach ($header_map as $value): ?>
                            <th><?php echo htmlspecialchars($value); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datos_category)): ?>
                        <?php foreach ($datos_category as $fila): ?>
                            <tr>
                                <?php foreach ($header_map as $key => $value): ?>
                                    <td>
                                        <?php
                                        $cell_content = $fila[$key] ?? '';
                                        if (in_array($key, $image_columns) && filter_var($cell_content, FILTER_VALIDATE_URL)) {
                                            echo '<a href="' . htmlspecialchars($cell_content) . '" target="_blank"><img src="' . htmlspecialchars($cell_content) . '" alt="Imagen" style="max-width: 80px; max-height: 80px; object-fit: cover;"></a>';
                                        } else {
                                            echo htmlspecialchars($cell_content);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
