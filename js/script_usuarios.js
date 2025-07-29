// js/script_usuarios.js
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    const formUsuario = document.getElementById('formUsuario');
    if (formUsuario) {
        formUsuario.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
        });
    }
    
    // Manejo de eliminación
    const btnsEliminar = document.querySelectorAll('.btn-eliminar');
    btnsEliminar.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('¿Está seguro de eliminar este usuario?')) {
                fetch(`procesar_usuario.php?action=eliminar&id=${id}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error al eliminar usuario');
                    }
                });
            }
        });
    });
});