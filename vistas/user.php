<?php
// superadmin_dashboard.php

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="container-fluid">
    <!-- Breadcrumb and Page Title -->
    <div class="row page-titles">
        <div class="col-md-6 col-sm-12">
            <h3 class="text-themecolor">Gestionar Usuarios</h3>
        </div>
        <div class="col-md-6 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item">Configuración</li>
                    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center bg-primary">
                    <h5 class="mb-0">Lista de Usuarios</h5>
                    <button class="btn btn-success" data-toggle="modal" data-target="#formularioregistros">
                        <i class="fa fa-plus"></i> Agregar Usuario
                    </button>
                </div>
                <div class="card-body">
                    <!-- Tabla para listar usuarios -->
                    <div class="table-responsive">
                        <table id="tbllistado" class="table table-striped table-bordered" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa</th>
                                    <th>Área</th>
                                    <th>Puesto</th>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa</th>
                                    <th>Área</th>
                                    <th>Puesto</th>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Modal para agregar usuario -->
                    <div class="modal fade" id="formularioregistros" tabindex="-1" role="dialog" aria-labelledby="agregarUsuarioLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="formulario" method="POST" enctype="multipart/form-data" novalidate>
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="agregarUsuarioLabel">Agregar Usuario</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Campos del formulario -->
                                        <div class="form-row">
                                            <!-- Empresa -->
                                            <div class="form-group col-md-6">
                                                <label for="company_id">Empresa <span class="text-danger">*</span></label>
                                                <select class="form-control" id="company_id" name="company_id" required>
                                                    <option value="">Seleccione una Empresa</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione una empresa.</div>
                                            </div>
                                            <!-- Área -->
                                            <div class="form-group col-md-6">
                                                <label for="area_id">Área <span class="text-danger">*</span></label>
                                                <select class="form-control" id="area_id" name="area_id" required>
                                                    <option value="">Seleccione un Área</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione un área.</div>
                                            </div>
                                            <!-- Puesto de Trabajo -->
                                            <div class="form-group col-md-6">
                                                <label for="job_id">Puesto de Trabajo <span class="text-danger">*</span></label>
                                                <select class="form-control" id="job_id" name="job_id" required>
                                                    <option value="">Seleccione un Puesto de Trabajo</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione un puesto de trabajo.</div>
                                            </div>
                                            <!-- Tipo de Identificación -->
                                            <div class="form-group col-md-6">
                                                <label for="identification_type">Tipo de Identificación <span class="text-danger">*</span></label>
                                                <select class="form-control" id="identification_type" name="identification_type" required>
                                                    <option value="">Seleccione un Tipo</option>
                                                    <option value="DNI">DNI</option>
                                                    <option value="Cédula">Cédula</option>
                                                    <option value="Pasaporte">Pasaporte</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione un tipo de identificación.</div>
                                            </div>

                                            <!-- Número de Identificación (Username) -->
                                            <div class="form-group col-md-6">
                                                <label for="username">Número de Identificación <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username" maxlength="50" placeholder="Número de Identificación" required>
                                                <div class="invalid-feedback" id="username_feedback">Ingrese un Número de Identificación válido.</div>
                                            </div>
                                            <!-- Email -->
                                            <div class="form-group col-md-6">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" maxlength="100" placeholder="Email" required>
                                                <div class="invalid-feedback">Ingrese un email válido.</div>
                                            </div>
                                            <!-- Apellido Paterno -->
                                            <div class="form-group col-md-4">
                                                <label for="lastname">Apellido Paterno <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="lastname" name="lastname" maxlength="100" placeholder="Apellido Paterno" required>
                                                <div class="invalid-feedback">Ingrese el apellido paterno.</div>
                                            </div>
                                            <!-- Apellido Materno -->
                                            <div class="form-group col-md-4">
                                                <label for="surname">Apellido Materno <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="surname" name="surname" maxlength="100" placeholder="Apellido Materno" required>
                                                <div class="invalid-feedback">Ingrese el apellido materno.</div>
                                            </div>
                                            <!-- Nombres -->
                                            <div class="form-group col-md-4">
                                                <label for="names">Nombres <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="names" name="names" maxlength="100" placeholder="Nombres" required>
                                                <div class="invalid-feedback">Ingrese los nombres.</div>
                                            </div>
                                            <!-- Nacionalidad -->
                                            <div class="form-group col-md-6">
                                                <label for="nacionality">Nacionalidad <span class="text-danger">*</span></label>
                                                <select class="form-control" id="nacionality" name="nacionality" required>
                                                    <!-- Opciones se cargarán dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione una nacionalidad.</div>
                                            </div>
                                            <!-- Rol -->
                                            <div class="form-group col-md-6">
                                                <label for="role">Rol <span class="text-danger">*</span></label>
                                                <select class="form-control" id="role" name="role" required>
                                                    <option value="">Seleccione un Rol</option>
                                                    <option value="user">Usuario</option>
                                                    <option value="superadmin">Super Administrador</option>
                                                    <option value="adminrh">Administrador RRHH</option>
                                                    <option value="adminpr">Administrador Procura</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione un rol.</div>
                                            </div>
                                            <!-- ¿Es Empleado? -->
                                            <div class="form-group col-md-12">
                                                <label for="is_employee">¿Es Empleado? <span class="text-danger">*</span></label>
                                                <select class="form-control" id="is_employee" name="is_employee" required>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione una opción.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para actualizar usuario -->
                    <div class="modal fade" id="formularioActualizar" tabindex="-1" role="dialog" aria-labelledby="actualizarUsuarioLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="formActualizar" method="POST" enctype="multipart/form-data" novalidate>
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="actualizarUsuarioLabel">Actualizar Usuario</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Campos del formulario -->
                                        <div class="form-row">
                                            <input type="hidden" id="idUpdate" name="idUpdate">
                                            <!-- Empresa -->
                                            <div class="form-group col-md-6">
                                                <label for="company_idUpdate">Empresa <span class="text-danger">*</span></label>
                                                <select class="form-control" id="company_idUpdate" name="company_idUpdate" required>
                                                    <option value="">Seleccione una Empresa</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione una empresa.</div>
                                            </div>
                                            <!-- Área -->
                                            <div class="form-group col-md-6">
                                                <label for="area_idUpdate">Área <span class="text-danger">*</span></label>
                                                <select class="form-control" id="area_idUpdate" name="area_idUpdate" required>
                                                    <option value="">Seleccione un Área</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione un área.</div>
                                            </div>
                                            <!-- Puesto de Trabajo -->
                                            <div class="form-group col-md-6">
                                                <label for="job_idUpdate">Puesto de Trabajo <span class="text-danger">*</span></label>
                                                <select class="form-control" id="job_idUpdate" name="job_idUpdate" required>
                                                    <option value="">Seleccione un Puesto de Trabajo</option>
                                                    <!-- Opciones cargadas dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione un puesto de trabajo.</div>
                                            </div>
                                            <!-- Tipo de Identificación -->
                                            <div class="form-group col-md-6">
                                                <label for="identification_typeUpdate">Tipo de Identificación <span class="text-danger">*</span></label>
                                                <select class="form-control" id="identification_typeUpdate" name="identification_typeUpdate" required>
                                                    <option value="">Seleccione un Tipo</option>
                                                    <option value="DNI">DNI</option>
                                                    <option value="Cédula">Cédula</option>
                                                    <option value="Pasaporte">Pasaporte</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione un tipo de identificación.</div>
                                            </div>

                                            <!-- Número de Identificación (Username) -->
                                            <div class="form-group col-md-6">
                                                <label for="usernameUpdate">Número de Identificación (Username) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="usernameUpdate" name="usernameUpdate" maxlength="50" placeholder="Número de Identificación" required readonly>
                                                <div class="invalid-feedback" id="usernameUpdate_feedback">Ingrese un número de identificación válido</div>
                                            </div>
                                            <!-- Email -->
                                            <div class="form-group col-md-6">
                                                <label for="emailUpdate">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="emailUpdate" name="emailUpdate" maxlength="100" placeholder="Email" required>
                                                <div class="invalid-feedback">Ingrese un email válido.</div>
                                            </div>
                                            <!-- Apellido Paterno -->
                                            <div class="form-group col-md-4">
                                                <label for="lastnameUpdate">Apellido Paterno <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="lastnameUpdate" name="lastnameUpdate" maxlength="100" placeholder="Apellido Paterno" required>
                                                <div class="invalid-feedback">Ingrese el apellido paterno.</div>
                                            </div>
                                            <!-- Apellido Materno -->
                                            <div class="form-group col-md-4">
                                                <label for="surnameUpdate">Apellido Materno <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="surnameUpdate" name="surnameUpdate" maxlength="100" placeholder="Apellido Materno" required>
                                                <div class="invalid-feedback">Ingrese el apellido materno.</div>
                                            </div>
                                            <!-- Nombres -->
                                            <div class="form-group col-md-4">
                                                <label for="namesUpdate">Nombres <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="namesUpdate" name="namesUpdate" maxlength="100" placeholder="Nombres" required>
                                                <div class="invalid-feedback">Ingrese los nombres.</div>
                                            </div>
                                            <!-- Nacionalidad -->
                                            <div class="form-group col-md-6">
                                                <label for="nacionalityUpdate">Nacionalidad <span class="text-danger">*</span></label>
                                                <select class="form-control" id="nacionalityUpdate" name="nacionalityUpdate" required>
                                                    <!-- Opciones se cargarán dinámicamente -->
                                                </select>
                                                <div class="invalid-feedback">Seleccione una nacionalidad.</div>
                                            </div>
                                            <!-- Rol -->
                                            <div class="form-group col-md-6">
                                                <label for="roleUpdate">Rol <span class="text-danger">*</span></label>
                                                <select class="form-control" id="roleUpdate" name="roleUpdate" required>
                                                    <option value="">Seleccione un Rol</option>
                                                    <option value="user">Usuario</option>
                                                    <option value="superadmin">Super Administrador</option>
                                                    <option value="adminrh">Administrador RRHH</option>
                                                    <option value="adminpr">Administrador Procura</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione un rol.</div>
                                            </div>
                                            <!-- ¿Es Empleado? -->
                                            <div class="form-group col-md-12">
                                                <label for="is_employeeUpdate">¿Es Empleado? <span class="text-danger">*</span></label>
                                                <select class="form-control" id="is_employeeUpdate" name="is_employeeUpdate" required>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <div class="invalid-feedback">Seleccione una opción.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fa fa-check"></i> Actualizar Cambios
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fa fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para ver el historial de accesos -->
                    <div class="modal fade" id="modalHistorial" tabindex="-1" role="dialog" aria-labelledby="historialAccesosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="historialAccesosLabel">Historial de Acceso</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <table id="tblHistorial" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Hora de Acceso</th>
                                                <th>Hora de Salida</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="thead-dark">
                                            <tr>
                                                <th>Hora de Acceso</th>
                                                <th>Hora de Salida</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carga de scripts -->
<?php
require 'layout/footer.php';
?>
<script src="scripts/user.js"></script>