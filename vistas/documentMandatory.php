<?php
// superadmin_dashboard.php

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Títulos de la Página -->
<div class="row page-titles">
    <div class="col-md-6 align-self-center">
        <h3 class="text-primary">Asignación de Documentos a Puestos</h3>
    </div>
    <div class="col-md-6 text-right">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Asignación de Documentos</li>
        </ol>
    </div>
</div>

<!-- Contenido de la Página -->
<div class="row">
    <div class="col-12">
        <div class="card shadow border-0 rounded-lg">
            <div class="card-body p-4">
                <h2 class="text-info mb-4 font-weight-bold text-center">
                    <i class="fa fa-file-text"></i> Asignación de Documentos a Puestos
                </h2>
                <h4 class="text-muted text-center mb-4">Selecciona la Empresa y el Puesto para realizar la asignación de documentos</h4>

                <!-- Inicio del Formulario -->
                <form id="formAsignarDocumentos">
                    <!-- Selección de Empresa -->
                    <div class="form-group">
                        <label for="company_id" class="font-weight-bold">Seleccione la Empresa</label>
                        <select id="company_id" class="form-control select2" data-placeholder="Seleccione una empresa" style="width: 100%;">
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>

                    <!-- Selección de Puesto -->
                    <div class="form-group">
                        <label for="position_id" class="font-weight-bold">Seleccione el Puesto</label>
                        <select id="position_id" class="form-control select2" data-placeholder="Seleccione un puesto" style="width: 100%;">
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>

                    <!-- Contenedor de Lista de Documentos -->
                    <div id="document-list-container" class="mt-4">
                        <h5 class="text-center font-weight-bold text-primary"><i class="fa fa-folder-open"></i> Asignación de Documentos</h5>
                        <p class="text-muted text-center mb-4">Seleccione los documentos obligatorios y opcionales asignados al puesto</p>

                        <div id="document-list">
                            <!-- Documentos cargados dinámicamente -->
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-success btn-lg shadow">Guardar Asignación</button>
                        <button type="button" id="btnCancelar" class="btn btn-danger btn-lg shadow ml-3">Cancelar</button>
                    </div>
                </form>
                <!-- Fin del Formulario -->
            </div>
        </div>
    </div>
</div>

<!-- Estilos Personalizados -->
<style>
    /* Tus estilos personalizados aquí */
    .form-group {
        margin-bottom: 1rem; /* Reducir el margen entre elementos */
    }

    label {
        font-size: 1.1rem;
        color: #333;
    }

    .form-control {
        padding: 0.6rem; /* Compactar el relleno interno */
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        font-size: 1.2rem;
        padding: 0.6rem 2rem;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        font-size: 1.2rem;
        padding: 0.6rem 2rem;
        transition: background-color 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .breadcrumb {
        background-color: transparent;
        font-size: 1rem;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    /* Contenedor de Documentos */
    #document-list-container {
        background-color: #f0f4f8;
        border-radius: 15px;
        padding: 15px; /* Reducir el relleno interno */
        margin-top: 15px; /* Reducir el margen superior */
        border: 2px solid #17a2b8;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    /* Título y descripción del contenedor */
    #document-list-container h5 {
        font-size: 1.3rem; /* Ajustar el tamaño del título */
        color: #007bff;
        margin-bottom: 10px; /* Reducir el margen inferior */
    }

    #document-list-container p {
        font-size: 0.9rem; /* Reducir el tamaño de la descripción */
        color: #6c757d;
        margin-bottom: 20px; /* Reducir el margen inferior */
    }

    /* Hover effect para el contenedor */
    #document-list-container:hover {
        background-color: #e3f2fd;
    }

    /* Estilos para los checkboxes y radios */
    .custom-checkbox .custom-control-label::before,
    .custom-radio .custom-control-label::before {
        border-radius: 50%;
        background-color: #f0f8ff;
        border: 2px solid #17a2b8;
    }

    .custom-checkbox .custom-control-input:checked~.custom-control-label::before,
    .custom-radio .custom-control-input:checked~.custom-control-label::before {
        background-color: #28a745;
        border-color: #28a745;
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem; /* Reducir el espacio entre los elementos */
    }

    .form-check-label {
        margin-left: 0.5rem;
        font-size: 0.95rem; /* Reducir el tamaño de la etiqueta */
    }

    .form-group.row {
        margin-bottom: 0.5rem; /* Reducir el margen entre filas */
    }

    /* Ajustar el espaciado entre radios y checkboxes */
    .custom-control {
        margin-right: 1rem; /* Reducir el espacio entre los controles */
    }

    /* Reducir el tamaño de los checkboxes y radios */
    .custom-control-input {
        transform: scale(1.1); /* Hacerlos ligeramente más pequeños */
    }
</style>

<!-- Script para la Interacción de Documentos -->
<script src="scripts/documentMandatory.js"></script>

<?php
require 'layout/footer.php';
?>
