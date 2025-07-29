<?php
// /db/CRUDwalmart_item.php

/**
 * Procesa el archivo CSV de Walmart Item y lo inserta en la BD.
 * No aplica filtros, guarda todos los registros del archivo.
 */
function procesarEInsertarWalmartItem($filePath, $import_data, $link) {
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
    $sql = "INSERT INTO `tbl_walmart_item` 
                (`sku`, `product_name`, `product_category`, `price`, `buy_box_eligible`, `wpid`, `gtin`, `upc`, 
                 `item_url`, `shelf_name`, `primary_category_path`, `brand`, `variant_group_id`, 
                 `variant_group_attributes`, `variant_group_value`, `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;

    // Mapeo de Columnas: Nombre en BD => Índice en el archivo CSV (Base 0)
    $columnMapping = [
        'sku' => 0,                         // Columna A
        'product_name' => 2,                // Columna C
        'product_category' => 6,            // Columna G
        'price' => 7,                       // Columna H
        'buy_box_eligible' => 16,           // Columna Q
        'wpid' => 25,                       // Columna Z
        'gtin' => 26,                       // Columna AA
        'upc' => 27,                        // Columna AB
        'item_url' => 28,                   // Columna AC
        'shelf_name' => 30,                 // Columna AE
        'primary_category_path' => 31,      // Columna AF
        'brand' => 32,                      // Columna AG
        'variant_group_id' => 40,           // Columna AO
        'variant_group_attributes' => 42,   // Columna AQ
        'variant_group_value' => 43         // Columna AR
    ];

    // Procesar las filas
    foreach ($all_rows as $row) {
        $params = [];
        $types = '';
        foreach ($columnMapping as $db_col => $csv_index) {
            $value = isset($row[$csv_index]) ? trim($row[$csv_index]) : null;
            if ($db_col === 'price') {
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

function vaciarTablaWalmartItem($link) {
    if ($link->query("TRUNCATE TABLE `tbl_walmart_item`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionWalmartItem($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_walmart_item ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosWalmartItemPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_walmart_item WHERE import_correlativo = ?";
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

function obtenerDatosWalmartItemPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_walmart_item WHERE import_correlativo = ?";
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
