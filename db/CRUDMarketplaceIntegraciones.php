<?php
// /db/CRUDMarketplaceIntegraciones.php

function crearIntegracion($datos, $link) {
    // Obtener el nombre del archivo antes de insertar
    $importacion = obtenerImportacionPorId($datos['id_importacion'], $link);
    $nombre_archivo = isset($importacion[$datos['url_select']]) ? basename($importacion[$datos['url_select']]) : null;

    $sql = "INSERT INTO tbl_marketplaces_integraciones (id_marketplace, id_importacion, url_select, archivo, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("iisss", $datos['id_marketplace'], $datos['id_importacion'], $datos['url_select'], $nombre_archivo, $datos['description']);
    return $stmt->execute();
}

function obtenerTodasLasIntegraciones($link) {
    $sql = "SELECT mi.id, m.marketplace, mi.url_select, mi.archivo
            FROM tbl_marketplaces_integraciones mi
            LEFT JOIN tbl_marketplaces m ON mi.id_marketplace = m.id
            ORDER BY m.marketplace ASC";
    $result = $link->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obtenerIntegracionPorId($id, $link) {
    $sql = "SELECT * FROM tbl_marketplaces_integraciones WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function actualizarIntegracion($id, $datos, $link) {
    // Obtener el nombre del archivo antes de actualizar
    $importacion = obtenerImportacionPorId($datos['id_importacion'], $link);
    $nombre_archivo = isset($importacion[$datos['url_select']]) ? basename($importacion[$datos['url_select']]) : null;

    $sql = "UPDATE tbl_marketplaces_integraciones SET id_marketplace = ?, id_importacion = ?, url_select = ?, archivo = ?, description = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("iisssi", $datos['id_marketplace'], $datos['id_importacion'], $datos['url_select'], $nombre_archivo, $datos['description'], $id);
    return $stmt->execute();
}

function eliminarIntegracion($id, $link) {
    $sql = "DELETE FROM tbl_marketplaces_integraciones WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function obtenerUrlsDeImportacion($id_importacion, $link) {
    $importacion = obtenerImportacionPorId($id_importacion, $link);
    $urls = [];
    if ($importacion) {
        foreach ($importacion as $key => $value) {
            if (strpos($key, 'url_') === 0 && !empty($value)) {
                // Devolvemos la clave (nombre de la columna) y el valor (nombre del archivo)
                $urls[] = ['key' => $key, 'value' => basename($value)];
            }
        }
    }
    return $urls;
}