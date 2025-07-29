<?php
// /db/CRUDInv_shopify_ph.php

/**
 * Procesa el archivo CSV de Inventario de Shopify PH aplicando el algoritmo de filtrado por location.
 */
function procesarEInsertarInvShopifyPH($filePath, $import_data, $link) {
    // 1. Leer el archivo completo en memoria
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

    // 2. Filtrar el archivo por los dos locations y disponibilidad
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

    // Preparar la inserción en la base de datos
    $sql = "INSERT INTO `tbl_inv_shopify_ph` 
                (`handle`, `title`, `color`, `hand`, `option`, `sku`, `available`, `location`,
                 `import_correlativo`, `import_fecha`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $link->error);
    }

    $registros_insertados = 0;
    $handle_to_parent_map = [];

    // 3. Procesar las filas filtradas con el algoritmo de opciones
    foreach ($rows_to_process as $row) {
        $current_handle = $row[0] ?? null;
        if (!$current_handle) continue;

        // ---- Extracción de Datos Directos ----
        $data_map = [
            'handle'    => $current_handle,         // Columna A
            'title'     => $row[1] ?? null,          // Columna B
            'sku'       => $row[8] ?? null,          // Columna I
            'available' => $row[15] ?? null,         // Columna P
            'location'  => $row[11] ?? null,         // Columna L
        ];

        // --- Lógica de Padre/Hijo para Opciones ---
        $option1_name_idx = 2; // Columna C
        if ((isset($row[$option1_name_idx]) && trim($row[$option1_name_idx]) !== '') || !isset($handle_to_parent_map[$current_handle])) {
            $handle_to_parent_map[$current_handle] = $row;
        }
        $parent_row = $handle_to_parent_map[$current_handle];

        // --- Aplicación del Algoritmo Priorizado ---
        $color = "";
        $hand = "";
        $option = "";

        $O1N = isset($parent_row[2]) ? trim($parent_row[2]) : ''; // Option1 Name (C)
        $O1V = isset($row[3]) ? trim($row[3]) : '';       // Option1 Value (D)
        $O2N = isset($parent_row[4]) ? trim($parent_row[4]) : ''; // Option2 Name (E)
        $O2V = isset($row[5]) ? trim($row[5]) : '';       // Option2 Value (F)

        if (strtolower($O1N) == "color | hand orientation") {
            $option = $O1V;
        } else if (empty($O1N) && strpos($O1V, "Inside the Waistband") !== false) {
            $option = $O1V;
        } else if (strtolower($O1N) == "color" && strtolower($O2N) == "hand") {
            $color = $O1V;
            $hand = $O2V;
        } else if (empty($O1N) && in_array($O1V, ["Black", "Brown", "Carbon Fiber"]) && empty($O2N) && in_array($O2V, ["Right", "Left"])) {
            $color = $O1V;
            $hand = $O2V;
        } else if (in_array(strtolower($O1N), ["title", "style", "size"])) {
            $option = $O1V;
        } else if (in_array(strtolower($O1N), ["hand", "handling", "hand orientation"])) {
            $hand = $O1V;
        } else if (strtolower($O1N) == "color") {
            $color = $O1V;
        } else if (empty($O1N)) {
            if (in_array($O1V, ["Right", "Left"])) {
                $hand = $O1V;
            } else if (in_array($O1V, ["Black", "Brown", "Carbon Fiber"])) {
                $color = $O1V;
            } else if (in_array($O1V, ["Small", "Medium", "Large", "Rotative", "Stationary"])) {
                $option = $O1V;
            } else if (strpos($O1V, "Single") !== false || strpos($O1V, "Double") !== false) {
                $option = $O1V;
            } else {
                $option = $O1V;
            }
        }
        
        $available_int = (int)$data_map['available'];

        // Insertar en la base de datos
        $stmt->bind_param(
            "ssssssisss",
            $data_map['handle'], $data_map['title'],
            $color, $hand, $option,
            $data_map['sku'], $available_int, $data_map['location'],
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

function vaciarTablaInvShopifyPH($link) {
    if ($link->query("TRUNCATE TABLE `tbl_inv_shopify_ph`")) return true;
    return false;
}

function obtenerDatosUltimaIntegracionInvShopifyPH($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_inv_shopify_ph ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) return $result->fetch_assoc();
    return null;
}

function contarRegistrosInvShopifyPHporCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_inv_shopify_ph WHERE import_correlativo = ?";
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

function obtenerDatosInvShopifyPHporCorrelativo($correlativo, $link) {
    $sql = "SELECT * FROM tbl_inv_shopify_ph WHERE import_correlativo = ?";
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