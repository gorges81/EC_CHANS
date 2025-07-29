<?php
// /app/integrador/category/procesar_category.php

set_time_limit(300);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDIntegrador.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if (isset($_POST['importacion_id'])) {
    $importacion_id = intval($_POST['importacion_id']);
    
    try {
        $link->begin_transaction();
        
        $import_data = obtenerImportacionPorId($importacion_id, $link);
        if (!$import_data || empty($import_data['url_category1'])) {
            throw new Exception("No se encontró un archivo de categoría para esta importación.");
        }
        
        $ruta_relativa = $import_data['url_category1'];
        $file_extension = strtolower(pathinfo($ruta_relativa, PATHINFO_EXTENSION));
        if ($file_extension !== 'txt') {
            throw new Exception("No se puede procesar porque no coincide el tipo de archivo. Se esperaba un .txt.");
        }

        $ruta_absoluta = PROJECT_ROOT . '/data/' . $ruta_relativa;
        if (!file_exists($ruta_absoluta)) {
            throw new Exception("El archivo físico no existe en el servidor.");
        }

        if (!vaciarTablaCategory($link)) {
            throw new Exception("No se pudo vaciar la tabla de categorías existente.");
        }
        registrarEvento("Vaciado de Tabla", "Eliminación", "Se eliminaron todos los registros de 'tbl_category' por nueva integración.", null, $link);

        $rows = [];
        if (($handle = fopen($ruta_absoluta, "r")) !== FALSE) {
            fgetcsv($handle);
            while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        } else {
            throw new Exception("No se pudo abrir el archivo de texto.");
        }
        
        if (empty($rows)) {
            throw new Exception("El archivo de importación está vacío o tiene un formato incorrecto.");
        }
        
        $registros_procesados = insertarRegistrosCategory($rows, $import_data, $link);
        
        $description = "Se integraron $registros_procesados registros a 'tbl_category' desde la importación " . $import_data['fecha_correlativa_display'];
        registrarEvento("Integración de Categorías (TSV)", "Integración", $description, $importacion_id, $link);
        
        $link->commit();

        $response['success'] = true;
        $response['message'] = "$registros_procesados registros han sido migrados exitosamente.";
        $response['data'] = [
            'fecha' => date("Y-m-d"),
            'hora' => date("H:i:s"),
            'correlativo_usado' => $import_data['fecha_correlativa_display'],
            'registros_procesados' => $registros_procesados
        ];
        
    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
}