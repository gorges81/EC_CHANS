// /ec_chans/js/script.js

document.addEventListener('DOMContentLoaded', function () {
    // --- 1. LÓGICA PARA MARCAR EL ENLACE ACTIVO EN EL MENÚ LATERAL ---
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('#sidebar-wrapper .list-group-item');

    sidebarLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath && linkPath !== '#' && currentPath.includes(linkPath)) {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });

    // --- 2. LÓGICA PARA ABRIR EL SUBMENÚ SI UN HIJO ESTÁ ACTIVO ---
    const activeSubmenuLink = document.querySelector('#sidebar-wrapper .collapse .list-group-item.active');
    if (activeSubmenuLink) {
        const collapseElement = activeSubmenuLink.closest('.collapse');
        if (collapseElement) {
            collapseElement.classList.add('show');
            const parentTrigger = document.querySelector(`a[data-bs-target="#${collapseElement.id}"]`);
            if (parentTrigger) {
                parentTrigger.classList.add('active');
            }
        }
    }
    
    // --- 3. INICIALIZACIÓN GLOBAL DE TOOLTIPS DE BOOTSTRAP ---
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

});

// --- 4. INICIALIZACIÓN GLOBAL DE DATATABLES ---
$(document).ready(function() {
    $('.datatable').DataTable({
        // --- CORRECCIÓN EN LA URL ---
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json"
        },
        "pageLength": 10
    });
});