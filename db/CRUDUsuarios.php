<?php
// db/CRUDUsuarios.php

require_once 'config.php';

class CRUDUsuarios {
    public static function crear($datos) {
        global $link;
        
        $stmt = $link->prepare("INSERT INTO tbl_usuarios 
            (username, nombre, telefono, email, departamento, tipo, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        $stmt->bind_param(
            "sssssss",
            $datos['username'],
            $datos['nombre'],
            $datos['telefono'],
            $datos['email'],
            $datos['departamento'],
            $datos['tipo'],
            $password_hash
        );
        
        if ($stmt->execute()) {
            return mysqli_insert_id($link); // Retorna el ID del nuevo usuario
        } else {
            return false;
        }
    }

    public static function obtenerTodos() {
        global $link;
        $result = mysqli_query($link, "SELECT id, username, nombre, email, departamento, tipo, estatus FROM tbl_usuarios");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public static function obtenerPorId($id) {
        global $link;
        $stmt = $link->prepare("SELECT id, username, nombre, telefono, email, departamento, tipo, estatus 
                              FROM tbl_usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function actualizar($id, $datos) {
        global $link;
        $stmt = $link->prepare("UPDATE tbl_usuarios SET 
            nombre = ?, telefono = ?, email = ?, departamento = ?, tipo = ?, estatus = ?
            WHERE id = ?");
        
        $stmt->bind_param(
            "sssssii",
            $datos['nombre'],
            $datos['telefono'],
            $datos['email'],
            $datos['departamento'],
            $datos['tipo'],
            $datos['estatus'],
            $id
        );
        
        return $stmt->execute();
    }

    public static function eliminar($id) {
        global $link;
        $stmt = $link->prepare("DELETE FROM tbl_usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function verificarCredenciales($username, $password) {
        global $link;
        
        $stmt = $link->prepare("SELECT id, username, nombre, tipo, password_hash 
                              FROM tbl_usuarios 
                              WHERE username = ? AND estatus = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($usuario = $result->fetch_assoc()) {
            if (password_verify($password, $usuario['password_hash'])) {
                return $usuario;
            }
        }
        return false;
    }
}