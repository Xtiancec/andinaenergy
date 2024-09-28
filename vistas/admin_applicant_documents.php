<?php
// admin/admin_applicants_documents.php

session_start();

// Verificar si el usuario ha iniciado sesión y es superadministrador
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'superadmin'
) {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenedor Principal -->
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
                <table id="applicantsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Puesto</th>
<<<<<<< HEAD
                            <th>Foto</th>
=======
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
                            <th>Postulante</th>
                            <th>Email</th>
                            <th>Subidos CV</th>
                            <th>Subidos Otros</th>
                            <th>Aprobados CV</th>
                            <th>Aprobados Otros</th>
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
<<<<<<< HEAD
<!-- Modal para visualizar la imagen maximizada -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen del Postulante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Imagen del Postulante">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
=======
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733

<!-- Modal Único para Mostrar Documentos y Experiencias -->
<div class="modal fade" id="modalDetallesApplicant" tabindex="-1" role="dialog" aria-labelledby="modalDetallesApplicantLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalDetallesApplicantLabel">Detalles de <span id="applicantNombre"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Pestañas para Documentos y Experiencias -->
                <ul class="nav nav-tabs" id="detailsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="documentos-tab" data-toggle="tab" href="#documentos" role="tab" aria-controls="documentos" aria-selected="true">Documentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="educacion-tab" data-toggle="tab" href="#educacion" role="tab" aria-controls="educacion" aria-selected="false">Experiencia Educativa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="trabajo-tab" data-toggle="tab" href="#trabajo" role="tab" aria-controls="trabajo" aria-selected="false">Experiencia Laboral</a>
                    </li>
                </ul>
                <div class="tab-content" id="detailsTabContent">
                    <!-- Tab Documentos -->
                    <div class="tab-pane fade show active" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                        <div class="mt-3" id="documentosApplicantContainer">
                            <!-- Contenido dinámico de Documentos -->
                        </div>
                    </div>
                    <!-- Tab Experiencia Educativa -->
                    <div class="tab-pane fade" id="educacion" role="tabpanel" aria-labelledby="educacion-tab">
                        <div class="mt-3" id="educacionApplicantContainer">
                            <!-- Contenido dinámico de Experiencia Educativa -->
                        </div>
                    </div>
                    <!-- Tab Experiencia Laboral -->
                    <div class="tab-pane fade" id="trabajo" role="tabpanel" aria-labelledby="trabajo-tab">
                        <div class="mt-3" id="trabajoApplicantContainer">
                            <!-- Contenido dinámico de Experiencia Laboral -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botón de cerrar -->
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
<!-- Font Awesome para Iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<!-- Toastify CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom CSS -->
<link rel="stylesheet" href="styles/custom.css">
<!-- Custom JavaScript -->
<script src="scripts/documentEvaluationApplicant.js"></script>

<style>
    /* Estilos Personalizados */
    /* Estilos para la tabla */
    #applicantsTable thead th {
        background-color: #343a40;
        color: white;
        text-align: center;
        padding: 10px;
    }

    /* Estilos para las barras de progreso */
    .progress {
        height: 20px;
    }

    .progress-bar {
        line-height: 20px;
        font-size: 12px;
    }

    /* Estilos para las pestañas del modal */
    .nav-tabs .nav-link {
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        background-color: #343a40;
        color: white;
    }

    /* Estilos para botones en listas */
    .list-group-item button {
        margin-right: 5px;
    }
</style>

<?php
require 'layout/footer.php';
?>
