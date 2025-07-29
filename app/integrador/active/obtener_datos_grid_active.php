<?php
// /app/integrador/active/obtener_datos_grid_active.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDActive.php';

header('Content-Type: application/json');
$response = ['success' => false, 'data' => [], 'headers' => []];

if (isset($_POST['correlativo'])) {
    $correlativo = trim($_POST['correlativo']);
    $datos = obtenerDatosActivePorCorrelativo($correlativo, $link);
    
    if ($datos !== null) {
        $response['success'] = true;
        
        $cleaned_data = array_map(function($row) {
            foreach ($row as $key => $value) {
                $row[$key] = str_replace(["\r", "\n"], ' ', $value);
            }
            return $row;
        }, $datos);
        
        $response['data'] = $cleaned_data;
        
        // Mapeo de encabezados para la tabla
        $response['headers'] = [
            'item_name' => 'Item Name', 'item_description' => 'Description', 'seller_sku' => 'SKU',
            'price' => 'Price', 'asin_1' => 'ASIN', 'product_id' => 'Product ID',
            'fullfillment_channel' => 'Channel', 'business_price' => 'Business Price',
            'import_correlativo' => 'Correlativo', 'import_fecha' => 'Fecha Importación'
        ];
    }
}

echo json_encode($response);