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

<!-- Títulos de la Página -->
<div class="row mb-4">
    <div class="col-12">
        <h3 class="text-primary">Registrar/Editar Experiencia Laboral y Educacional</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="fa fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registrar/Editar Experiencia</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Formularios para Experiencia Educativa y Laboral -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <!-- Experiencia Educativa -->
        <div class="card card-outline-inverse">
            <div class="card-header bg-inverse text-white d-flex justify-content-between align-items-center">
                <h4 class="m-b-0 text-white"><i class="fa fa-book mr-2"></i> Experiencia Educativa</h4>
                <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalEducacion">
                    <i class="fa fa-plus text-primary"></i> Agregar
                </button>
            </div>
            <div class="card-body">
                <!-- Tabla de Experiencia Educativa -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tablaExperienciaEducativa">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>Institución</th>
                                <th>Tipo de Educación</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Duración</th>
                                <th>Archivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filas generadas dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <!-- Experiencia Laboral -->
        <div class="card card-outline-inverse">
            <div class="card-header bg-inverse text-white d-flex justify-content-between align-items-center">
                <h4 class="m-b-0 text-white"><i class="fa fa-briefcase mr-2"></i> Experiencia Laboral</h4>
                <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalTrabajo">
                    <i class="fa fa-plus text-success"></i> Agregar
                </button>
            </div>
            <div class="card-body">
                <!-- Tabla de Experiencia Laboral -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tablaExperienciaLaboral">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>Empresa</th>
                                <th>Puesto</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Archivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filas generadas dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar/Editar Experiencia Educativa -->
<div class="modal fade" id="modalEducacion" tabindex="-1" aria-labelledby="modalEducacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEducacion" enctype="multipart/form-data">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title m-b-0 text-white" id="modalEducacionLabel">Agregar Experiencia Educativa</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="educacion_id" name="educacion_id">
                    <div class="form-group">
                        <label for="institution">Institución <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="institution" name="institution" required>
                    </div>
                    <div class="form-group">
                        <label for="education_type">Tipo de Educación <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="education_type" name="education_type" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date_education">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date_education" name="start_date_education" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date_education">Fecha de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date_education" name="end_date_education" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duration_education">Duración <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="duration_education" name="duration_education" min="1" required>
                            
                                <select class="form-control" id="duration_unit_education" name="duration_unit_education" required>
                                    <option value="Horas">Horas</option>
                                    <option value="Meses" selected>Meses</option>
                                    <option value="Semestres">Semestres</option>
                                    <option value="Años">Años</option>
                                </select>
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_education">Archivo (Opcional)</label>
                        <input type="file" class="form-control-file" id="file_education" name="file_education" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Tamaño máximo: 5MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="guardarEducacion">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Agregar/Editar Experiencia Laboral -->
<div class="modal fade" id="modalTrabajo" tabindex="-1" aria-labelledby="modalTrabajoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTrabajo" enctype="multipart/form-data">
                <div class="modal-header bg-inverse text-white">
                    <h5 class="modal-title m-b-0 text-white" id="modalTrabajoLabel">Agregar Experiencia Laboral</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="trabajo_id" name="trabajo_id">
                    <div class="form-group">
                        <label for="company">Empresa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company" name="company" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Puesto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date_work">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date_work" name="start_date_work" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date_work">Fecha de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date_work" name="end_date_work" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_work">Archivo (Opcional)</label>
                        <input type="file" class="form-control-file" id="file_work" name="file_work" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Tamaño máximo: 5MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="guardarTrabajo">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts Mejorados -->
<!-- Asegúrate de incluir las librerías necesarias antes de este script -->


<!-- Scripts Mejorados -->
<script src="scripts/experience.js"></script>

<?php require 'layout/footer.php'; ?>