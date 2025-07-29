<?php
// app/usuarios/agregar_usuario.php
require_once __DIR__.'/../../db/config.php';
include __DIR__.'/../../includes/header.php';
?>

<link rel="stylesheet" href="/ec_chans/css/style_usuarios.css">

<div class="container-fluid dashboard-section p-4 my-4">
    <h2 class="mb-4 text-dark-blue">Agregar Nuevo Usuario</h2>
    
    <div class="card shadow-sm p-4">
        <form id="formUsuario" action="procesar_usuario.php" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario*</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo*</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email*</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña*</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña*</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Usuario*</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="lector">Lector</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="departamento" class="form-label">Departamento</label>
                        <input type="text" class="form-control" id="departamento" name="departamento">
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="main_usuarios.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-dark-blue">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<script src="/ec_chans/js/script_usuarios.js"></script>

<?php
include __DIR__.'/../../includes/footer.php';
?>