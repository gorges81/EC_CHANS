<?php
// /app/importaciones/procesar_nueva_importacion.php

// Manejador de errores
ini_set('display_errors', 1); // Habilitar la visualización de errores
error_reporting(E_ALL);     // Reportar todos los errores, advertencias y avisos

session_start();
header('Content-Type: application/json'); // Asegúrate de que esta línea esté, es CRÍTICA

require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../db/CRUDEventos.php';
require_once __DIR__ . '/../../db/CRUDImportaciones.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

// Depuración de acceso y sesión
if (!isset($_SESSION['usuario_id'])) {
    $response['message'] = 'Acceso no autorizado. Por favor, inicie sesión.';
    error_log("ERROR PROCESAR NUEVA IMPORTACION: Acceso no autorizado, usuario_id no en sesión.");
    echo json_encode($response);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['message'] = 'Método de solicitud incorrecto. Se espera POST.';
    error_log("ERROR PROCESAR NUEVA IMPORTACION: Método HTTP incorrecto: " . $_SERVER["REQUEST_METHOD"]);
    echo json_encode($response);
    exit();
}

// Depuración de la conexión a la base de datos
if (!isset($link) || $link->connect_error) {
    $response['message'] = "Error de conexión a la base de datos: " . $link->connect_error;
    error_log("ERROR PROCESAR NUEVA IMPORTACION: Fallo en la conexión a la base de datos: " . $link->connect_error);
    echo json_encode($response);
    exit();
}

$base_upload_dir = dirname(dirname(__DIR__)) . '/data/';
error_log("DEBUG PROCESAR NUEVA IMPORTACION: Directorio base de subida: " . $base_upload_dir);


