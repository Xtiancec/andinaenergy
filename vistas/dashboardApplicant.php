<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor"><i class="fa fa-chart-bar"></i> Mi Panel de Información</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Postulantes</li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>

<!-- Fila de gráficos -->
<div class="row">
    <!-- Progreso de Documentos -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="fa fa-file-upload"></i> Progreso de Documentos</h4>
                <canvas id="documentsChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Historial de Accesos -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-success"><i class="fa fa-history"></i> Historial de Accesos</h4>
                <canvas id="accessLogsChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Estado de Evaluación de Documentos -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-warning"><i class="fa fa-check-circle"></i> Evaluación de Documentos</h4>
                <canvas id="evaluationChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estado del Proceso de Selección -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-info"><i class="fa fa-tasks"></i> Proceso de Selección</h4>
                <canvas id="processChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Progreso Educativo -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-danger"><i class="fa fa-graduation-cap"></i> Progreso Educativo</h4>
                <canvas id="educationChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Documentos Subidos por Tipo -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="fa fa-folder"></i> Documentos por Tipo</h4>
                <canvas id="documentTypeChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Experiencia Laboral Total -->
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="card-title text-success"><i class="fa fa-briefcase"></i> Experiencia Laboral</h4>
                <canvas id="experienceChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<div id="dashboardData" data-applicant-id="<?= $_SESSION['applicant_id']; ?>"></div>
<!-- Agregar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="scripts/dashboardApplicant.js"></script>

<!-- Opcional: Personalizar colores de las tarjetas con CSS -->
<style>
    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: scale(1.02);
    }
</style>
