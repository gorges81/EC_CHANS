<?php
// /app/integrador/active/vaciar_active.php

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDActive.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link->begin_transaction();
    try {
        if (!vaciarTablaActive($link)) {
            throw new Exception("Error al vaciar la tabla 'tbl_active'.");
        }
        
        registrarEvento("Vaciado Total de Tabla", "Eliminación", "Se eliminaron todos los registros de la tabla 'tbl_active' manualmente.", null, $link);
        
        $link->commit();
        $response['success'] = true;
        $response['message'] = 'Todos los datos de la tabla Active han sido eliminados.';

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
}