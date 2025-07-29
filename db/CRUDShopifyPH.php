<?php
// /db/CRUDShopifyPH.php

/**
 * Procesa el archivo CSV de Shopify PH aplicando el algoritmo priorizado.
 */
function procesarEInsertarShopifyPH($filePath, $import_data, $link) {
    // Paso 1 y 2: Leer y filtrar el archivo
    $all_rows = [];
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle);
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $all_rows[] = $data;
        }
        fclose($handle);
    } else {
        throw new Exception("No se pudo abrir el archivo CSV: " . $filePath);
    }

    $price_column_index = 22; // Columna W
    $filtered_rows = array_filter($all_rows, function($row) use ($price_column_index) {
        return isset($row[$price_column_index]) && trim($row[$price_column_index]) !== '';
    });

    if (empty($filtered_rows)) {
        throw new Exception("No se encontraron filas con precio válido para procesar.");
    }

    // Preparar la inserción
    $sql = "INSERT INTO `tbl_shopify_ph` 
                (`handle`, `title`, `body`, `vendor`, `tags`, `color`, `hand`, `option`, 
                 `variant_sku`, `variant_inventory_tracker`, `variant_price`, `variant_barcode`, `status`, 
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;

    // Procesar las filas filtradas
    foreach ($filtered_rows as $row) {
        // Mapeo de datos directos
        $data_map = [
            'handle' => $row[0] ?? null, 'title' => $row[1] ?? null,
            'body' => $row[2] ?? null, 'vendor' => $row[3] ?? null,
            'tags' => $row[6] ?? null, 'variant_inventory_tracker' => $row[19] ?? null,
            'variant_sku' => $row[17] ?? null, 'variant_price' => (float)($row[22] ?? 0),
            'variant_barcode' => $row[26] ?? null, 'status' => $row[50] ?? null,
        ];

        // --- INICIO DEL ALGORITMO PRIORIZADO ---
        $color = "";
        $hand = "";
        $option = "";

        // Extraer valores de las columnas
        $O1N = isset($row[8]) ? trim($row[8]) : '';    // Option1 Name (I)
        $O1V = isset($row[9]) ? trim($row[9]) : '';    // Option1 Value (J)
        $O2N = isset($row[11]) ? trim($row[11]) : '';   // Option2 Name (L)
        $O2V = isset($row[12]) ? trim($row[12]) : '';   // Option2 Value (M)

        // Prioridad 1: Nombres de opción combinados
        if (strtolower($O1N) == "color | hand orientation") {
            $option = $O1V;
        }
        // Prioridad 2: Valores combinados que describen todo
        else if (empty($O1N) && strpos($O1V, "Inside the Waistband") !== false) {
            $option = $O1V;
        }
        // Prioridad 3: Dos opciones explícitas (Color y Hand)
        else if (strtolower($O1N) == "color" && strtolower($O2N) == "hand") {
            $color = $O1V;
            $hand = $O2V;
        }
        // Prioridad 4: Dos opciones implícitas (Color y Hand por valor)
        else if (empty($O1N) && in_array($O1V, ["Black", "Brown", "Carbon Fiber"]) && empty($O2N) && in_array($O2V, ["Right", "Left"])) {
            $color = $O1V;
            $hand = $O2V;
        }
        // Prioridad 5: Opción única es "Title", "Style", "Size" o similar
        else if (in_array(strtolower($O1N), ["title", "style", "size"])) {
            $option = $O1V;
        }
        // Prioridad 6: Opción única es "Hand" o "Handling"
        else if (in_array(strtolower($O1N), ["hand", "handling", "hand orientation"])) {
            $hand = $O1V;
        }
        // Prioridad 7: Opción única es "Color"
        else if (strtolower($O1N) == "color") {
            $color = $O1V;
        }
        // Prioridad 8: El valor de la Opción 1 implica su tipo (casos genéricos)
        else if (empty($O1N)) {
            if (in_array($O1V, ["Right", "Left"])) {
                $hand = $O1V;
            } else if (in_array($O1V, ["Black", "Brown", "Carbon Fiber"])) {
                $color = $O1V;
            } else if (in_array($O1V, ["Small", "Medium", "Large", "Rotative", "Stationary"])) {
                $option = $O1V;
            } else if (strpos($O1V, "Single") !== false || strpos($O1V, "Double") !== false) {
                $option = $O1V;
            } else {
                // Si ninguna de las anteriores se cumple, 'option' podría tomar el valor por defecto
                $option = $O1V;
            }
        }
        // --- FIN DEL ALGORITMO ---

        // Insertar en la base de datos
        $stmt->bind_param(
            "ssssssssssdssss",
            $data_map['handle'], $data_map['title'], $data_map['body'], $data_map['vendor'], $data_map['tags'],
            $color, $hand, $option,
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

function vaciarTablaShopifyPH($link) {
    if ($link->query("TRUNCATE TABLE `tbl_shopify_ph`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionShopifyPH($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_shopify_ph ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosShopifyPHPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_shopify_ph WHERE import_correlativo = ?";
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

function obtenerDatosShopifyPHPorCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_shopify_ph WHERE import_correlativo = ?";
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