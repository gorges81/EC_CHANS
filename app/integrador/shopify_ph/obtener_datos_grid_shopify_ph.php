<?php
// /app/integrador/shopify_ph/obtener_datos_grid_shopify_ph.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDShopifyPH.php';

// --- FUNCIÓN DE CORRECCIÓN DE CODIFICACIÓN ---
function utf8_encode_array($array) {
    return array_map(function($item) {
        if (is_array($item)) {
            return utf8_encode_array($item);
        }
        // Solo codifica si no es un UTF-8 válido
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
    $datos = obtenerDatosShopifyPHPorCorrelativo($correlativo, $link);
    
    if ($datos !== null) {
        $response['success'] = true;
        
        // Limpiar los datos de saltos de línea
        $cleaned_data = array_map(function($row) {
            foreach ($row as $key => $value) {
                $row[$key] = is_string($value) ? str_replace(["\r", "\n"], ' ', $value) : $value;
            }
            return $row;
        }, $datos);
        
        // Asegurar codificación UTF-8
        $response['data'] = utf8_encode_array($cleaned_data);
        
        $response['headers'] = [
            'handle' => 'Handle', 'title' => 'Title', 'vendor' => 'Vendor',
            'tags' => 'Tags', 'color' => 'Color', 'hand' => 'Hand', 'option' => 'Option',
            'variant_sku' => 'SKU', 'variant_price' => 'Price', 'status' => 'Status'
        ];
    } else {
        $response['message'] = "No se encontraron datos para el correlativo proporcionado.";
    }
} else {
    $response['message'] = "No se proporcionó un correlativo.";
}

// Forzar la codificación UTF-8 en la salida final
echo json_encode($response, JSON_UNESCAPED_UNICODE);
