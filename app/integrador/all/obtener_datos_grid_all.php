<?php
// /app/integrador/all/obtener_datos_grid_all.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDAll.php';

header('Content-Type: application/json');
$response = ['success' => false, 'data' => [], 'headers' => []];

if (isset($_POST['correlativo'])) {
    $correlativo = trim($_POST['correlativo']);
    $datos = obtenerDatosAllPorCorrelativo($correlativo, $link);
    
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
            'seller-sku' => 'SKU', 'asin1' => 'ASIN', 'item-name' => 'Item Name',
            'item-description' => 'Description', 'price' => 'Price', 'open-date' => 'Open Date',
            'product-id' => 'Product ID', 'fulfillment-channel' => 'Channel', 'status' => 'Status',
            'import_correlativo' => 'Correlativo', 'import_fecha' => 'Fecha Importación'
        ];
    }
}

echo json_encode($response);