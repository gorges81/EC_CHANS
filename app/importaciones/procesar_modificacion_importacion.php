<?php
// /app/importaciones/procesar_modificacion_importacion.php

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importacion_id'])) {
    $importacion_id = intval($_POST['importacion_id']);
    
    $link->begin_transaction();
    try {
        // 1. Obtener el estado actual de la importación
        $current_import = obtenerImportacionPorId($importacion_id, $link);
        if (!$current_import) {
            throw new Exception("Registro de importación no encontrado.");
        }
        $directory_path = PROJECT_ROOT . '/data/' . $current_import['fecha_correlativa_display'] . '/';

        $update_columns = [];
        $event_changes = [];

        // 2. Procesar los archivos subidos
        foreach ($_FILES as $form_field_name => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Si se sube un archivo nuevo para este campo
                $old_file_path_relative = $current_import[$form_field_name];
                $old_file_path_absolute = PROJECT_ROOT . '/data/' . $old_file_path_relative;

                // 2.1 Borrar el archivo viejo si existía
                if (!empty($old_file_path_relative) && file_exists($old_file_path_absolute)) {
                    unlink($old_file_path_absolute);
                }

                // 2.2 Guardar el archivo nuevo
                $new_filename = basename($file['name']);
                $new_target_path = $directory_path . $new_filename;
                move_uploaded_file($file['tmp_name'], $new_target_path);

                // 2.3 Preparar datos para la BD y el log de eventos
                $new_relative_path = $current_import['fecha_correlativa_display'] . '/' . $new_filename;
                $update_columns[$form_field_name] = $new_relative_path;
                $event_changes[] = "Archivo '$form_field_name' cambiado a '$new_filename'";
            }
        }
        
        // 3. Actualizar la base de datos si hubo cambios
        if (!empty($update_columns)) {
            if (!actualizarImportacion($importacion_id, $update_columns, $link)) {
                throw new Exception("Error al actualizar la base de datos.");
            }

            // 4. Registrar el evento de modificación
            $description = "Se modificó la importación #$importacion_id. Cambios: " . implode(', ', $event_changes) . ".";
            registrarEvento('Importación Modificada', 'Modificación', $description, $importacion_id, $link);
        }

        $link->commit();
        $response['success'] = true;
        $response['message'] = '¡Importación actualizada exitosamente!';

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
}