<?php
// db/CRUDEventos.php

// Asegúrate de que config.php se cargue solo una vez.
// Esto es importante si este archivo se incluye directamente o en otro contexto.
if (!isset($link)) { // Solo si $link no ha sido definido globalmente
    require_once __DIR__ . '/config.php';
}

/**
 * Registra un nuevo evento en la base de datos.
 */
function registrarEvento($titulo, $tipo_evento, $descripcion, $referencia_id, $link) {
    error_log("DEBUG REGISTRAR EVENTO: Intentando registrar: " . $titulo . " (" . $tipo_evento . ") para ref_id: " . $referencia_id);
    $sql = "INSERT INTO tbl_eventos (titulo, tipo_evento, descripcion, referencia_id, fecha_evento) VALUES (?, ?, ?, ?, NOW())";
    
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("sssi", $titulo, $tipo_evento, $descripcion, $referencia_id);
        
        if ($stmt->execute()) {
            error_log("DEBUG REGISTRAR EVENTO: Evento registrado exitosamente. ID: " . $stmt->insert_id);
            $stmt->close();
            return true;
        } else {
            error_log("ERROR REGISTRAR EVENTO: Error al ejecutar inserción de evento: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } else {
        error_log("ERROR REGISTRAR EVENTO: Error al preparar la consulta para registrar evento: " . $link->error);
        return false;
    }
}

/**
 * Obtiene todos los eventos o los filtra por un término de búsqueda.
 */
function obtenerTodosLosLosEventos($search_term = '') {
    global $link;
    $eventos = [];
    $query = "SELECT id, fecha_evento, titulo, descripcion, tipo_evento, referencia_id FROM tbl_eventos";
    $params = [];
    $types = '';

    if (!empty($search_term)) {
        $query .= " WHERE (titulo LIKE ? OR descripcion LIKE ? OR tipo_evento LIKE ?)";
        $like_term = "%" . $search_term . "%";
        $params = [$like_term, $like_term, $like_term];
        $types = 'sss';
    }

    $query .= " ORDER BY fecha_evento DESC";

    if ($stmt = $link->prepare($query)) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Error al obtener eventos: " . $link->error);
    }
    return $eventos;
}

?>