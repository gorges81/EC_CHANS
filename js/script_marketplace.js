// /js/script_marketplace.js

$(document).ready(function() {
    // --- Lógica para la página principal (main_marketplaces.php) ---
    if ($('#marketplacesGrid').length) {
        var table = $('#marketplacesGrid').DataTable({
            ajax: {
                url: 'obtener_datos_marketplaces.php',
                dataSrc: 'data'
            },
            columns: [
                { data: 'marketplace' },
                { data: 'description' },
                { data: 'url' },
                { data: 'usuario_marketplace' },
                { data: 'contrasena_marketplace' },
                { data: 'accion', orderable: false, searchable: false }
            ],
            // --- CORRECCIÓN DE IDIOMA ---
            // Se reemplaza la URL externa por el objeto de traducción directamente.
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
                },
                "aria": {
                    "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');
                $(row).on('click', function(e) {
                    // Evitar que el clic en el botón de eliminar navegue
                    if (!$(e.target).hasClass('btn-eliminar')) {
                        window.location.href = 'modificar_marketplace.php?id=' + data.id;
                    }
                });
            }
        });

        // Evento para el botón de eliminar
        $('#marketplacesGrid').on('click', '.btn-eliminar', function(e) {
            e.stopPropagation(); // Evitar que el clic se propague a la fila
            var id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('procesar_marketplace.php', { action: 'eliminar', id: id }, function(response) {
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
    }

    // --- Lógica para el formulario de agregar ---
    $('#formAgregarMarketplace').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post('procesar_marketplace.php', formData, function(response) {
            if (response.success) {
                Swal.fire('¡Guardado!', response.message, 'success').then(() => {
                    window.location.href = 'main_marketplaces.php';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json');
    });

    // --- Lógica para el formulario de modificar ---
    $('#formModificarMarketplace').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post('procesar_marketplace.php', formData, function(response) {
            if (response.success) {
                Swal.fire('¡Actualizado!', response.message, 'success').then(() => {
                    window.location.href = 'main_marketplaces.php';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json');
    });
});