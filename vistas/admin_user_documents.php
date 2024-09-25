<?php
// admin_user_documents.php

session_start();

// Verificar si el usuario ha iniciado sesión y es superadministrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenedor para los filtros y el DataTable -->
<div class="container-fluid mt-4">
    <h3 class="text-themecolor mb-4">Revisión de Documentos de Usuarios</h3>
    
    <!-- Formulario de Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filtroForm" class="form-inline">
                <div class="form-group mb-2">
                    <label for="startDate" class="mr-2">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="startDate" name="startDate">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="endDate" class="mr-2">Fecha Fin:</label>
                    <input type="date" class="form-control" id="endDate" name="endDate">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Aplicar Filtro</button>
                <button type="button" id="resetFilter" class="btn btn-secondary mb-2 ml-2">Resetear Filtro</button>
            </form>
        </div>
    </div>

    <!-- DataTable de Usuarios -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="usuariosTable" class="table color-table inverse-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Subidos Obligatorios (%)</th>
                            <th>Subidos Opcionales (%)</th>
                            <th>Aprobados Obligatorios (%)</th>
                            <th>Aprobados Opcionales (%)</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar documentos subidos del usuario -->
<div class="modal fade" id="modalDocumentosUsuario" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white font-weight-bold">
                <h5 class="modal-title" id="modalDocumentosUsuarioLabel">Documentos Subidos por <span id="usuarioNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs para documentos obligatorios y opcionales -->
                <ul class="nav nav-tabs mb-3" id="documentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="obligatorios-tab" data-toggle="tab" href="#obligatorios" role="tab" aria-controls="obligatorios" aria-selected="true">Documentos Obligatorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="opcionales-tab" data-toggle="tab" href="#opcionales" role="tab" aria-controls="opcionales" aria-selected="false">Documentos Opcionales</a>
                    </li>
                </ul>

                <div class="tab-content" id="documentTabsContent">
                    <div class="tab-pane fade show active" id="obligatorios" role="tabpanel" aria-labelledby="obligatorios-tab">
                        <div id="documentosObligatoriosContainer">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="opcionales" role="tabpanel" aria-labelledby="opcionales-tab">
                        <div id="documentosOpcionalesContainer">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- No es necesario el botón de guardar observación aquí -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Librerías Necesarias -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS y JS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" />
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<!-- Toastify CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    #usuariosTable thead th {
        background-color: #343a40;
        color: white;
        text-align: center;
        padding: 10px;
    }
</style>

<script src="scripts/user_documents.js"></script>

<?php
require 'layout/footer.php';
?>
