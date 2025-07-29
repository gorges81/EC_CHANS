<?php
// /app/integrador/maestros/marketplaces_integraciones/procesar_generar_marcas.php

require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
require_once PROJECT_ROOT . '/db/CRUDMarcas.php';
require_once PROJECT_ROOT . '/db/CRUDEventos.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud no válida.'];

if (isset($_POST['integracion_id'])) {
    $id = intval($_POST['integracion_id']);
    
    try {
        $link->begin_transaction();

        // 1. Obtener datos de la integración
        $integracion = obtenerIntegracionPorId($id, $link);
        if (!$integracion) {
            throw new Exception("La integración no fue encontrada.");
        }

        // 2. Validar que la URL seleccionada sea 'url_category1'
        if ($integracion['url_select'] !== 'url_category1') {
            throw new Exception("Esta función solo está disponible para integraciones que usan 'url_category1'.");
        }

        // 3. Obtener la ruta del archivo
        $importacion = obtenerImportacionPorId($integracion['id_importacion'], $link);
        $ruta_relativa = $importacion[$integracion['url_select']];
        $ruta_absoluta = PROJECT_ROOT . '/data/' . $importacion['fecha_correlativa_display'] . '/' . basename($ruta_relativa);
        $ruta_absoluta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ruta_absoluta);

        if (!file_exists($ruta_absoluta)) {
            throw new Exception("El archivo físico no existe: " . $ruta_absoluta);
        }

        // 4. Leer el archivo y extraer marcas
        $marcas_encontradas = [];
        if (($handle = fopen($ruta_absoluta, "r")) !== FALSE) {
            $header = fgetcsv($handle, 0, "\t"); // Asumiendo que es un TSV (tab-separated)
            while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $brand_name = isset($data[6]) ? trim($data[6]) : ''; // Columna G (índice 6)
                if (!empty($brand_name) && strtolower($brand_name) !== 'brand name') {
                    $marcas_encontradas[] = $brand_name;
                }
            }
            fclose($handle);
        }

        $marcas_unicas = array_unique($marcas_encontradas);
        $marcas_nuevas = 0;

        // 5. Verificar y guardar marcas nuevas
        foreach ($marcas_unicas as $nombre_marca) {
            if (!marcaExiste($integracion['id_marketplace'], $nombre_marca, $link)) {
                $datos_marca = [
                    'id_marketplace' => $integracion['id_marketplace'],
                    'nombre' => $nombre_marca,
                    'siglas' => null,
                    'description' => 'Generado automáticamente desde integración.'
                ];
                crearMarca($datos_marca, $link);
                $marcas_nuevas++;
            }
        }

        $mensaje_final = "Proceso completado. Se encontraron " . count($marcas_unicas) . " marcas únicas. Se agregaron " . $marcas_nuevas . " marcas nuevas.";
        registrarEvento('Generación de Marcas', 'Maestro', $mensaje_final . " (Integración ID: {$id})", $id, $link);
        
        $link->commit();
        $response = ['success' => true, 'message' => $mensaje_final];

    } catch (Exception $e) {
        $link->rollback();
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);