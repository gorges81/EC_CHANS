<?php
// /app/importaciones/main_importaciones.php

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once PROJECT_ROOT . '/db/CRUDImportaciones.php';
include_once PROJECT_ROOT . '/includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /ec_chans/index.php?error=2");
    exit();
}

$importaciones = obtenerTodasLasImportaciones($link);
?>
<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="text-dark-blue mb-4">Módulo de Importaciones</h2>

    <div class="card p-3 shadow-sm">
        <div class="mb-3">
            <a href="agregar_importaciones.php" class="btn btn-warning">
                <i class="fas fa-plus-circle me-2"></i>Agregar
            </a>
        </div>
        
        <div id="alertPlaceholder"></div>
        <div class="table-responsive">
            <table id="importacionesTable" class="table table-striped table-hover datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Correlativo</th>
                        <th>Fecha de Importación</th>
                        <th class="text-center">Nro. de Archivos</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($importaciones)): ?>
                        <?php foreach ($importaciones as $importacion): ?>
                            <tr data-href="modificar_importacion.php?id=<?php echo $importacion['id']; ?>" style="cursor: pointer;">
                                <td class="align-middle"><?php echo htmlspecialchars($importacion['id']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($importacion['fecha_correlativa_display']); ?></td>
                                <td class="align-middle"><?php echo date("Y-m-d H:i:s", strtotime($importacion['fecha_importacion'])); ?></td>
                                <td class="align-middle text-center">
                                    <?php
                                    $file_count = 0;
                                    $file_list_tooltip = 'La carpeta no existe o está vacía.';
                                    $directory_path = PROJECT_ROOT . '/data/' . $importacion['fecha_correlativa_display'];
                                    if (!empty($importacion['fecha_correlativa_display']) && is_dir($directory_path)) {
                                        $files = array_diff(scandir($directory_path), ['.', '..']);
                                        $file_count = count($files);
                                        if ($file_count > 0) {
                                            $escaped_files = array_map('htmlspecialchars', $files);
                                            $file_list_tooltip = implode("<br>", $escaped_files);
                                        } else { $file_list_tooltip = 'La carpeta está vacía.'; }
                                    }
                                    ?>
                                    <span class="badge bg-primary fs-6" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<?php echo $file_list_tooltip; ?>">
                                        <?php echo $file_count; ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="#" class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-id="<?php echo htmlspecialchars($importacion['id']); ?>" data-correlativo="<?php echo htmlspecialchars($importacion['fecha_correlativa_display']); ?>" data-file-count="<?php echo $file_count; ?>">
                                       <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once PROJECT_ROOT . '/includes/footer.php'; ?>
<script src="/ec_chans/js/script_importaciones.js"></script>
<script src="/ec_chans/js/script_importaciones_links.js"></script>