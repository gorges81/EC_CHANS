<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarcas.php';

header('Content-Type: application/json; charset=utf-8');

$marcas = obtenerTodasLasMarcas($link);
$data = [];
foreach ($marcas as $marca) {
    $accion = '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $marca['id'] . '">Eliminar</button>';
    $data[] = [
        'id' => $marca['id'],
        'siglas' => htmlspecialchars($marca['siglas']),
        'nombre' => htmlspecialchars($marca['nombre']),
        'marketplace' => htmlspecialchars($marca['marketplace']),
        'accion' => $accion
    ];
}
echo json_encode(['data' => $data]);