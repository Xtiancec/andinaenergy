<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>




<h2 class="mb-6 text-center">Mis Experiencias</h2>

<!-- Botón General para Editar Experiencias -->
<div class="mb-6 text-right">
    <a href="experience.php" class="btn btn-primary">
        <i class="fa fa-edit"></i> Editar Experiencias
    </a>
</div>
<br>


<!-- Sección de Experiencia Educativa -->
<div class="row mt-6">
    <div class="col-lg-12 col-md-12">
        <div class="card card-outline-inverse">
            <div class="card-header bg-inverse">
                <h4 class="m-b-0 text-white"><i class="fa fa-folder-open"></i>Experiencia Educativa</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaExperienciaEducativa" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>Institución</th>
                                <th>Tipo de Educación</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Duración</th>
                                <th>Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente mediante DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Sección de Experiencia Laboral -->
<div class="row mt-6">
    <div class="col-lg-12 col-md-12">
        <div class="card card-outline-inverse">
            <div class="card-header bg-inverse">
                <h4 class="m-b-0 text-white"><i class="fa fa-folder-open"></i>Experiencia Laboral</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaExperienciaLaboral" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>Compañía</th>
                                <th>Posición</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente mediante DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>




<script src="scripts/view_experience.js"></script>


<?php require 'layout/footer.php'; ?>