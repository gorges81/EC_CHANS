<?php
// /app/integrador/shopify_ppb/obtener_datos_grid_shopify_ppb.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDShopifyPPB.php';

function utf8_encode_array($array) {
    return array_map(function($item) {
        if (is_array($item)) {
            return utf8_encode_array($item);
        }
        if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
            return utf8_encode($item);
        }
        return $item;
    }, $array);
}

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false, 'data' => [], 'headers' => []];

if (isset($_POST['correlativo'])) {
    $correlativo = trim($_POST['correlativo']);
    $datos = obtenerDatosShopifyPPBPorCorrelativo($correlativo, $link);
    
    if ($datos !== null) {
        $response['success'] = true;
        
        $cleaned_data = array_map(function($row) {
            foreach ($row as $key => $value) {
                $row[$key] = is_string($value) ? str_replace(["\r", "\n"], ' ', $value) : $value;
            }
            return $row;
        }, $datos);
        
        $response['data'] = utf8_encode_array($cleaned_data);
        
        // Mapeo de encabezados actualizado
        $response['headers'] = [
            'handle' => 'Handle', 'title' => 'Title', 'vendor' => 'Vendor',
            'tags' => 'Tags', 'color' => 'Color', 'Type' => 'Type', 'pack' => 'Pack', // 'option' cambiado a 'pack'
            'variant_sku' => 'SKU', 'variant_price' => 'Price', 'status' => 'Status'
        ];
    } else {
        $response['message'] = "No se encontraron datos para el correlativo proporcionado.";
    }
} else {
    $response['message'] = "No se proporcionó un correlativo.";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
