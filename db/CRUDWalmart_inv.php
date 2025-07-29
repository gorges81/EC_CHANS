<?php
// /db/CRUDwalmart_inv.php

/**
 * Procesa el archivo CSV de Walmart Inventory y lo inserta en la BD.
 * No aplica filtros, guarda todos los registros del archivo.
 */
function procesarEInsertarWalmartInv($filePath, $import_data, $link) {
    // Leer el archivo
    $all_rows = [];
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle); // Ignorar la primera fila de encabezados
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $all_rows[] = $data;
        }
        fclose($handle);
    } else {
        throw new Exception("No se pudo abrir el archivo CSV: " . $filePath);
    }

    if (empty($all_rows)) {
        throw new Exception("El archivo de importación está vacío.");
    }

    // Preparar la inserción
    $sql = "INSERT INTO `tbl_walmart_inv` 
                (`sku`, `product_name`, `input_qty`, `sell_qty`,
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;

    // Mapeo de Columnas: Nombre en BD => Índice en el archivo CSV (Base 0)
    $columnMapping = [
        'sku'          => 0,  // Columna A
        'product_name' => 2,  // Columna C
        'input_qty'    => 5,  // Columna F
        'sell_qty'     => 6   // Columna G
    ];

    // Procesar las filas
    foreach ($all_rows as $row) {
        $params = [];
        $types = '';
        foreach ($columnMapping as $db_col => $csv_index) {
            $value = isset($row[$csv_index]) ? trim($row[$csv_index]) : null;
            if (in_array($db_col, ['input_qty', 'sell_qty'])) {
                $params[] = (int)$value;
                $types .= 'i';
            } else {
                $params[] = $value;
                $types .= 's';
            }
        }
        
        // Añadir datos de importación
        $params[] = $import_data['fecha_correlativa_display'];
        $params[] = $import_data['fecha_importacion'];
        $types .= 'ss';

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $registros_insertados++;
        }
    }

    $stmt->close();
    return $registros_insertados;
}

function vaciarTablaWalmartInv($link) {
    if ($link->query("TRUNCATE TABLE `tbl_walmart_inv`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionWalmartInv($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_walmart_inv ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosWalmartInvPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_walmart_inv WHERE import_correlativo = ?";
    $stmt = $link->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correlativo);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $fila['total'] ?? 0;
    }
    return 0;
}

function obtenerDatosWalmartInvPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_walmart_inv WHERE import_correlativo = ?";
    $stmt = $link->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correlativo);
        $stmt->execute();
        $datos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $datos;
    }
    return null;
}
