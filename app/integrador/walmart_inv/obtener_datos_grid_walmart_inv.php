<?php
// /app/integrador/walmart_inv/obtener_datos_grid_walmart_inv.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
// Nombre del archivo CRUD corregido
require_once PROJECT_ROOT . '/db/CRUDWalmart_inv.php';

function utf8_encode_array($array) {
    return array_map(function($item) {
        if (is_array($item)) return utf8_encode_array($item);
        if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) return utf8_encode($item);
        return $item;
    }, $array);
}

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false, 'data' => [], 'headers' => []];

if (isset($_POST['correlativo'])) {
    $correlativo = trim($_POST['correlativo']);
    $datos = obtenerDatosWalmartInvPorCorrelativo($correlativo, $link);
    
    if ($datos !== null) {
        $response['success'] = true;
        
        $cleaned_data = array_map(function($row) {
            foreach ($row as $key => $value) {
                $row[$key] = is_string($value) ? str_replace(["\r", "\n"], ' ', $value) : $value;
            }
            return $row;
        }, $datos);
        
        $response['data'] = utf8_encode_array($cleaned_data);
        
        // Mapeo de encabezados actualizado para mostrar TODAS las columnas
        $response['headers'] = [
            'id' => 'ID',
            'sku' => 'SKU', 
            'product_name' => 'Product Name', 
            'input_qty' => 'Input Qty', 
            'sell_qty' => 'Sell Qty',
            'import_correlativo' => 'Correlativo',
            'import_fecha' => 'Fecha Importación',
            'created_at' => 'Fecha Creación'
        ];
    } else {
        $response['message'] = "No se encontraron datos para el correlativo proporcionado.";
    }
} else {
    $response['message'] = "No se proporcionó un correlativo.";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
