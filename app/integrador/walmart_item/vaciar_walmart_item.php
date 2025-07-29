<?php
// /app/integrador/walmart_item/vaciar_walmart_item.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDwalmart_item.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link->begin_transaction();
    try {
        if (!vaciarTablaWalmartItem($link)) {
            throw new Exception("Error al ejecutar el vaciado en la base de datos.");
        }
        
        registrarEvento("Vaciado Total de Tabla", "Eliminación", "Se eliminaron todos los registros de 'tbl_walmart_item' manualmente.", null, $link);
        
        $link->commit();
        $response['success'] = true;
        $response['message'] = 'Todos los datos de la tabla Walmart Item han sido eliminados.';

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
}
