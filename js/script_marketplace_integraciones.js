// /js/script_marketplace_integraciones.js

$(document).ready(function() {
    // --- Lógica para la página principal (main) ---
    if ($('#integracionesGrid').length) {
        var table = $('#integracionesGrid').DataTable({
            ajax: {
                url: 'obtener_datos_integraciones.php',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'marketplace' },
                { data: 'url_select' },
                { data: 'archivo' },
                { data: 'accion', orderable: false, searchable: false },
                { data: 'marcas', orderable: false, searchable: false },
                { data: 'productos', orderable: false, searchable: false }
            ],
            language: {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "loadingRecords": "Cargando...",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');
                $(row).on('click', function(e) {
                    if ($(e.target).closest('button').length === 0) {
                        window.location.href = 'modificar_marketplace_integracion.php?id=' + data.id;
                    }
                });
            }
        });

        // Evento para el botón de eliminar integración
        $('#integracionesGrid').on('click', '.btn-eliminar', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Se eliminará la integración completa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('procesar_marketplace_integracion.php', { action: 'eliminar', id: id }, function(response) {
                        if (response.success) {
                            Swal.fire('¡Eliminado!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });

        // Lógica para el botón "Agregar Marcas"
        $('#integracionesGrid').on('click', '.btn-agregar-marcas', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            
            Swal.fire({
                title: '¿Agregar Marcas?',
                text: "Se leerá el archivo de la integración y se agregarán las marcas que no existan. ¿Continuar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Agregando marcas desde el archivo. Por favor espere.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.post('procesar_agregar_marcas.php', { integracion_id: id }, function(response) {
                        if (response.success) {
                            Swal.fire('¡Éxito!', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });

        // Lógica para el botón "Agregar Productos"
        $('#integracionesGrid').on('click', '.btn-agregar-productos', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            Swal.fire('Función no implementada', `Agregar Productos para la integración ID: ${id}`, 'info');
        });
    }

    // --- Lógica para los formularios (agregar y modificar) ---
    const importacionSelect = $('#id_importacion');
    const urlSelect = $('#url_select');
    const fileDisplayContainer = $('#fileDisplayContainer');
    const fileDisplayInput = $('#fileDisplay');

    function cargarUrls(selectedImportId, selectedUrl) {
        if (selectedImportId) {
            urlSelect.prop('disabled', true).html('<option>Cargando...</option>');
            $.post('obtener_urls_importacion.php', { id_importacion: selectedImportId }, function(response) {
                if (response.success && response.urls.length > 0) {
                    urlSelect.prop('disabled', false).html('<option value="">-- Seleccione una URL --</option>');
                    response.urls.forEach(function(urlData) {
                        const isSelected = (urlData.key === selectedUrl) ? 'selected' : '';
                        urlSelect.append(`<option value="${urlData.key}" data-filename="${urlData.value}" ${isSelected}>${urlData.key}</option>`);
                    });
                    urlSelect.trigger('change');
                } else {
                    urlSelect.html('<option value="">-- No hay URLs en esta importación --</option>');
                }
            }, 'json');
        } else {
            urlSelect.prop('disabled', true).html('<option value="">-- Seleccione una importación primero --</option>');
        }
    }

    function mostrarNombreArchivo() {
        const selectedOption = urlSelect.find('option:selected');
        const filename = selectedOption.data('filename');

        if (filename && selectedOption.val() !== '') {
            fileDisplayInput.val(filename);
            fileDisplayContainer.show();
        } else {
            fileDisplayInput.val('');
            fileDisplayContainer.hide();
        }
    }

    importacionSelect.on('change', function() {
        cargarUrls($(this).val(), null);
        mostrarNombreArchivo();
    });
    
    urlSelect.on('change', mostrarNombreArchivo);

    if ($('#formModificarIntegracion').length) {
        cargarUrls(importacionSelect.val(), urlSelect.val());
    }
    
    $('#formAgregarIntegracion, #formModificarIntegracion').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post('procesar_marketplace_integracion.php', formData, function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                    window.location.href = 'main_marketplaces_integraciones.php';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json');
    });
});