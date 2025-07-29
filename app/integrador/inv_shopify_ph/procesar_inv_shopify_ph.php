<?php
// /app/integrador/inv_shopify_ph/procesar_inv_shopify_ph.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDInv_shopify_ph.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false, 'message' => 'Solicitud no válida.'];

if (isset($_POST['importacion_id'])) {
    $importacion_id = intval($_POST['importacion_id']);
    
    try {
        $import_data = obtenerImportacionPorId($importacion_id, $link);
        if (!$import_data || empty($import_data['url_inv_shopify_ph'])) {
            throw new Exception("No se encontró un archivo 'Inv Shopify PH' para esta importación.");
        }
        
        $ruta_relativa = $import_data['url_inv_shopify_ph'];
        $ruta_absoluta = PROJECT_ROOT . '/' . (strpos($ruta_relativa, 'data/') === 0 ? $ruta_relativa : 'data/' . $ruta_relativa);
        $ruta_absoluta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ruta_absoluta);

        if (!file_exists($ruta_absoluta)) {
            throw new Exception("El archivo físico no existe: " . $ruta_absoluta);
        }

        $link->begin_transaction();
        
        vaciarTablaInvShopifyPH($link);
        
        $registros_procesados = procesarEInsertarInvShopifyPH($ruta_absoluta, $import_data, $link);
        
        if ($registros_procesados === 0) {
            throw new Exception("No se procesó ningún registro. Verifique el contenido del archivo.");
        }

        $description = "Se integraron $registros_procesados registros a 'tbl_inv_shopify_ph' desde la importación " . $import_data['fecha_correlativa_display'];
        registrarEvento("Integración de Inv Shopify PH", "Integración", $description, $importacion_id, $link);
        
        $link->commit();
        $response['success'] = true;
        $response['message'] = "$registros_procesados registros han sido migrados.";
        
    } catch (Exception $e) {
        if ($link->in_transaction) {
             $link->rollback();
        }
        $response['message'] = "Error: " . $e->getMessage();
        http_response_code(500);
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}