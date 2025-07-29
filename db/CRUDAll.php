<?php
// /db/CRUDAll.php

function insertarRegistrosAll($rows, $import_data, $link) {
    // MAPA: Columna de la BD => Índice de la columna en el archivo (empezando en 0)
    $columnMapping = [
        'seller-sku' => 0,          // Columna A
        'asin1' => 1,               // Columna B
        'item-name' => 2,           // Columna C
        'item-description' => 3,    // Columna D
        'price' => 5,               // Columna F
        'open-date' => 7,           // Columna H
        'product-id' => 13,         // Columna N
        'fulfillment-channel' => 15,// Columna P
        'status' => 16              // Columna Q
    ];

    $db_columns = array_keys($columnMapping);
    $db_columns[] = 'import_correlativo';
    $db_columns[] = 'import_fecha';

    $placeholders = implode(', ', array_fill(0, count($db_columns), '?'));
    $sql = "INSERT INTO tbl_all (" . implode(', ', array_map(fn($c) => "`$c`", $db_columns)) . ") VALUES ($placeholders)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta para tbl_all: " . $link->error);
    }
    
    $types = str_repeat('s', count($db_columns));
    $registros_procesados = 0;
    foreach ($rows as $row) {
        $params = [];
        foreach ($columnMapping as $index) {
            $params[] = $row[$index] ?? null;
        }
        $params[] = $import_data['fecha_correlativa_display'];
        $params[] = $import_data['fecha_importacion'];
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $registros_procesados++;
    }
    
    $stmt->close();
    return $registros_procesados;
}

function vaciarTablaAll($link) {
    if ($link->query("TRUNCATE TABLE `tbl_all`")) return true;
    error_log("Error al vaciar la tabla tbl_all: " . $link->error);
    return false;
}

function obtenerDatosUltimaIntegracionAll($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_all ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosAllPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_all WHERE import_correlativo = ?";
    $stmt = $link->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correlativo);
        $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $stmt->close();
        return $fila['total'] ?? 0;
    }
    return 0;
}

function obtenerDatosAllPorCorrelativo($correlativo, $link) {
    $datos = [];
    $sql = "SELECT * FROM tbl_all WHERE import_correlativo = ?";
    $stmt = $link->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correlativo);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }
        $stmt->close();
    }
    return $datos;
}