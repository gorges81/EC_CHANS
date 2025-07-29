<?php
// /db/CRUDIntegrador.php

function obtenerCorrelativosDeImportaciones($link) {
    $correlativos = [];
    $sql = "SELECT id, fecha_correlativa_display, fecha_importacion FROM tbl_importaciones WHERE fecha_correlativa_display != 'PENDIENTE' ORDER BY fecha_importacion DESC";
    if ($result = $link->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $correlativos[] = $row;
        }
        $result->free();
    }
    return $correlativos;
}

function vaciarTablaCategory($link) {
    $sql = "TRUNCATE TABLE `tbl_category`";
    return $link->query($sql);
}

function obtenerDatosUltimaIntegracion($link) {
    $sql = "SELECT import_correlativo, import_fecha FROM tbl_category ORDER BY id DESC LIMIT 1";
    if ($result = $link->query($sql)) {
        return $result->fetch_assoc();
    }
    return null;
}

function contarRegistrosPorCorrelativo($correlativo, $link) {
    $sql = "SELECT COUNT(id) as total FROM tbl_category WHERE import_correlativo = ?";
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

function obtenerDatosCategoryPorCorrelativo($correlativo, $link) {
    $datos = [];
    $sql = "SELECT * FROM tbl_category WHERE import_correlativo = ?";
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

function columnIndexFromString($pString) {
    $pString = strtoupper($pString);
    $a=0; for($i=0; $i<strlen($pString); $i++) { $a = $a*26 + ord($pString[$i]) - 64; }
    return $a-1;
}

function insertarRegistrosCategory($rows, $import_data, $link) {
    $columnMapping = [
        'status' => 0, 'title' => 1, 'sku' => 2, 'product_type' => 4, 'item_name' => 5, 'brand_name' => 6,
        'product_id_type' => 7, 'product_id' => 8, 'item_type_keyword' => 9, 'model_number' => 13,
        'model_name' => 14, 'manufacturer' => 15, 'item_condition' => 33, 'list_price' => 35,
        'merchant_release_date' => 37, 'maximum_order_quantity' => 39, 'offering_can_be_gift_messaged' => 40,
        'is_gift_wrap_available' => 41, 'import_designation' => 48, 'your_price_usd' => 65,
        'product_description' => 93, 'bullet_point_1' => 94, 'bullet_point_2' => 95,
        'bullet_point_3' => 96, 'bullet_point_4' => 97, 'bullet_point_5' => 98, 'generic_keywords' => 99,
        'special_features_1' => 100, 'special_features_2' => 101, 'special_features_3' => 102,
        'special_features_4' => 103, 'special_features_5' => 104, 'style' => 105, 'department_name' => 106,
        'target_gender' => 107, 'age_range_description' => 108, 'material_1' => 109, 'material_2' => 110,
        'material_3' => 111, 'number_of_items' => 112, 'color' => 115, 'color_map' => 182, 'size' => 116,
        'size_map' => 116, 'part_number' => 119, 'item_shape' => 120, 'parentage_level' => 286,
        'relationship_type' => 287, 'parent_sku' => 288, 'variation_theme_name' => 289,
        'country_of_origin' => 290, 'warranty_description' => 291, 'are_batteries_required' => 293,
        'dangerous_goods_regulations' => 308, 'mandatory_cautionary_statement' => 338,
        'main_image_url' => 253, 'other_image_url_1' => 254, 'other_image_url_2' => 255,
        'other_image_url_3' => 256, 'other_image_url_4' => 257, 'other_image_url_5' => 258,
        'other_image_url_6' => 259, 'other_image_url_7' => 260, 'other_image_url_8' => 261,
        'swatch_image_url' => 262
    ];

    $db_columns = array_keys($columnMapping);
    $db_columns[] = 'import_correlativo';
    $db_columns[] = 'import_fecha';
    
    $placeholders = implode(', ', array_fill(0, count($db_columns), '?'));
    $sql = "INSERT INTO tbl_category (" . implode(', ', array_map(fn($c) => "`$c`", $db_columns)) . ") VALUES ($placeholders)";
    $stmt = $link->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta de inserción: " . $link->error);
    }
    
    $types = str_repeat('s', count($db_columns));
    $registros_procesados = 0;
    foreach ($rows as $row) {
        $params = [];
        foreach ($columnMapping as $db_col => $index) {
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