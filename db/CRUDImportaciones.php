<?php
// /db/CRUDImportaciones.php

require_once __DIR__ . '/CRUDEventos.php';
// Función global para obtener correlativos de importaciones
if (!function_exists('obtenerCorrelativosDeImportaciones')) {
    /**
     * Obtiene los correlativos de las importaciones.
     * @param mysqli $link La conexión a la base de datos.
     * @return array Un array con los correlativos (id, fecha_correlativa_display, fecha_importacion).
     */
    function obtenerCorrelativosDeImportaciones($link) {
        $correlativos = [];
        // Puedes filtrar aquí si solo quieres importaciones que tengan archivos de Shopify PH,
        // por ejemplo, WHERE url_shopify_ph IS NOT NULL AND url_shopify_ph != ''
        $sql = "SELECT id, fecha_correlativa_display, fecha_importacion FROM tbl_importaciones ORDER BY fecha_importacion DESC";
        if ($result = $link->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $correlativos[] = $row;
            }
            $result->free();
        } else {
            error_log("ERROR CRUDImportaciones: Fallo al obtener correlativos de importaciones: " . $link->error);
        }
        return $correlativos;
    }
/**
 * Obtiene los correlativos de las importaciones que tienen un archivo en una columna específica.
 * @param mysqli $link La conexión a la base de datos.
 * @param string $nombreColumna El nombre de la columna URL a verificar (ej: 'url_shopify_ph').
 * @return array Un array con los correlativos que cumplen la condición.
 */
function obtenerCorrelativosConArchivo($link, $nombreColumna) {
    // Validar que el nombre de la columna sea seguro para evitar inyección SQL
    // Se asume que los nombres de columna son controlados internamente y no por el usuario.
    $correlativos = [];
    $sql = "SELECT id, fecha_correlativa_display, fecha_importacion 
            FROM tbl_importaciones 
            WHERE `$nombreColumna` IS NOT NULL AND `$nombreColumna` != '' 
            ORDER BY fecha_importacion DESC";
            
    if ($result = $link->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $correlativos[] = $row;
        }
        $result->free();
    } else {
        error_log("ERROR CRUDImportaciones: Fallo al obtener correlativos con archivo: " . $link->error);
    }
    return $correlativos;
}

}
/**
 * Obtiene todas las importaciones de la base de datos.
 */
function obtenerTodasLasImportaciones($link) {
    $importaciones = [];
    $sql = "SELECT * FROM tbl_importaciones ORDER BY fecha_importacion DESC";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $importaciones[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener importaciones: " . mysqli_error($link));
    }
    return $importaciones;
}

/**
 * Obtiene una importación por su ID.
 */
function obtenerImportacionPorId($id, $link) {
    $id = intval($id);
    $query = "SELECT * FROM tbl_importaciones WHERE id = $id";
    $result = mysqli_query($link, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $importacion = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $importacion;
    }
    return null;
}

/**
 * Inserta un registro de importación inicial.
 */
function insertarImportacionInicial($link) {
    $sql = "INSERT INTO tbl_importaciones (fecha_importacion) VALUES (NOW())";
    if ($link->query($sql)) {
        return $link->insert_id;
    }
    return false;
}

/**
 * Actualiza un registro de importación con las rutas de archivo y el correlativo.
 */
function actualizarImportacion($id, $update_data, $link) {
    $sql_set_parts = [];
    $db_values = [];
    $types = '';

    foreach ($update_data as $column => $value) {
        $sql_set_parts[] = "`$column` = ?";
        $db_values[] = $value;
        $types .= 's';
    }
    
    if (empty($sql_set_parts)) return false;

    $db_values[] = $id;
    $types .= 'i';

    $sql = "UPDATE tbl_importaciones SET " . implode(', ', $sql_set_parts) . " WHERE id = ?";
    $stmt = $link->prepare($sql);
    
    if ($stmt === false) return false;
    
    $stmt->bind_param($types, ...$db_values);
    return $stmt->execute();
}

/**
 * Elimina una importación y su carpeta asociada (VERSIÓN CORREGIDA).
 */
function eliminarImportacion($id, $link) {
    $link->begin_transaction();
    try {
        // 1. Obtener datos de la importación
        $importacion_data = obtenerImportacionPorId($id, $link);
        if (!$importacion_data) {
            throw new Exception("Importación no encontrada.");
        }

        // 2. Eliminar la carpeta de archivos si existe
        $folder_name = $importacion_data['fecha_correlativa_display'];
        if (!empty($folder_name)) {
            $folder_path = PROJECT_ROOT . '/data/' . $folder_name;
            
            if (is_dir($folder_path)) {
                // Verificación de error crucial
                if (!deleteDirectoryAndContents($folder_path)) {
                    throw new Exception("Fallo al eliminar la carpeta física. Verifica los permisos del directorio '/data'.");
                }
            }
        }
        
        // 3. Eliminar eventos relacionados
        $stmt_events = $link->prepare("DELETE FROM tbl_eventos WHERE referencia_id = ? AND tipo_evento LIKE 'Importación%'");
        $stmt_events->bind_param("i", $id);
        $stmt_events->execute();
        $stmt_events->close();
        
        // 4. Registrar el evento de eliminación ANTES de borrar el registro principal
        $log_desc = "Se eliminó la importación #{$id} con correlativo '{$importacion_data['fecha_correlativa_display']}'.";
        registrarEvento('Importación Eliminada', 'Eliminación', $log_desc, $id, $link);

        // 5. Eliminar el registro de importación
        $stmt_import = $link->prepare("DELETE FROM tbl_importaciones WHERE id = ?");
        $stmt_import->bind_param("i", $id);
        $stmt_import->execute();
        $stmt_import->close();
        
        $link->commit();
        return true;

    } catch (Exception $e) {
        $link->rollback();
        error_log("Error en eliminarImportacion: " . $e->getMessage());
        return false;
    }
}

/**
 * Función auxiliar para eliminar un directorio y su contenido.
 */
function deleteDirectoryAndContents($dir) {
    if (!is_dir($dir)) return true;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deleteDirectoryAndContents("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}