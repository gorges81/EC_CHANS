// /js/script_importaciones.js

document.addEventListener('DOMContentLoaded', function () {
    // --- Lógica para los formularios de Agregar/Modificar Importación ---
    const formNueva = document.getElementById('formNuevaImportacion');
    const formModificar = document.getElementById('formModificarImportacion');
    const botones = document.querySelectorAll('.btn-select-file');
    const fileInputs = document.querySelectorAll('input[type="file"]');

    // Lógica para el botón "Buscar"
    botones.forEach(boton => {
        boton.addEventListener('click', function () {
            const inputId = this.getAttribute('data-target-input');
            const inputFile = document.getElementById(inputId);
            if (inputFile) {
                inputFile.click();
            }
        });
    });

    // Lógica para mostrar el nombre del archivo seleccionado
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const fileName = this.files.length > 0 ? this.files[0].name : '';
            const displayInputId = 'display_' + this.id.replace('file_', '');
            const displayInput = document.getElementById(displayInputId);
            if (displayInput) {
                displayInput.value = fileName;
            }
            checkFormValidity();
        });
    });

    // Lógica para habilitar/deshabilitar el botón de guardar
    function checkFormValidity() {
        let atLeastOneFileSelected = false;
        fileInputs.forEach(input => {
            if (input.files.length > 0) {
                atLeastOneFileSelected = true;
            }
        });
        const btnGuardar = document.getElementById('btnGuardarImportacion');
        if (btnGuardar) {
            btnGuardar.disabled = !atLeastOneFileSelected;
        }
    }
    checkFormValidity();

    // Función genérica para manejar el envío de formularios con AJAX
    function handleFormSubmit(formElement) {
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(formElement);

                Swal.fire({
                    title: 'Procesando...',
                    text: 'Guardando los datos, por favor espere.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(formElement.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message
                        }).then(() => {
                            window.location.href = 'main_importaciones.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo procesar la solicitud. Intente de nuevo.'
                    });
                });
            });
        }
    }

    // Aplicar el manejador a ambos formularios
    handleFormSubmit(formNueva);
    handleFormSubmit(formModificar);

    // --- Lógica para la tabla en main_importaciones.php ---
    if (typeof jQuery !== 'undefined' && $('#importacionesTable').length) {
        // Inicialización de la tabla con la traducción corregida
        $('#importacionesTable').DataTable({
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
            }
        });

        // Listener para el botón de eliminar
        $('#importacionesTable').on('click', '.btn-eliminar', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const importacionId = $(this).data('id');
            const correlativo = $(this).data('correlativo');
            const fileCount = $(this).data('file-count');

            let confirmText = `¿Está seguro de eliminar la importación ${correlativo}?`;
            if (fileCount > 0) {
                confirmText += ` Se eliminarán ${fileCount} archivo(s) asociado(s).`;
            }

            Swal.fire({
                title: '¿Confirmar Eliminación?', text: confirmText, icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('eliminar_importaciones.php', { id: importacionId }, function(response) {
                        if (response.success) {
                            Swal.fire('¡Eliminado!', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
        
        // Listener para hacer las filas clickeables
        $('#importacionesTable tbody').on('click', 'tr', function(e) {
            if (e.target.closest('.btn-eliminar')) {
                return;
            }
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        });

        // Inicializar Popovers para la lista de archivos
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
});