<?php
// /db/CRUDEbay.php

/**
 * Procesa el archivo CSV de eBay y lo inserta en la BD.
 * No aplica filtros, guarda todos los registros del archivo.
 */
function procesarEInsertarEbay($filePath, $import_data, $link) {
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
    $sql = "INSERT INTO `tbl_ebay` 
                (`title`, `sku`, `quantity`, `price`, `sold_qty`, `watchers`, `ebay_cat1`, `upc`,
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;

    // Mapeo de Columnas: Nombre en BD => Índice en el archivo CSV (Base 0)
    $columnMapping = [
        'title'     => 1,  // Columna B
        'sku'       => 3,  // Columna D
        'quantity'  => 4,  // Columna E
        'price'     => 7,  // Columna H <-- CORREGIDO
        'sold_qty'  => 11, // Columna L
        'watchers'  => 12, // Columna M
        'ebay_cat1' => 16, // Columna Q
        'upc'       => 27  // Columna AB
    ];

    // Procesar las filas
    foreach ($all_rows as $row) {
        $params = [];
        $types = '';
        foreach ($columnMapping as $db_col => $csv_index) {
            $value = isset($row[$csv_index]) ? trim($row[$csv_index]) : null;
            if (in_array($db_col, ['quantity', 'sold_qty', 'watchers'])) {
                $params[] = (int)$value;
                $types .= 'i';
            } elseif ($db_col === 'price') {
                // Limpiar el valor del precio de símbolos de moneda o comas
                $clean_price = preg_replace('/[^0-9.]/', '', $value);
                $params[] = (float)$clean_price;
                $types .= 'd';
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

function vaciarTablaEbay($link) {
    if ($link->query("TRUNCATE TABLE `tbl_ebay`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionEbay($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_ebay ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosEbayPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_ebay WHERE import_correlativo = ?";
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

function obtenerDatosEbayPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_ebay WHERE import_correlativo = ?";
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
