<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php'; // Necesario para obtener el nombre del archivo
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no válida.'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    try {
        $link->begin_transaction();
        if ($action === 'agregar') {
            crearIntegracion($_POST, $link);
            registrarEvento('Integración Creada', 'Maestro', "Se creó una nueva integración de marketplace.", null, $link);
            $response = ['success' => true, 'message' => 'Integración agregada exitosamente.'];
        }
        elseif ($action === 'actualizar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            actualizarIntegracion($id, $_POST, $link);
            registrarEvento('Integración Actualizada', 'Maestro', "Se actualizó la integración ID: {$id}", $id, $link);
            $response = ['success' => true, 'message' => 'Integración actualizada exitosamente.'];
        }
        elseif ($action === 'eliminar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            eliminarIntegracion($id, $link);
            registrarEvento('Integración Eliminada', 'Maestro', "Se eliminó la integración ID: {$id}", $id, $link);
            $response = ['success' => true, 'message' => 'Integración eliminada exitosamente.'];
        }
        $link->commit();
    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
    }
}
echo json_encode($response);