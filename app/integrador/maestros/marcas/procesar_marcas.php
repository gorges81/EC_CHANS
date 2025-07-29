<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarcas.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no válida.'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    try {
        $link->begin_transaction();
        if ($action === 'agregar') {
            crearMarca($_POST, $link);
            registrarEvento('Marca Creada', 'Maestro', "Se creó la marca: '{$_POST['nombre']}'", null, $link);
            $response = ['success' => true, 'message' => 'Marca agregada exitosamente.'];
        }
        elseif ($action === 'actualizar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            actualizarMarca($id, $_POST, $link);
            registrarEvento('Marca Actualizada', 'Maestro', "Se actualizó la marca: '{$_POST['nombre']}' (ID: {$id})", $id, $link);
            $response = ['success' => true, 'message' => 'Marca actualizada exitosamente.'];
        }
        elseif ($action === 'eliminar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $marca = obtenerMarcaPorId($id, $link); // Para el log
            eliminarMarca($id, $link);
            registrarEvento('Marca Eliminada', 'Maestro', "Se eliminó la marca: '{$marca['nombre']}' (ID: {$id})", $id, $link);
            $response = ['success' => true, 'message' => 'Marca eliminada exitosamente.'];
        }
        $link->commit();
    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
    }
}
echo json_encode($response);