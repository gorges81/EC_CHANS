<?php
require_once __DIR__ . '/../../../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDMarketplace.php';

header('Content-Type: application/json; charset=utf-8');

$marketplaces = obtenerTodosLosMarketplaces($link);
$data = [];

foreach ($marketplaces as $mp) {
    $accion = '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' . $mp['id'] . '">Eliminar</button>';
    $data[] = [
        'id' => $mp['id'],
        'marketplace' => htmlspecialchars($mp['marketplace']),
        'description' => htmlspecialchars($mp['description']),
        'url' => htmlspecialchars($mp['url']),
        'usuario_marketplace' => htmlspecialchars($mp['usuario_marketplace']),
        'contrasena_marketplace' => '********', // No mostrar la contraseña
        'accion' => $accion
    ];
}

echo json_encode(['data' => $data]);