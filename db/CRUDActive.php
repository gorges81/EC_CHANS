<?php
// /db/CRUDActive.php

function insertarRegistrosActive($rows, $import_data, $link) {
    // MAPEO FINAL Y CORREGIDO
    // Letra de Columna -> Índice (Base 0)
    $columnMapping = [
        'item_name'            => 0,  // A
        'item_description'     => 1,  // B
        'seller_sku'           => 3,  // D
        'price'                => 4,  // E
        'asin_1'               => 16, // Q
        'product_id'           => 22, // W
        'fullfillment_channel' => 26, // AA
        'business_price'       => 27, // AB
    ];

    $db_columns = array_keys($columnMapping);
    $db_columns[] = 'import_correlativo';
    $db_columns[] = 'import_fecha';

    $placeholders = implode(', ', array_fill(0, count($db_columns), '?'));
    $sql = "INSERT INTO tbl_active (" . implode(', ', array_map(fn($c) => "`$c`", $db_columns)) . ") VALUES ($placeholders)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta para tbl_active: " . $link->error);
    }

    $registros_procesados = 0;
    foreach ($rows as $row) {
        $params = [];
        $types = '';
        foreach ($columnMapping as $db_col => $txt_index) {
            $value = isset($row[$txt_index]) ? trim($row[$txt_index]) : null;
            // Tratar los precios como números decimales
            if ($db_col === 'price' || $db_col === 'business_price') {
                $params[] = (float) $value;
                $types .= 'd';
            } else {
                $params[] = $value;
                $types .= 's';
            }
        }
        
        $params[] = $import_data['fecha_correlativa_display'];
        $params[] = $import_data['fecha_importacion'];
        $types .= 'ss';
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $registros_procesados++;
    }
    
    $stmt->close();
    return $registros_procesados;
}

function vaciarTablaActive($link) {
    if ($link->query("TRUNCATE TABLE `tbl_active`")) return true;
    error_log("Error al vaciar la tabla tbl_active: " . $link->error);
    return false;
}

function obtenerDatosUltimaIntegracionActive($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_active ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) {
        return $result->fetch_assoc();
    }
    return null;
}

function contarRegistrosActivePorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_active WHERE import_correlativo = ?";
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

function obtenerDatosActivePorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_active WHERE import_correlativo = ?";
    $stmt = $link->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correlativo);
        $stmt->execute();
        $result = $stmt->get_result();
        $datos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $datos;
    }
    return null;
}