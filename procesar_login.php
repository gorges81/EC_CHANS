<?php
// procesar_login.php

// Inicia la sesión de PHP. Es crucial llamarla al principio de cualquier script que use sesiones.
session_start();

// Incluye el archivo de configuración de la base de datos y la clase CRUDUsuarios.
// Las rutas son relativas desde la raíz del proyecto.
require_once __DIR__ . '/db/config.php';
require_once __DIR__ . '/db/CRUDUsuarios.php';

// Verifica si la solicitud es de tipo POST. El formulario de login envía datos por POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene el nombre de usuario y la contraseña enviados desde el formulario.
    // trim() se usa para eliminar espacios en blanco al inicio y al final.
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Verifica que ambos campos no estén vacíos.
    if (empty($username) || empty($password)) {
        // Redirige de vuelta al index.php con un mensaje de error si los campos están vacíos.
        header('Location: index.php?error=' . urlencode('Por favor, ingrese usuario y contraseña.'));
        exit;
    }

    // Llama a la función estática 'verificarCredenciales' de la clase CRUDUsuarios.
    // Esta función se encarga de consultar la base de datos y verificar la contraseña hasheada.
    $usuario = CRUDUsuarios::verificarCredenciales($username, $password);

    if ($usuario) {
        // Si las credenciales son válidas, guarda la información esencial del usuario en la sesión.
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['tipo'] = $usuario['tipo']; // Guarda el tipo de usuario (admin, editor, lector)

        // Redirige al usuario al dashboard.php.
        header('Location: dashboard.php');
        exit;
    } else {
        // Si las credenciales no son válidas, redirige de vuelta al index.php con un mensaje de error.
        header('Location: index.php?error=' . urlencode('Usuario o contraseña incorrectos.'));
        exit;
    }
} else {
    // Si se intenta acceder a este archivo directamente sin una solicitud POST,
    // redirige al index.php para evitar accesos no deseados.
    header('Location: index.php');
    exit;
}
?>