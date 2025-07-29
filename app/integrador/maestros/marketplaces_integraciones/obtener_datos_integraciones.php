<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplaceIntegraciones.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';

header('Content-Type: application/json; charset=utf-8');

$integraciones = obtenerTodasLasIntegraciones($link);
$data = [];
foreach ($integraciones as $int) {
    $id = $int['id'];
    $accion = '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $id . '">Eliminar</button>';
    
    // Botón "Agregar" para Marcas
    $marcas_btn = '<button class="btn btn-success btn-sm btn-agregar-marcas" data-id="' . $id . '">Agregar</button>';

    // Botón "Agregar" para Productos
    $productos_btn = '<button class="btn btn-success btn-sm btn-agregar-productos" data-id="' . $id . '">Agregar</button>';

    $data[] = [
        'id' => $id,
        'marketplace' => htmlspecialchars($int['marketplace']),
        'url_select' => htmlspecialchars($int['url_select']),
        'archivo' => htmlspecialchars($int['archivo']),
        'accion' => $accion,
        'marcas' => $marcas_btn,
        'productos' => $productos_btn
    ];
}
echo json_encode(['data' => $data]);