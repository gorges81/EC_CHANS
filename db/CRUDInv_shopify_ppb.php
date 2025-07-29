<?php
// /db/CRUDInv_shopify_ppb.php

/**
 * Procesa el archivo CSV de Inventario de Shopify PPB aplicando la lógica de negocio.
 */
function procesarEInsertarInvShopifyPPB($filePath, $import_data, $link) {
    // 1. Leer el archivo completo
    $all_rows = [];
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle); // Ignorar encabezados
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $all_rows[] = $data;
        }
        fclose($handle);
    } else {
        throw new Exception("No se pudo abrir el archivo CSV: " . $filePath);
    }

    // 2. Filtrar por Location y Disponibilidad
    $locations_to_process = ['5602 NW 105th CT', 'Amazon MCF'];
    $rows_to_process = [];
    foreach ($all_rows as $row) {
        $location = isset($row[11]) ? trim($row[11]) : ''; // Columna L
        $available = isset($row[15]) ? strtolower(trim($row[15])) : ''; // Columna P
        
        if (in_array($location, $locations_to_process) && $available != 'not stocked') {
            $rows_to_process[] = $row;
        }
    }

    if (empty($rows_to_process)) {
        throw new Exception("No se encontraron filas válidas para procesar (verifique locations y disponibilidad).");
    }

    // 3. Preparar la inserción
    $sql = "INSERT INTO `tbl_inv_shopify_ppb` 
                (`handle`, `title`, `color`, `Type`, `pack`, `sku`, `location`, `available`,
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;
    $lista_colores = ["White", "Black", "Clear", "Olive", "Red", "Yellow", "Green", "Purple", "Blue", "Gold", "Silver"];

    // 4. Procesar las filas filtradas
    foreach ($rows_to_process as $row) {
        // ---- Extracción de Datos Directos ----
        $data_map = [
            'handle'    => $row[0] ?? null,
            'title'     => $row[1] ?? null,
            'sku'       => $row[8] ?? null,
            'location'  => $row[11] ?? null,
            'available' => $row[15] ?? null,
        ];

        // ---- Lógica de Campos Calculados (basada en shopify_ppb) ----
        $color = "";
        foreach ($lista_colores as $color_buscado) {
            if (stripos($data_map['sku'], $color_buscado) !== false) {
                $color = $color_buscado;
                break;
            }
        }
        
        $Type = "Standard";
        if (stripos($data_map['sku'], "Tky") !== false) {
            $Type = "Thank You";
        } elseif (stripos($data_map['sku'], "car") !== false) {
            $Type = "Trash Car";
        } elseif (stripos($data_map['sku'], "double") !== false) {
            $Type = "Double";
        } elseif (stripos($data_map['sku'], "Bottle") !== false || stripos($data_map['sku'], "Star") !== false || stripos($data_map['sku'], "Cane") !== false) {
            $Type = "Special";
        }

        $pack = "100 pack";
        if (stripos($data_map['sku'], "200") !== false) {
            $pack = "200 pack";
        } elseif (stripos($data_map['sku'], "50") !== false) {
            $pack = "50 pack";
        } elseif (stripos($data_map['sku'], "1k") !== false) {
            $pack = "1000 pack";
        }
        
        $available_int = (int)$data_map['available'];

        // --- LÍNEA CORREGIDA ---
        // Se cambió el tipo de dato para 'location' de 'i' (integer) a 's' (string).
        // El orden de los tipos ahora es "ssssss s i ss" para que coincida con las variables.
        $stmt->bind_param(
            "sssssssiss",
            $data_map['handle'], $data_map['title'],
            $color, $Type, $pack,
            $data_map['sku'], $data_map['location'], $available_int,
            $import_data['fecha_correlativa_display'], $import_data['fecha_importacion']
        );

        if ($stmt->execute()) {
            $registros_insertados++;
        }
    }

    $stmt->close();
    return $registros_insertados;
}

function vaciarTablaInvShopifyPPB($link) {
    if ($link->query("TRUNCATE TABLE `tbl_inv_shopify_ppb`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionInvShopifyPPB($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_inv_shopify_ppb ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosInvShopifyPPBPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_inv_shopify_ppb WHERE import_correlativo = ?";
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

function obtenerDatosInvShopifyPPBPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_inv_shopify_ppb WHERE import_correlativo = ?";
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