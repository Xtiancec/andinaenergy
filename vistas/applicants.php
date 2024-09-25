<?php
// Verificar si el usuario ha iniciado sesión y tiene permisos adecuados
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<!-- Breadcrumb y título de la página -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Gestionar Postulantes</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Postulantes</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Botón para agregar postulante -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Listado de Postulantes</h5>
                    <button class="btn btn-success" data-toggle="modal" data-target="#formularioregistros">
                        <i class="fa fa-plus"></i> Agregar Postulante
                    </button>
                </div>

                <!-- Tabla para listar postulantes -->
                <div class="table-responsive">
                    <table id="tbllistado" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>ID</th>
                                <th>DNI</th>
                                <th>Email</th>
                                <th>Nombre Completo</th>
                                <th>Empresa</th>
                                <th>Área</th>
                                <th>Puesto</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>DNI</th>
                                <th>Email</th>
                                <th>Nombre Completo</th>
                                <th>Empresa</th>
                                <th>Área</th>
                                <th>Puesto</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Modal para agregar postulante -->
                <div class="modal fade" id="formularioregistros" tabindex="-1" role="dialog" aria-labelledby="modalAgregarLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <form id="formulario" method="POST" class="needs-validation" novalidate>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Postulante</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Formulario para agregar postulante -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="company_id">Empresa <span class="text-danger">*</span></label>
                                            <select class="form-control" id="company_id" name="company_id" required>
                                                <option value="">Seleccione una Empresa</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione una empresa.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="area_id">Área <span class="text-danger">*</span></label>
                                            <select class="form-control" id="area_id" name="area_id" required>
                                                <option value="">Seleccione un Área</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione un área.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="job_id">Puesto de Trabajo <span class="text-danger">*</span></label>
                                            <select class="form-control" id="job_id" name="job_id" required>
                                                <option value="">Seleccione un Puesto de Trabajo</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione un puesto de trabajo.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="username">DNI <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" maxlength="8 placeholder="Ingrese DNI" required pattern="\d{8}">
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un DNI válido de 8 dígitos.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="lastname">Apellido Paterno <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Apellido Paterno" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese el apellido paterno.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="surname">Apellido Materno <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="surname" name="surname" placeholder="Apellido Materno" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese el apellido materno.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="names">Nombres <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="names" name="names" placeholder="Nombres" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese los nombres.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un email válido.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal para actualizar postulante -->
                <div class="modal fade" id="formularioActualizar" tabindex="-1" role="dialog" aria-labelledby="modalActualizarLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <form id="formActualizar" method="POST" class="needs-validation" novalidate>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalActualizarLabel">Actualizar Postulante</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Formulario para actualizar postulante -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="company_idUpdate">Empresa <span class="text-danger">*</span></label>
                                            <select class="form-control" id="company_idUpdate" name="company_idUpdate" required>
                                                <option value="">Seleccione una Empresa</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione una empresa.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="area_idUpdate">Área <span class="text-danger">*</span></label>
                                            <select class="form-control" id="area_idUpdate" name="area_idUpdate" required>
                                                <option value="">Seleccione un Área</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione un área.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="job_idUpdate">Puesto de Trabajo <span class="text-danger">*</span></label>
                                            <select class="form-control" id="job_idUpdate" name="job_idUpdate" required>
                                                <option value="">Seleccione un Puesto de Trabajo</option>
                                                <!-- Opciones cargadas dinámicamente -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, seleccione un puesto de trabajo.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="usernameUpdate">DNI <span class="text-danger">*</span></label>
                                            <input type="hidden" id="idUpdate" name="idUpdate">
                                            <input type="text" class="form-control" id="usernameUpdate" name="usernameUpdate" maxlength="8" placeholder="Ingrese DNI" required pattern="\d{8}">
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un DNI válido de 8 dígitos.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="lastnameUpdate">Apellido Paterno <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="lastnameUpdate" name="lastnameUpdate" maxlength="100" placeholder="Apellido Paterno" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese el apellido paterno.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="surnameUpdate">Apellido Materno <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="surnameUpdate" name="surnameUpdate" maxlength="100" placeholder="Apellido Materno" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese el apellido materno.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="namesUpdate">Nombres <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="namesUpdate" name="namesUpdate" maxlength="100" placeholder="Nombres" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese los nombres.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="emailUpdate">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="emailUpdate" name="emailUpdate" maxlength="100" placeholder="Email" required>
                                            <div class="invalid-feedback">
                                                Por favor, ingrese un email válido.
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
<!-- Incluir el archivo JavaScript externo -->
<script src="scripts/applicants.js"></script>
