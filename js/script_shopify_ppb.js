// /js/script_shopify_ppb.js

document.addEventListener('DOMContentLoaded', function() {
    // Se asegura de buscar los IDs correctos para el módulo PPB
    const correlativoSelect = document.getElementById('correlativoSelect');
    const btnIntegrar = document.getElementById('btnIntegrarShopifyPPB');
    const btnVaciar = document.getElementById('btnVaciarShopifyPPB');
    const btnVerDatos = document.getElementById('btnVerDatosShopifyPPB');
    const datosModalElement = document.getElementById('datosModal');
    const datosModal = datosModalElement ? new bootstrap.Modal(datosModalElement) : null;

    // Lógica para activar/desactivar el botón de integración
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
                html: `Se borrarán los datos actuales de 'Shopify PPB' y se procesarán los del lote <b>${selectedText}</b>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, integrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Migrando datos de Shopify PPB. Por favor espere.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '/ec_chans/app/integrador/shopify_ppb/procesar_shopify_ppb.php',
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

    // --- LÓGICA CORREGIDA PARA EL BOTÓN "ELIMINAR TODO" ---
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Todos los registros de Shopify PPB serán eliminados permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/ec_chans/app/integrador/shopify_ppb/vaciar_shopify_ppb.php', function(response) {
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

    // --- LÓGICA CORREGIDA PARA EL BOTÓN "VER DATOS" ---
    if (btnVerDatos) {
        btnVerDatos.addEventListener('click', function() {
            const correlativo = this.dataset.correlativo;
            const gridContainer = document.getElementById('gridContainer');
            
            gridContainer.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Cargando datos...</p></div>';
            datosModal.show();

            $.post('/ec_chans/app/integrador/shopify_ppb/obtener_datos_grid_shopify_ppb.php', { correlativo: correlativo }, 'json')
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