<?php
// /app/integrador/category/obtener_datos_grid.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDIntegrador.php';

header('Content-Type: application/json');
$response = ['success' => false, 'data' => [], 'headers' => []];

if (isset($_POST['correlativo'])) {
    $correlativo = trim($_POST['correlativo']);
    $datos = obtenerDatosCategoryPorCorrelativo($correlativo, $link);
    
    if ($datos !== null) {
        $response['success'] = true;
        
        // --- NUEVO: Limpiar saltos de línea de los datos ---
        $cleaned_data = array_map(function($row) {
            foreach ($row as $key => $value) {
                // Reemplaza saltos de línea y retornos de carro por un espacio
                $row[$key] = str_replace(["\r", "\n"], ' ', $value);
            }
            return $row;
        }, $datos);
        
        $response['data'] = $cleaned_data;
        
        // Mapeo de nombres de columna de la BD a encabezados amigables
        $response['headers'] = [
            'status' => 'Status', 'title' => 'Title', 'sku' => 'SKU', 'product_type' => 'Product Type',
            'item_name' => 'Item Name', 'brand_name' => 'Brand Name', 'product_id_type' => 'Product ID Type',
            'product_id' => 'Product ID', 'item_type_keyword' => 'Item Type Keyword', 'model_number' => 'Model Number',
            'model_name' => 'Model Name', 'manufacturer' => 'Manufacturer', 'item_condition' => 'Item Condition',
            'list_price' => 'List Price', 'merchant_release_date' => 'Release Date', 'maximum_order_quantity' => 'Max Order Qty',
            'offering_can_be_gift_messaged' => 'Gift Message', 'is_gift_wrap_available' => 'Gift Wrap',
            'import_designation' => 'Import Designation', 'your_price_usd' => 'Price USD',
            'product_description' => 'Product Description', 'bullet_point_1' => 'Bullet Point 1',
            'bullet_point_2' => 'Bullet Point 2', 'bullet_point_3' => 'Bullet Point 3', 'bullet_point_4' => 'Bullet Point 4',
            'bullet_point_5' => 'Bullet Point 5', 'generic_keywords' => 'Generic Keywords',
            'special_features_1' => 'Special Feature 1', 'special_features_2' => 'Special Feature 2',
            'special_features_3' => 'Special Feature 3', 'special_features_4' => 'Special Feature 4',
            'special_features_5' => 'Special Feature 5', 'style' => 'Style', 'department_name' => 'Department',
            'target_gender' => 'Target Gender', 'age_range_description' => 'Age Range', 'material_1' => 'Material 1',
            'material_2' => 'Material 2', 'material_3' => 'Material 3', 'number_of_items' => 'Number of Items',
            'color' => 'Color', 'color_map' => 'Color Map', 'size' => 'Size', 'size_map' => 'Size Map',
            'part_number' => 'Part Number', 'item_shape' => 'Item Shape', 'parentage_level' => 'Parentage',
            'relationship_type' => 'Relationship', 'parent_sku' => 'Parent SKU', 'variation_theme_name' => 'Variation Theme',
            'country_of_origin' => 'Country', 'warranty_description' => 'Warranty', 'are_batteries_required' => 'Batteries?',
            'dangerous_goods_regulations' => 'Dangerous Goods', 'mandatory_cautionary_statement' => 'Cautionary Statement',
            'main_image_url' => 'Main Image URL', 'other_image_url_1' => 'Other Image 1', 'other_image_url_2' => 'Other Image 2',
            'other_image_url_3' => 'Other Image 3', 'other_image_url_4' => 'Other Image 4', 'other_image_url_5' => 'Other Image 5',
            'other_image_url_6' => 'Other Image 6', 'other_image_url_7' => 'Other Image 7', 'other_image_url_8' => 'Other Image 8',
            'swatch_image_url' => 'Swatch Image URL'
        ];
    }
}

echo json_encode($response);