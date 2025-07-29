// /js/script_marcas.js

$(document).ready(function() {
    // --- Lógica para la página principal (main_marcas.php) ---
    if ($('#marcasGrid').length) {
        var table = $('#marcasGrid').DataTable({
            ajax: {
                url: 'obtener_datos_marcas.php',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'siglas' },
                { data: 'nombre' },
                { data: 'marketplace' },
                { data: 'accion', orderable: false, searchable: false }
            ],
            language: { /* ... Objeto de traducción en español ... */ },
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');
                $(row).on('click', function(e) {
                    if (!$(e.target).hasClass('btn-eliminar')) {
                        window.location.href = 'modificar_marcas.php?id=' + data.id;
                    }
                });
            }
        });

        $('#marcasGrid').on('click', '.btn-eliminar', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?', text: "¡No podrás revertir esto!", icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('procesar_marcas.php', { action: 'eliminar', id: id }, function(response) {
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

    // --- Lógica para los formularios ---
    $('#formAgregarMarca, #formModificarMarca').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post('procesar_marcas.php', formData, function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                    window.location.href = 'main_marcas.php';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json');
    });
});