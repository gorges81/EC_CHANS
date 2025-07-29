// /js/script_all.js

document.addEventListener('DOMContentLoaded', function() {
    const correlativoSelect = document.getElementById('correlativoSelect');
    const btnIntegrar = document.getElementById('btnIntegrarAll');
    const progressContainer = document.getElementById('progressContainer');
    const btnVaciar = document.getElementById('btnVaciarAll');
    const btnVerDatos = document.getElementById('btnVerDatosAll');
    const datosModalElement = document.getElementById('datosModal');
    const datosModal = datosModalElement ? new bootstrap.Modal(datosModalElement) : null;

    // --- LÓGICA PARA INTEGRAR DATOS ---
    if (correlativoSelect && btnIntegrar) {
        correlativoSelect.addEventListener('change', () => btnIntegrar.disabled = !correlativoSelect.value);

        btnIntegrar.addEventListener('click', function() {
            const importacionId = correlativoSelect.value;
            const selectedText = correlativoSelect.options[correlativoSelect.selectedIndex].text;
            Swal.fire({
                title: '¿Confirmar Integración?',
                html: `Se borrarán todos los datos de la tabla 'All' y se procesarán los del correlativo <b>${selectedText}</b>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    progressContainer.style.display = 'block';
                    btnIntegrar.disabled = true;
                    correlativoSelect.disabled = true;
                    const formData = new FormData();
                    formData.append('importacion_id', importacionId);
                    fetch('/ec_chans/app/integrador/all/procesar_all.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire(data.success ? '¡Éxito!' : 'Error', data.message, data.success ? 'success' : 'error')
                        .then(() => window.location.reload());
                    });
                }
            });
        });
    }

    // --- LÓGICA PARA ELIMINAR TODO ---
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Todos los datos en la tabla 'All' serán eliminados permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡eliminar todo!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/ec_chans/app/integrador/all/vaciar_all.php', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success').then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        });
    }
    
    // --- LÓGICA PARA VER DATOS ---
    if (btnVerDatos) {
        btnVerDatos.addEventListener('click', function() {
            const correlativo = this.dataset.correlativo;
            let fetchController; 
            Swal.fire({
                title: 'Cargando Datos...',
                html: `Buscando registros para <b>${correlativo}</b>.`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    if (fetchController) fetchController.abort();
                }
            });
            const formData = new FormData();
            formData.append('correlativo', correlativo);
            fetchController = new AbortController();
            const signal = fetchController.signal;
            fetch('/ec_chans/app/integrador/all/obtener_datos_grid_all.php', { method: 'POST', body: formData, signal: signal })
            .then(response => response.json())
            .then(result => {
                Swal.close(); 
                if (result.success && result.data.length > 0) {
                    const gridContainer = document.getElementById('gridContainer');
                    if ($.fn.DataTable.isDataTable('#dynamicDataTableAll')) {
                        $('#dynamicDataTableAll').DataTable().destroy();
                    }
                    gridContainer.innerHTML = '<table id="dynamicDataTableAll" class="table table-striped table-bordered" style="width:100%"></table>';
                    
                    window.dataTableDataAll = result.data;
                    window.dataTableHeadersAll = result.headers;
                    
                    datosModal.show();
                } else {
                    Swal.fire('Sin Datos', 'No se encontraron registros para este correlativo.', 'info');
                }
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
                }
            });
        });
    }

    // --- CORRECCIÓN PARA ALINEACIÓN DE ENCABEZADOS EN POPUP ---
    if (datosModalElement) {
        datosModalElement.addEventListener('shown.bs.modal', function () {
            if (window.dataTableDataAll && window.dataTableHeadersAll) {
                $('#dynamicDataTableAll').DataTable({
                    data: window.dataTableDataAll,
                    columns: Object.keys(window.dataTableHeadersAll).map(key => ({ data: key, title: window.dataTableHeadersAll[key] })),
                    language: {"url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json"},
                    pageLength: 10,
                    scrollY: "50vh",
                    scrollX: true,
                    scrollCollapse: true,
                    initComplete: function() {
                        this.api().columns.adjust();
                    }
                });
                window.dataTableDataAll = null;
                window.dataTableHeadersAll = null;
            }
        });
        datosModalElement.addEventListener('hidden.bs.modal', function () {
            if ($.fn.DataTable.isDataTable('#dynamicDataTableAll')) {
                $('#dynamicDataTableAll').DataTable().destroy();
            }
            document.getElementById('gridContainer').innerHTML = '';
        });
    }
});