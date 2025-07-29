// /js/script_active.js

document.addEventListener('DOMContentLoaded', function() {
    const correlativoSelect = document.getElementById('correlativoSelect');
    const btnIntegrar = document.getElementById('btnIntegrarActive');
    const btnVaciar = document.getElementById('btnVaciarActive');
    const btnVerDatos = document.getElementById('btnVerDatosActive');
    const datosModalElement = document.getElementById('datosModal');
    const datosModal = datosModalElement ? new bootstrap.Modal(datosModalElement) : null;

    // Habilitar el botón de integrar cuando se selecciona un lote
    if (correlativoSelect && btnIntegrar) {
        correlativoSelect.addEventListener('change', () => {
            btnIntegrar.disabled = !correlativoSelect.value;
        });
    }

    // Lógica para el botón "Iniciar Integración"
    if (btnIntegrar) {
        btnIntegrar.addEventListener('click', function() {
            const importacionId = correlativoSelect.value;
            const selectedText = correlativoSelect.options[correlativoSelect.selectedIndex].text;
            
            Swal.fire({
                title: '¿Confirmar Integración?',
                html: `Se borrarán los datos actuales de 'Active' y se procesarán los del lote <b>${selectedText}</b>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, integrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Migrando datos de Active Listings. Por favor espere.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '/ec_chans/app/integrador/active/procesar_active.php',
                        type: 'POST',
                        data: { importacion_id: importacionId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('¡Éxito!', response.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'No se pudo conectar con el servidor.';
                            Swal.fire('Error de Comunicación', errorMsg, 'error');
                        }
                    });
                }
            });
        });
    }

    // Lógica para el botón "Eliminar Todo"
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Todos los registros de Active Listings serán eliminados permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/ec_chans/app/integrador/active/vaciar_active.php', function(response) {
                        if(response.success) {
                            Swal.fire('Eliminado', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
    }

    // Lógica para el botón "Ver Datos"
    if (btnVerDatos) {
        btnVerDatos.addEventListener('click', function() {
            const correlativo = this.dataset.correlativo;
            const gridContainer = document.getElementById('gridContainer');
            
            gridContainer.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Cargando datos...</p></div>';
            datosModal.show();

            $.post('/ec_chans/app/integrador/active/obtener_datos_grid_active.php', { correlativo: correlativo }, 'json')
                .done(function(response) {
                    if (response.success && response.data.length > 0) {
                        window.dataTableData = response.data;
                        window.dataTableHeaders = response.headers;
                    } else {
                        gridContainer.innerHTML = '<div class="alert alert-warning">No se encontraron datos para mostrar.</div>';
                    }
                })
                .fail(function() {
                    gridContainer.innerHTML = '<div class="alert alert-danger">Error al cargar los datos. Intente de nuevo.</div>';
                });
        });
    }

    // Eventos para manejar el popup (modal)
    if (datosModalElement) {
        datosModalElement.addEventListener('shown.bs.modal', function () {
            if (window.dataTableData && window.dataTableHeaders) {
                const gridContainer = document.getElementById('gridContainer');
                gridContainer.innerHTML = '<table id="dynamicDataTable" class="table table-striped table-bordered w-100"></table>';
                
                $('#dynamicDataTable').DataTable({
                    data: window.dataTableData,
                    columns: Object.keys(window.dataTableHeaders).map(key => ({ data: key, title: window.dataTableHeaders[key] })),
                    language: { url: "https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json" },
                    responsive: true,
                    scrollX: true
                });
            }
        });

        datosModalElement.addEventListener('hidden.bs.modal', function () {
            if ($.fn.DataTable.isDataTable('#dynamicDataTable')) {
                $('#dynamicDataTable').DataTable().destroy();
            }
            document.getElementById('gridContainer').innerHTML = '';
            window.dataTableData = null;
            window.dataTableHeaders = null;
        });
    }
});