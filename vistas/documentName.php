<?php
// superadmin_dashboard.php

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Crear Documentos</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Creación de Documentos</li>
        </ol>
    </div>

    <div>
        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
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
                        <button class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#formularioregistros">
                            <span class="btn-label"><i class="fa fa-plus"></i></span>
                            Agregar Documento
                        </button>
                    </div>
                </div>

                <div class="table-responsive mb-4 mt-4">
                    <table id="tbllistado" class="table full-color-table full-muted-table hover-table" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="30%">Documento</th>
                                <th width="15%">F. Creación</th>
                                <th width="15%">F. Actualización</th>
                                <th width="15%">Estado</th>
                                <th width="15%">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- Modal para Guardar -->
                <div class="modal fade" id="formularioregistros" tabindex="-1" role="dialog" aria-labelledby="formularioregistrosLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="formularioregistrosLabel">Agregar Documento</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formulario" name="formulario" class="form-material" method="POST">
                                    <input type="hidden" id="id" name="id">
                                    <div class="form-group">
                                        <label for="documentName">Documento</label>
                                        <input type="text" class="form-control" id="documentName" name="documentName"
                                            maxlength="50" placeholder="Nombre del documento" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" id="btnGuardar">
                                            <i class="fa fa-check"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="flaticon-cancel-12"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para Actualizar -->
                <div class="modal fade" id="formularioActualizar" tabindex="-1" role="dialog" aria-labelledby="formularioActualizarLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="formularioActualizarLabel">Actualizar Documento</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formActualizar" name="formActualizar" class="form-material" method="POST">
                                    <input type="hidden" id="idUpdate" name="idUpdate">
                                    <div class="form-group">
                                        <label for="documentNameUpdate">Documento</label>
                                        <input type="text" class="form-control" id="documentNameUpdate" name="documentNameUpdate"
                                            maxlength="50" placeholder="Nombre del documento" required autofocus>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="btnActualizar">
                                            <i class="fa fa-check"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="flaticon-cancel-12"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Scripts finales -->
<?php
require 'layout/footer.php';
?>
<!-- Incluye el archivo JavaScript externo -->
<script src="scripts/documentName.js"></script>
