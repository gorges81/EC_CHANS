<?php
// /db/CRUDMarcas.php

function crearMarca($datos, $link) {
    $sql = "INSERT INTO tbl_marcas (id_marketplace, siglas, nombre, description) VALUES (?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("isss", $datos['id_marketplace'], $datos['siglas'], $datos['nombre'], $datos['description']);
    return $stmt->execute();
}

// --- NUEVA FUNCIÓN AÑADIDA ---
/**
 * Verifica si una marca ya existe para un marketplace específico (insensible a mayúsculas).
 */
function marcaExiste($id_marketplace, $nombre_marca, $link) {
    $sql = "SELECT id FROM tbl_marcas WHERE id_marketplace = ? AND LOWER(nombre) = LOWER(?)";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false; // Devuelve false en caso de error para no bloquear
    $lower_nombre_marca = strtolower($nombre_marca);
    $stmt->bind_param("is", $id_marketplace, $lower_nombre_marca);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();
    return $num_rows > 0;
}

function obtenerTodasLasMarcas($link) {
    $sql = "SELECT m.id, m.siglas, m.nombre, mk.marketplace 
            FROM tbl_marcas m
            LEFT JOIN tbl_marketplaces mk ON m.id_marketplace = mk.id
            ORDER BY m.nombre ASC";
    $result = $link->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obtenerMarcaPorId($id, $link) {
    $sql = "SELECT * FROM tbl_marcas WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function actualizarMarca($id, $datos, $link) {
    $sql = "UPDATE tbl_marcas SET id_marketplace = ?, siglas = ?, nombre = ?, description = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("isssi", $datos['id_marketplace'], $datos['siglas'], $datos['nombre'], $datos['description'], $id);
    return $stmt->execute();
}

function eliminarMarca($id, $link) {
    $sql = "DELETE FROM tbl_marcas WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}