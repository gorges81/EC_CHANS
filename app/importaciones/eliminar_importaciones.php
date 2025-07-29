<?php
// ... (Bloque de manejador de errores) ...

// --- BLOQUE DE ARRANQUE CORREGIDO ---
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json');
// ... (El resto del archivo sigue igual) ...

$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $importacion_id = intval($_POST['id']);
    
    if (eliminarImportacion($importacion_id, $link)) {
        $response['success'] = true;
        $response['message'] = 'Importación #' . $importacion_id . ' y sus archivos han sido eliminados.';
    } else {
        $response['message'] = 'Error: No se pudo eliminar la importación.';
    }
}

echo json_encode($response);