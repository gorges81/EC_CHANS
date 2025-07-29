<?php
// /db/CRUDAnalisis.php

// Incluir el archivo de configuración de la base de datos si no ha sido incluido ya
// Asegúrate de que $link esté disponible. bootstrap.php ya lo hace.
if (!isset($link)) {
    require_once __DIR__ . '/config.php';
}

class CRUDAnalisis { // AHORA ES UNA CLASE
    /**
     * Obtiene un resumen general de las importaciones.
     * @param mysqli $link La conexión a la base de datos.
     * @return array Un array asociativo con el resumen (ej. total_importaciones, total_archivos, ultima_importacion_fecha).
     */
    public static function obtenerResumenImportaciones($link) { // MÉTODO ESTÁTICO
        $resumen = [
            'total_importaciones' => 0,
            'total_archivos' => 0,
            'ultima_importacion_fecha' => null
        ];

        // Consulta para el total de importaciones
        $sqlTotalImportaciones = "SELECT COUNT(id) AS total FROM tbl_importaciones";
        if ($result = $link->query($sqlTotalImportaciones)) {
            $row = $result->fetch_assoc();
            $resumen['total_importaciones'] = $row['total'];
            $result->free();
        } else {
            error_log("Error al obtener total de importaciones: " . $link->error);
        }

        // Consulta para la última fecha de importación
        $sqlUltimaFecha = "SELECT MAX(fecha_importacion) AS ultima_fecha FROM tbl_importaciones";
        if ($result = $link->query($sqlUltimaFecha)) {
            $row = $result->fetch_assoc();
            $resumen['ultima_importacion_fecha'] = $row['ultima_fecha'];
            $result->free();
        } else {
            error_log("Error al obtener última fecha de importación: " . $link->error);
        }

        $resumen['total_archivos'] = self::calcularTotalArchivosImportados($link); // Llamada a un método estático
        // Ojo: `self::` para llamar a otros métodos estáticos dentro de la misma clase.

        return $resumen;
    }

    /**
     * Función auxiliar para calcular el total de archivos.
     * Esto es un ejemplo y podría ser costoso si hay muchos directorios/archivos.
     * Idealmente, se guardaría el conteo en la DB al importar.
     */
    public static function calcularTotalArchivosImportados($link) { // MÉTODO ESTÁTICO
        $total = 0;
        // Asegúrate de que PROJECT_ROOT esté definido, o calcula la ruta base
        if (!defined('PROJECT_ROOT')) {
            error_log("ADVERTENCIA: PROJECT_ROOT no definido al calcularTotalArchivosImportados. Usando ruta relativa desde db/.");
            $base_data_path = dirname(__DIR__, 2) . '/data/'; // Ir dos niveles arriba desde db/
        } else {
            $base_data_path = PROJECT_ROOT . '/data/';
        }

        if (is_dir($base_data_path)) {
            $import_folders = array_diff(scandir($base_data_path), ['.', '..']);
            foreach ($import_folders as $folder) {
                $folder_path = $base_data_path . $folder;
                if (is_dir($folder_path)) {
                    $files = array_diff(scandir($folder_path), ['.', '..']);
                    $total += count($files);
                }
            }
        }
        return $total;
    }

    // Puedes añadir más métodos estáticos aquí para obtener datos para gráficos específicos,
    // por ejemplo: obtenerImportacionesPorMes($link), obtenerArchivosPorTipo($link), etc.
}
?>