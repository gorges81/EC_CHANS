<?php
// /app/integrador/ebay/obtener_datos_grid_ebay.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDEbay.php';

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
    $datos = obtenerDatosEbayPorCorrelativo($correlativo, $link);
    
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
            'title' => 'Title',
            'sku' => 'SKU',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'sold_qty' => 'Sold Qty',
            'watchers' => 'Watchers',
            'ebay_cat1' => 'Category',
            'upc' => 'UPC',
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
