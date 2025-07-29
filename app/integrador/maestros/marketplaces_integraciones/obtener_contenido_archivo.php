<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json');
$response = ['success' => false, 'content' => 'Error: Parámetros inválidos.'];

if (isset($_POST['id_importacion']) && isset($_POST['url_select'])) {
    $id_importacion = intval($_POST['id_importacion']);
    $url_select = $_POST['url_select'];

    $content = obtenerContenidoArchivo($id_importacion, $url_select, $link);
    $response = ['success' => true, 'content' => htmlspecialchars($content)];
}

echo json_encode($response);