<?php
// app/usuarios/procesar_usuario.php
require_once __DIR__.'/../../db/config.php';
require_once __DIR__.'/../../db/CRUDUsuarios.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /ec_chans/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : 'crear';
    
    try {
        if ($action === 'crear') {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                throw new Exception('Las contraseñas no coinciden');
            }
            
            $datos = [
                'username' => $_POST['username'],
                'nombre' => $_POST['nombre'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'departamento' => $_POST['departamento'],
                'tipo' => $_POST['tipo'],
                'password' => $_POST['password']
            ];
            
            if (CRUDUsuarios::crear($datos)) {
                header('Location: main_usuarios.php?success=Usuario creado correctamente');
                exit;
            }
        }
        
        // Puedes agregar aquí el manejo para actualizar
        
    } catch (Exception $e) {
        header('Location: '.($_SERVER['HTTP_REFERER'] ?? 'main_usuarios.php').'?error='.urlencode($e->getMessage()));
        exit;
    }
}

header('Location: main_usuarios.php');
?>