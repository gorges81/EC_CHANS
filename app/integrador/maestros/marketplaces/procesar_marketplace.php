<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php';
// --- ARCHIVO AÑADIDO ---
// Se incluye el archivo necesario para poder registrar eventos.
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no válida.'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        $link->begin_transaction();

        if ($action === 'agregar') {
            crearMarketplace($_POST, $link);
            $marketplace_name = $_POST['marketplace'] ?? 'N/A';
            // --- LLAMADA A EVENTO AÑADIDA ---
            registrarEvento('Marketplace Creado', 'Maestro', "Se creó el marketplace: '{$marketplace_name}'", null, $link);
            $response = ['success' => true, 'message' => 'Marketplace agregado exitosamente.'];
        }
        elseif ($action === 'actualizar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $marketplace = obtenerMarketplacePorId($id, $link);
            $datos = $_POST;
            
            if (empty($datos['contrasena_marketplace'])) {
                $datos['contrasena_marketplace'] = $marketplace['contrasena_marketplace'];
            }
            actualizarMarketplace($id, $datos, $link);
            $marketplace_name = $_POST['marketplace'] ?? 'N/A';
            // --- LLAMADA A EVENTO AÑADIDA ---
            registrarEvento('Marketplace Actualizado', 'Maestro', "Se actualizó el marketplace: '{$marketplace_name}' (ID: {$id})", $id, $link);
            $response = ['success' => true, 'message' => 'Marketplace actualizado exitosamente.'];
        }
        elseif ($action === 'eliminar' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            // Obtener datos antes de eliminar para el log
            $marketplace = obtenerMarketplacePorId($id, $link);
            $marketplace_name = $marketplace['marketplace'] ?? 'N/A';

            eliminarMarketplace($id, $link);
            // --- LLAMADA A EVENTO AÑADIDA ---
            registrarEvento('Marketplace Eliminado', 'Maestro', "Se eliminó el marketplace: '{$marketplace_name}' (ID: {$id})", $id, $link);
            $response = ['success' => true, 'message' => 'Marketplace eliminado exitosamente.'];
        }

        $link->commit();

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
        http_response_code(500);
    }
}

echo json_encode($response);