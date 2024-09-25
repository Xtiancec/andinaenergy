<?php 
// admin_applicants_documents.php

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
    <h3 class="text-themecolor mb-4">Revisión de Documentos de Postulantes</h3>
    
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

    <!-- DataTable de Postulantes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="applicantsTable" class="table color-table inverse-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Postulante</th>
                            <th>Email</th>
                            <th>Subidos CV (%)</th>
                            <th>Subidos Otros (%)</th>
                            <th>Aprobados CV (%)</th>
                            <th>Aprobados Otros (%)</th>
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

<!-- Modal para mostrar documentos subidos del postulante -->
<div class="modal fade" id="modalDocumentosApplicant" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosApplicantLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white font-weight-bold">
                <h5 class="modal-title" id="modalDocumentosApplicantLabel">Documentos Subidos por <span id="applicantNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenido dinámico -->
                <div id="documentosApplicantContainer">
                    <!-- Se llenará dinámicamente con JavaScript -->
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
    #applicantsTable thead th {
        background-color: #343a40;
        color: white;
        text-align: center;
        padding: 10px;
    }
</style>

<!-- Incluir el script JavaScript -->
<script src="scripts/documentEvaluationApplicant.js"></script>

<?php
require 'layout/footer.php';
?>
