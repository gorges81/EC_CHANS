<?php
// db/config.php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Usualmente 'root' para XAMPP
define('DB_PASSWORD', '');     // Usualmente vacío para XAMPP
define('DB_NAME', 'db_ec_chans');

// Intentar conectar a la base de datos MySQL
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar conexión
if($link === false){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}
?>