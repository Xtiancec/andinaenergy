<?php
// applicant_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión y es un applicant
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><i class="fa fa-file"></i> Subir Documentos</h3>
        </div>
    </div>

    <div class="row">
        <!-- Subida de CV -->
        <div class="col-lg-6 col-md-6">
            <div class="card card-outline-inverse">
                <div class="card-header bg-primary">
                    <h4 class="m-b-0 text-white"><i class="fa fa-file-text"></i> Subir CV</h4>
                </div>

                <div class="card-body">
                    <form id="formCvUpload" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="cv_file"><i class="fa fa-upload"></i> Seleccionar CV</label>
                            <input type="file" id="cv_file" name="cv_file[]" class="dropify" data-max-file-size="5M" multiple required />
                        </div>
                        <div class="form-group">
                            <label for="cv_observation"><i class="fa fa-comment"></i> Observación (Opcional)</label>
                            <textarea id="cv_observation" name="cv_observation" class="form-control" rows="3" placeholder="Escribe una observación sobre tu CV..."></textarea>
                        </div>
                        <div class="progress mb-3">
                            <div id="cvUploadProgress" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0% Complete
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary btn-block">
                            <i class="fa fa-cloud-upload-alt"></i> Subir CV
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subida de otros documentos -->
        <div class="col-lg-6 col-md-6">
            <div class="card card-outline-inverse">
                <div class="card-header bg-info">
                    <h4 class="m-b-0 text-white"><i class="fa fa-folder-open"></i> Subir Otros Documentos</h4>
                </div>

                <div class="card-body">
                    <form id="formOtherDocsUpload" method="POST" enctype="multipart/form-data">
                        <div id="otherDocsContainer">
                            <div class="form-group" id="doc_row_0">
                                <label for="other_file_0"><i class="fa fa-upload"></i> Seleccionar Documento</label>
                                <input type="file" id="other_file_0" name="other_files[]" class="dropify" data-max-file-size="5M" required />
                                <div class="form-group">
                                    <label for="other_observation_0"><i class="fa fa-comment"></i> Observación (Opcional)</label>
                                    <textarea id="other_observation_0" name="other_observations[]" class="form-control" rows="2" placeholder="Escribe una observación sobre este documento..."></textarea>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="button" class="btn btn-sm btn-danger removeDocumentRow" data-row-id="doc_row_0"><i class="fa fa-trash"></i> Eliminar</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addDocument" class="btn btn-info btn-sm mb-3">
                            <i class="fa fa-plus-circle"></i> Añadir Otro Documento
                        </button>
                        <div class="progress mb-3">
                            <div id="otherDocsUploadProgress" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0% Complete
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-info btn-block">
                            <i class="fa fa-cloud-upload-alt"></i> Subir Documentos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Documentos -->
    <div class="row mt-4">
        <div class="col-lg-12 col-md-12">
            <div class="card card-outline-inverse">
                <div class="card-header bg-secondary">
                    <h4 class="m-b-0 text-white"><i class="fa fa-archive"></i> Documentos Subidos</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableDocuments" class="table color-table inverse-table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-file"></i> Nombre Original</th>
                                    <th><i class="fa fa-comment"></i> Observación</th>
                                    <th><i class="fa fa-clock-o"></i> Fecha de Subida</th>
                                    <th><i class="fa fa-cog"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="documentList">
                                <!-- Los documentos se cargarán dinámicamente aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Incluir los scripts necesarios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Incluir DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- CSS adicional para mejorar el diseño -->
<style>
    /* General Colors */
    body {
        background-color: #f9f9f9;
    }

    /* Button Styles */
    .btn-gradient-primary {
        background: linear-gradient(45deg, #1e88e5, #42a5f5);
        border: none;
        color: white;
        font-weight: 600;
        transition: background 0.3s ease-in-out;
    }

    .btn-gradient-info {
        background: linear-gradient(45deg, #29b6f6, #81d4fa);
        border: none;
        color: white;
        font-weight: 600;
        transition: background 0.3s ease-in-out;
    }

    .btn-gradient-primary:hover,
    .btn-gradient-info:hover {
        background: linear-gradient(45deg, #1565c0, #2196f3);
        /* Darker on hover */
    }

    .btn-sm {
        padding: 5px 10px;
    }

    /* Card Styling */
    .card {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.15);
    }

    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #e0e0e0;
    }

    /* Table Headers */
    .table thead th {
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 12px;
    }

    /* Progress Bar */
    .progress-bar {
        height: 14px;
        border-radius: 10px;
    }

    /* Dropify Input */
    .dropify-wrapper {
        border-radius: 10px;
    }

    /* Form Spacing */
    .form-group {
        margin-bottom: 20px;
    }

    /* Table Box Shadow */
    .table-responsive {
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    /* Page Title Styling */
    .text-themecolor {
        color: #3f51b5 !important;
        font-weight: bold;
    }

    /* Container Padding */
    .container-fluid {
        padding: 20px;
    }
</style>

<!-- JavaScript para la Subida de Documentos -->
<script src="scripts/documentApplicant.js"></script>