try {
    // 1. Crear registro inicial
    $new_import_id = insertarImportacionInicial($link);
    if (!$new_import_id) {
        throw new Exception("No se pudo crear el registro inicial en la base de datos.");
    }
    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Registro inicial creado con ID: " . $new_import_id);


    // 2. Crear carpeta
    $correlativo_final = date('Ymd') . '_' . $new_import_id;
    $final_dir_path = $base_upload_dir . $correlativo_final . '/';
    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Ruta final del directorio: " . $final_dir_path);

    if (!is_dir($final_dir_path)) {
        // Depuración: Intentar crear el directorio
        if (!mkdir($final_dir_path, 0755, true)) {
            $last_error = error_get_last();
            error_log("ERROR PROCESAR NUEVA IMPORTACION: Fallo al crear directorio " . $final_dir_path . ". Mensaje: " . ($last_error['message'] ?? 'Desconocido'));
            throw new Exception("No se pudo crear el directorio de importación: " . ($last_error['message'] ?? 'Error desconocido al crear carpeta.'));
        }
        error_log("DEBUG PROCESAR NUEVA IMPORTACION: Directorio creado: " . $final_dir_path);
    } else {
        error_log("DEBUG PROCESAR NUEVA IMPORTACION: Directorio ya existe: " . $final_dir_path);
    }
    
    $update_columns = [];
    $event_description_parts = [];
    $files_uploaded_count = 0; // Contador de archivos subidos exitosamente

    // 3. Procesar archivos
    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Contenido de _FILES: " . print_r($_FILES, true)); // Depurar contenido de $_FILES

    if (empty($_FILES)) {
        throw new Exception("No se recibieron archivos para subir.");
    }
    
    $any_file_selected = false; // Flag para verificar si al menos un archivo fue seleccionado
    foreach ($_FILES as $form_field_name => $file) {
        // Asegurarse de que $file es un array válido y no un string vacío si el campo estaba vacío
        if (!is_array($file) || !isset($file['error'])) {
            error_log("ADVERTENCIA PROCESAR NUEVA IMPORTACION: Entrada _FILES no válida para " . $form_field_name);
            continue; // Saltar si la entrada no es un array de archivo
        }

        error_log("DEBUG PROCESAR NUEVA IMPORTACION: Procesando campo " . $form_field_name . " - Nombre: " . $file['name'] . " - Error: " . $file['error']);

        if ($file['error'] === UPLOAD_ERR_OK) {
            $any_file_selected = true; // Al menos un archivo válido ha sido subido
            $original_filename = basename($file['name']);
            $target_path = $final_dir_path . $original_filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $relative_path = $correlativo_final . '/' . $original_filename;
                $update_columns[$form_field_name] = $relative_path;
                $event_description_parts[] = "$form_field_name: $original_filename";
                $files_uploaded_count++;
                error_log("DEBUG PROCESAR NUEVA IMPORTACION: Archivo " . $original_filename . " movido exitosamente.");
            } else {
                $last_error = error_get_last();
                error_log("ERROR PROCESAR NUEVA IMPORTACION: Fallo al mover el archivo " . $original_filename . " a " . $target_path . ". Mensaje: " . ($last_error['message'] ?? 'Desconocido'));
                // No lanzar una excepción fatal aquí si es solo un archivo de varios
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            // Registrar otros errores de subida (ej. UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE)
            error_log("ERROR PROCESAR NUEVA IMPORTACION: Error de subida para " . ($file['name'] ?? 'N/A') . ": Código " . $file['error']);
            // Opcional: Puedes añadir un mensaje al usuario sobre este error específico del archivo
        }
    }
    
    // Validar que al menos un archivo haya sido subido exitosamente si es un requisito
    if ($files_uploaded_count === 0 && !$any_file_selected) { // Si no se seleccionó *ningún* archivo o ninguno se subió con éxito
        throw new Exception("No se seleccionaron o subieron archivos válidos. Por favor, adjunte al menos un archivo.");
    }


    // 4. Actualizar el registro con las rutas
    $update_columns['fecha_correlativa_display'] = $correlativo_final;
    if (!actualizarImportacion($new_import_id, $update_columns, $link)) {
        throw new Exception("No se pudieron guardar las rutas de los archivos en la base de datos.");
    }
    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Registro de importación actualizado en BD.");


    // 5. Registrar evento
    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Intentando registrar evento de importación.");
    // Asegurarse de que CRUDEventos.php se incluya si no lo está.
    // Aunque ya lo requerimos al principio, es un doble chequeo.
    if (!function_exists('registrarEvento')) {
        // Intentar incluir de nuevo si no está definida (caso muy raro)
        require_once __DIR__ . '/../../db/CRUDEventos.php';
        if (!function_exists('registrarEvento')) {
            error_log("FATAL ERROR PROCESAR NUEVA IMPORTACION: registrarEvento NO ESTÁ DEFINIDA incluso después de re-incluir CRUDEventos.php");
            throw new Exception("Error interno del sistema: la función de registro de eventos no está disponible.");
        }
    }
    
    $descripcion_evento = "Se ha registrado una nueva importación con ID: $new_import_id. Correlativo: $correlativo_final. Archivos subidos: $files_uploaded_count.";
    if (!empty($event_description_parts)) {
        $descripcion_evento .= " Archivos: " . implode(', ', $event_description_parts) . ".";
    }

    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; 
    
    $titulo_evento = 'Importación Completada'; // Título para el evento
    $tipo_evento = 'Importación'; // Tipo de evento para el log

    error_log("DEBUG PROCESAR NUEVA IMPORTACION: Llamando a registrarEvento con: Titulo='" . $titulo_evento . "', Tipo='" . $tipo_evento . "', Descripcion='" . $descripcion_evento . "', ID='" . $new_import_id . "', UsuarioID='" . $usuario_id . "'");

    $registroExitoso = registrarEvento($titulo_evento, $tipo_evento, $descripcion_evento, $new_import_id, $link);
    if ($registroExitoso) {
        error_log("DEBUG PROCESAR NUEVA IMPORTACION: Evento de importación registrado en la BD con éxito.");
    } else {
        error_log("ERROR PROCESAR NUEVA IMPORTACION: FALLO al registrar evento de importación.");
    }


    $response['success'] = true;
    $response['message'] = "¡Importación #{$new_import_id} guardada exitosamente!";

} catch (Exception $e) {
    // Si la transacción ha sido iniciada y hay un error, revertirla.
    if ($link && $link->in_transaction) {
        $link->rollback();
        error_log("DEBUG PROCESAR NUEVA IMPORTACION: Rollback de transacción debido a error.");
    }
    $response['message'] = "Error al procesar la importación: " . $e->getMessage();
    error_log("ERROR PROCESAR NUEVA IMPORTACION (Excepción): " . $e->getMessage() . " en línea " . $e->getLine() . " de " . $e->getFile());
}

echo json_encode($response);
exit();
?>