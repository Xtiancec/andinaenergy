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
        <h3 class="text-themecolor">Registrar Nombre de Documento de Empresas</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Documentos</li>
            <li class="breadcrumb-item active">Registrar Nombre de Documento de Empresas</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Formulario para registrar nombre de documento -->
                <form class="form-material m-t-40" id="formDocumentRegister" method="POST" enctype="multipart/form-data">
                    <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                        <div class="card-body p-4">
                            <h4 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-file-alt"></i> Registrar Nombre de Documento de Empresas</h4>

                            <!-- Nombre del Documento -->
                            <div class="form-group">
                                <label for="documentName" class="font-weight-bold"><i class="fa fa-file"></i> Nombre del Documento</label>
                                <input type="text" class="form-control rounded-lg" id="documentName" name="documentName" maxlength="255" placeholder="Ingrese el nombre del documento" required>
                            </div>

                            <!-- Descripción del Documento -->
                            <div class="form-group">
                                <label for="documentDescription" class="font-weight-bold"><i class="fa fa-info-circle"></i> Descripción del Documento</label>
                                <textarea class="form-control rounded-lg" id="documentDescription" name="documentDescription" rows="4" placeholder="Ingrese una descripción del documento (opcional)"></textarea>
                            </div>

                            <!-- Plantilla del Documento -->
                            <div class="form-group">
                                <label for="documentTemplate" class="font-weight-bold"><i class="fa fa-upload"></i> Subir Plantilla (opcional)</label>
                                <input type="file" class="dropify" id="documentTemplate" name="documentTemplate" accept=".pdf,.doc,.docx">
                            </div>

                            <!-- Botón de Envío -->
                            <button type="submit" class="btn btn-primary btn-block rounded-lg font-weight-bold">
                                <i class="fa fa-save"></i> Guardar Documento
                            </button>

                        </div>
                    </div>
                </form>

                <!-- Tabla para mostrar documentos registrados -->
                <div class="mt-5">
                    <h4 class="text-info mb-4 font-weight-bold"><i class="fa fa-list"></i> Documentos Registrados</h4>
                    <div class="table-responsive">
                        <table id="documentTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Documento</th>
                                    <th>Descripción</th>
                                    <th>Plantilla</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal para editar documento -->
                <div class="modal fade" id="modalEditarDocumento" tabindex="-1" role="dialog" aria-labelledby="modalEditarDocumentoLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="formEditarDocumento" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditarDocumentoLabel"><i class="fa fa-edit"></i> Editar Documento</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Campo oculto para el ID del documento -->
                                    <input type="hidden" id="editDocumentoId" name="id">

                                    <!-- Nombre del Documento -->
                                    <div class="form-group">
                                        <label for="editDocumentName" class="font-weight-bold"><i class="fa fa-file"></i> Nombre del Documento</label>
                                        <input type="text" class="form-control" id="editDocumentName" name="name" required>
                                    </div>

                                    <!-- Descripción del Documento -->
                                    <div class="form-group">
                                        <label for="editDocumentDescription" class="font-weight-bold"><i class="fa fa-info-circle"></i> Descripción del Documento</label>
                                        <textarea class="form-control" id="editDocumentDescription" name="description" rows="4"></textarea>
                                    </div>

                                    <!-- Plantilla del Documento -->
                                    <div class="form-group">
                                        <label for="editDocumentTemplate" class="font-weight-bold"><i class="fa fa-upload"></i> Subir nueva Plantilla (opcional)</label>
                                        <input type="file" class="dropify" id="editDocumentTemplate" name="documentTemplate" accept=".pdf,.doc,.docx">
                                        <small class="form-text text-muted">Deja este campo vacío si no deseas cambiar la plantilla.</small>
                                    </div>
                                </div>

                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script src="scripts/documentNameSupplier.js"></script>

<?php
require 'layout/footer.php';
?>