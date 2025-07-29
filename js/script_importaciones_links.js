// /js/script_importaciones_links.js
// Este script se encarga EXCLUSIVAMENTE de hacer las filas de la tabla de importaciones clickeables.

document.addEventListener('DOMContentLoaded', function() {
    // Asegurarnos de que jQuery esté cargado antes de continuar
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado. DataTables y este script no funcionarán.');
        return;
    }

    const importacionesTable = document.getElementById('importacionesTable');
    if (importacionesTable) {
        
        // --- LÓGICA DEFINITIVA PARA HACER LAS FILAS CLICKEABLES ---
        // Se usa el método recomendado por DataTables para manejar eventos en filas dinámicas (al paginar, buscar, etc.)
        $('#importacionesTable tbody').on('click', 'tr', function(e) {
            // Se ignora el clic si se presionó el botón de eliminar o sus hijos (como el ícono <i>)
            if (e.target.closest('.btn-eliminar')) {
                return;
            }
            
            // Se obtiene la URL del atributo data-href de la fila y se navega a esa página
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        });
    }
});