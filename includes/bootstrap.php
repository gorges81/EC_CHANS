<?php
// /ec_chans/includes/bootstrap.php

// 1. Definimos una constante para la raíz del proyecto.
// Esto nos ayuda a tener rutas de archivo siempre correctas.
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// 2. Iniciamos la sesión de forma segura.
// Esto evita el error de "session is already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Cargamos la configuración de la base de datos.
// La ruta ahora es siempre correcta gracias a la constante PROJECT_ROOT.
require_once PROJECT_ROOT . '/db/config.php';