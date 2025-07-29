<?php
// /app/integrador/maestros/marketplaces_integraciones/procesar_agregar_marcas.php

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

        $url_seleccionada = $integracion['url_select'];
        $columna_marca = -1;
        $delimitador = ","; // Por defecto para CSV

        // 2. Determinar la columna de la marca y el delimitador según la URL
        if ($url_seleccionada === 'url_category1' || $url_seleccionada === 'url_category2') {
            $columna_marca = 6; // Columna G
            $delimitador = "\t"; // Es un archivo TSV
        } elseif ($url_seleccionada === 'url_shopify_ph') {
            $columna_marca = 3; // Columna D
            $delimitador = ",";  // Es un archivo CSV
        } elseif ($url_seleccionada === 'url_ebay') {
            throw new Exception("El archivo de eBay no contiene información de marcas.");
        } else {
            throw new Exception("Esta función no está disponible para el tipo de archivo seleccionado ({$url_seleccionada}).");
        }

        // 3. Obtener la ruta del archivo
        $importacion = obtenerImportacionPorId($integracion['id_importacion'], $link);
        $ruta_relativa = $importacion[$url_seleccionada];
        $ruta_absoluta = PROJECT_ROOT . '/data/' . $importacion['fecha_correlativa_display'] . '/' . basename($ruta_relativa);
        $ruta_absoluta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ruta_absoluta);

        if (!file_exists($ruta_absoluta)) {
            throw new Exception("El archivo físico no existe: " . $ruta_absoluta);
        }

        // 4. Leer el archivo y extraer marcas
        $marcas_encontradas = [];
        if (($handle = fopen($ruta_absoluta, "r")) !== FALSE) {
            fgetcsv($handle, 0, $delimitador); // Ignorar encabezado
            while (($data = fgetcsv($handle, 0, $delimitador)) !== FALSE) {
                // Asegurarse de que la fila tenga suficientes columnas
                if (isset($data[$columna_marca])) {
                    $brand_name = trim($data[$columna_marca]);
                    if (!empty($brand_name) && strtolower($brand_name) !== 'brand name' && strtolower($brand_name) !== 'vendor') {
                        $marcas_encontradas[] = $brand_name;
                    }
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
        if ($link->in_transaction) {
            $link->rollback();
        }
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);