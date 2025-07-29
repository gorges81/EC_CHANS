<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
// --- LÍNEA CORREGIDA ---
// Se incluye el archivo necesario para poder buscar los datos de la importación.
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json');
$response = ['success' => false, 'urls' => []];

if (isset($_POST['id_importacion'])) {
    $id = intval($_POST['id_importacion']);
    $urls = obtenerUrlsDeImportacion($id, $link);
    $response = ['success' => true, 'urls' => $urls];
}

echo json_encode($response);