<?php
// /app/integrador/inv_shopify_ph/vaciar_inv_shopify_ph.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDInv_shopify_ph.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link->begin_transaction();
    try {
        if (!vaciarTablaInvShopifyPH($link)) {
            throw new Exception("Error al ejecutar el vaciado en la base de datos.");
        }
        
        registrarEvento("Vaciado Total de Tabla", "Eliminación", "Se eliminaron todos los registros de 'tbl_inv_shopify_ph' manualmente.", null, $link);
        
        $link->commit();
        $response['success'] = true;
        $response['message'] = 'Todos los datos de la tabla Inventario Shopify PH han sido eliminados.';

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
}