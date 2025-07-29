<?php
// /db/CRUDShopifyPPB.php

/**
 * Procesa el archivo CSV de Shopify PPB aplicando el algoritmo de negocio para el campo 'pack'.
 */
function procesarEInsertarShopifyPPB($filePath, $import_data, $link) {
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

    // Filtrar filas sin precio en la columna W (índice 22)
    $price_column_index = 22;
    $filtered_rows = array_filter($all_rows, function($row) use ($price_column_index) {
        return isset($row[$price_column_index]) && trim($row[$price_column_index]) !== '';
    });

    if (empty($filtered_rows)) {
        throw new Exception("No se encontraron filas con precio válido para procesar.");
    }

    // Preparar la inserción (con la columna 'pack' en lugar de 'option')
    $sql = "INSERT INTO `tbl_shopify_ppb` 
                (`handle`, `title`, `body`, `vendor`, `tags`, `color`, `Type`, `pack`, 
                 `variant_sku`, `variant_inventory_tracker`, `variant_price`, `variant_barcode`, `status`, 
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;
    $lista_colores = ["White", "Black", "Clear", "Olive", "Red", "Yellow", "Green", "Purple", "Blue", "Gold", "Silver"];

    // Procesar las filas filtradas
    foreach ($filtered_rows as $row) {
        // ---- A. Extracción de Datos Directos ----
        $data_map = [
            'handle' => $row[0] ?? null,
            'title' => $row[1] ?? null,
            'body' => $row[2] ?? null,
            'vendor' => $row[3] ?? null,
            'tags' => $row[6] ?? null,
            'variant_sku' => $row[17] ?? null,
            'variant_inventory_tracker' => $row[19] ?? null,
            'variant_price' => (float)($row[22] ?? 0),
            'variant_barcode' => $row[26] ?? null,
            'status' => $row[50] ?? null,
        ];

        // ---- B. Lógica de Campos Calculados ----
        
        // b.1) Calcular 'color' a partir del SKU
        $color = "";
        foreach ($lista_colores as $color_buscado) {
            if (stripos($data_map['variant_sku'], $color_buscado) !== false) {
                $color = $color_buscado;
                break;
            }
        }
        
        // b.2) Calcular 'Type' a partir del SKU
        $Type = "Standard"; // Valor por defecto
        if (stripos($data_map['variant_sku'], "Tky") !== false) {
            $Type = "Thank You";
        } elseif (stripos($data_map['variant_sku'], "car") !== false) {
            $Type = "Trash Car";
        } elseif (stripos($data_map['variant_sku'], "double") !== false) {
            $Type = "Double";
        } elseif (stripos($data_map['variant_sku'], "Bottle") !== false || stripos($data_map['variant_sku'], "Star") !== false || stripos($data_map['variant_sku'], "Cane") !== false) {
            $Type = "Special";
        }

        // b.3) Calcular 'pack' a partir del SKU
        $pack = "100 pack"; // Valor por defecto
        if (stripos($data_map['variant_sku'], "200") !== false) {
            $pack = "200 pack";
        } elseif (stripos($data_map['variant_sku'], "50") !== false) {
            $pack = "50 pack";
        } elseif (stripos($data_map['variant_sku'], "1k") !== false) {
            $pack = "1000 pack";
        }

        // Insertar en la base de datos
        $stmt->bind_param(
            "ssssssssssdssss",
            $data_map['handle'], $data_map['title'], $data_map['body'], $data_map['vendor'], $data_map['tags'],
            $color, $Type, $pack, // Se usa la nueva variable $pack
            $data_map['variant_sku'], $data_map['variant_inventory_tracker'], $data_map['variant_price'],
            $data_map['variant_barcode'], $data_map['status'],
            $import_data['fecha_correlativa_display'], $import_data['fecha_importacion']
        );

        if ($stmt->execute()) {
            $registros_insertados++;
        }
    }

    $stmt->close();
    return $registros_insertados;
}


// --- El resto de las funciones (vaciar, obtener datos, etc.) permanecen igual ---

function vaciarTablaShopifyPPB($link) {
    if ($link->query("TRUNCATE TABLE `tbl_shopify_ppb`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionShopifyPPB($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_shopify_ppb ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosShopifyPPBPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_shopify_ppb WHERE import_correlativo = ?";
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

function obtenerDatosShopifyPPBPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_shopify_ppb WHERE import_correlativo = ?";
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
