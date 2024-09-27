<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Gestionar Empresa</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Empresa</li>
        </ol>
    </div>

    <div>
        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10" aria-label="Abrir Panel de Configuración">
            <i class="ti-settings text-white"></i>
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="accordionBasic" class="widget-header">
                    <div class="text-left">
                        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#formularioregistros">
                            <i class="fa fa-plus"></i> Agregar Empresa
                        </button>
                    </div>
                </div>
                
                <!-- Tabla para listar empresas -->
                <div class="table-responsive mb-4 mt-4">
                    <table id="tbllistado" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="10%">RUC</th>
                                <th width="20%">Empresa</th>
                                <th width="20%">Descripción</th>
                                <th width="15%">F. Creación</th>
                                <th width="15%">F. Actualización</th>
                                <th width="10%">Estado</th>
                                <th width="10%">Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Modal para guardar empresa -->
                <div class="modal fade" id="formularioregistros" tabindex="-1" role="dialog" aria-labelledby="tituloModalGuardar" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document"> <!-- Usamos modal-lg para un tamaño más amplio -->
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="tituloModalGuardar">Agregar Empresa</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formulario" method="POST" class="form-material">
                                    <div class="form-group">
                                        <label for="ruc">RUC <span class="text-danger">*</span></label>
                                        <input type="text" id="ruc" name="ruc" class="form-control" maxlength="20" placeholder="Número de RUC" required pattern="\d{11}" title="Ingrese un RUC válido de 11 dígitos">
                                        <small id="rucHelp" class="form-text text-muted">Ingrese un RUC de 11 dígitos.</small>
                                        <div id="rucFeedback" class="invalid-feedback">
                                            RUC ya existe. Por favor, ingrese un RUC diferente.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="company_name">Empresa <span class="text-danger">*</span></label>
                                        <input type="text" id="company_name" name="company_name" class="form-control" maxlength="255" placeholder="Nombre de la empresa" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Descripción <span class="text-danger">*</span></label>
                                        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Descripción de la empresa" required></textarea>
                                    </div>
                                    <!-- Botón convertido a type="button" -->
                                    <button type="button" class="btn btn-success" onclick="guardar();">
                                        <i class="fa fa-check"></i> Guardar
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal para actualizar empresa -->
                <div class="modal fade" id="formularioActualizar" tabindex="-1" role="dialog" aria-labelledby="tituloModalActualizar" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document"> <!-- Usamos modal-lg para un tamaño más amplio -->
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="tituloModalActualizar">Actualizar Empresa</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formActualizar" method="POST" class="form-material">
                                    <input type="hidden" id="idUpdate" name="idUpdate">
                                    <div class="form-group">
                                        <label for="rucUpdate">RUC <span class="text-danger">*</span></label>
                                        <input type="text" id="rucUpdate" name="rucUpdate" class="form-control" maxlength="20" placeholder="Número de RUC" required pattern="\d{11}" title="Ingrese un RUC válido de 11 dígitos">
                                        <small id="rucUpdateHelp" class="form-text text-muted">Ingrese un RUC de 11 dígitos.</small>
                                        <div id="rucUpdateFeedback" class="invalid-feedback">
                                            RUC ya existe. Por favor, ingrese un RUC diferente.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="company_nameUpdate">Empresa <span class="text-danger">*</span></label>
                                        <input type="text" id="company_nameUpdate" name="company_nameUpdate" class="form-control" maxlength="255" placeholder="Nombre de la empresa" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="descriptionUpdate">Descripción <span class="text-danger">*</span></label>
                                        <textarea id="descriptionUpdate" name="descriptionUpdate" class="form-control" rows="3" placeholder="Descripción de la empresa" required></textarea>
                                    </div>
                                    <!-- Botón convertido a type="button" -->
                                    <button type="button" class="btn btn-primary" onclick="actualizar();">
                                        <i class="fa fa-check"></i> Guardar Cambios
                                    </button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
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
    <script src="scripts/companies.js"></script>
</div>