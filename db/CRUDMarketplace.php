<?php
// /db/CRUDMarketplace.php

function crearMarketplace($datos, $link) {
    $sql = "INSERT INTO tbl_marketplaces (marketplace, description, url, usuario_marketplace, contrasena_marketplace) VALUES (?, ?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("sssss", $datos['marketplace'], $datos['description'], $datos['url'], $datos['usuario_marketplace'], $datos['contrasena_marketplace']);
    return $stmt->execute();
}

function obtenerTodosLosMarketplaces($link) {
    $sql = "SELECT * FROM tbl_marketplaces ORDER BY marketplace ASC";
    $result = $link->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obtenerMarketplacePorId($id, $link) {
    $sql = "SELECT * FROM tbl_marketplaces WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function actualizarMarketplace($id, $datos, $link) {
    $sql = "UPDATE tbl_marketplaces SET marketplace = ?, description = ?, url = ?, usuario_marketplace = ?, contrasena_marketplace = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("sssssi", $datos['marketplace'], $datos['description'], $datos['url'], $datos['usuario_marketplace'], $datos['contrasena_marketplace'], $id);
    return $stmt->execute();
}

function eliminarMarketplace($id, $link) {
    $sql = "DELETE FROM tbl_marketplaces WHERE id = ?";
    $stmt = $link->prepare($sql);
    if ($stmt === false) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}