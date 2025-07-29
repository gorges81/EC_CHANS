// /ec_chans/js/script_analisis.js

document.addEventListener('DOMContentLoaded', function() {
    console.log("Módulo de Análisis cargado. Inicializando gráficos...");

    // Datos de ejemplo para los gráficos (en un entorno real, estos vendrían de una API PHP)
    const datosImportacionesPorMes = {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
        datasets: [{
            label: 'Nuevas Importaciones',
            data: [12, 19, 3, 5, 2, 3, 15],
            backgroundColor: 'rgba(0, 38, 58, 0.8)', // var(--dark-blue)
            borderColor: 'rgba(254, 221, 0, 1)', // var(--bright-yellow)
            borderWidth: 1,
            fill: false
        }]
    };

    const datosArchivosPorTipo = {
        labels: ['Categoría', 'Activos', 'Shopify PH', 'Walmart Inv'],
        datasets: [{
            label: 'Cantidad de Archivos',
            data: [300, 500, 200, 450],
            backgroundColor: [
                'rgba(254, 221, 0, 0.8)',   // bright-yellow
                'rgba(0, 38, 58, 0.8)',    // dark-blue
                'rgba(100, 118, 150, 0.8)', // grey-blue
                'rgba(255, 105, 0, 0.8)'   // action-orange
            ],
            borderColor: [
                'rgba(254, 221, 0, 1)',
                'rgba(0, 38, 58, 1)',
                'rgba(100, 118, 150, 1)',
                'rgba(255, 105, 0, 1)'
            ],
            borderWidth: 1
        }]
    };

    // Configuración y renderizado del gráfico de Importaciones por Mes (Líneas)
    const ctxImportacionesMes = document.getElementById('importacionesPorMesChart');
    if (ctxImportacionesMes) {
        new Chart(ctxImportacionesMes, {
            type: 'line',
            data: datosImportacionesPorMes,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Configuración y renderizado del gráfico de Archivos por Tipo (Barras)
    const ctxArchivosTipo = document.getElementById('archivosPorTipoChart');
    if (ctxArchivosTipo) {
        new Chart(ctxArchivosTipo, {
            type: 'bar',
            data: datosArchivosPorTipo,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Lógica para el formulario de filtros (ejemplo: solo log en consola por ahora)
    const formFiltros = document.getElementById('formFiltrosAnalisis');
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            const fechaInicio = document.getElementById('filtroFechaInicio').value;
            const fechaFin = document.getElementById('filtroFechaFin').value;
            console.log(`Filtros aplicados: Desde ${fechaInicio} hasta ${fechaFin}`);
            // Aquí iría la lógica AJAX para recargar los datos de los gráficos y la tabla
            // Basándose en los filtros seleccionados, y luego actualizar los gráficos.
        });
    }

    // Aquí puedes añadir más lógica JavaScript específica para el módulo de análisis
    // como la carga dinámica de datos, interactividad con tablas, etc.
});