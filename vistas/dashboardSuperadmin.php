<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión y es un superadministrador
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    $_SESSION['user_role'] !== 'superadmin'
) {
    header("Location: ../../login.php"); // Asegúrate de que esta sea la URL correcta de login
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenido del Dashboard del Super Administrador -->
<div class="container-fluid">
    <!-- Título y Breadcrumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                <i class="fas fa-chart-bar"></i> Dashboard Superadmin
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="escritorio.php">Inicio</a></li>
                <li class="breadcrumb-item">Super Administrador</li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <!-- Total de Usuarios -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Usuarios Totales</h5>
                            <h3 class="font-weight-bold" id="total-usuarios">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total de Postulantes -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Postulantes Totales</h5>
                            <h3 class="font-weight-bold" id="total-postulantes">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-user-tie fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total de Empresas -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Empresas Totales</h5>
                            <h3 class="font-weight-bold" id="total-empresas">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-building fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Pendientes User -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Docs Pendientes User</h5>
                            <h3 class="font-weight-bold" id="documentos-pendientesUser">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Pendientes Postulantes -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Docs Pendientes Postulantes</h5>
                            <h3 class="font-weight-bold" id="documentos-pendientesApplicant">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Pendientes Proveedores -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Docs Pendientes Proveedores</h5>
                            <h3 class="font-weight-bold" id="documentos-pendientesSupplier">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Usuarios Registrados por Mes -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Usuarios Registrados por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="usuarios-chart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Actividad Reciente</h5>
                    <a href="#" class="btn btn-sm btn-primary">Ver Más</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabla-actividad">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contenido dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Documentos por Estado -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documentos por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="documentos-estado-chart" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- Puedes añadir más gráficos o componentes aquí -->
    </div>
</div>
<style>
    /* style.css */

    /* Estilos para las tarjetas de estadísticas */
    .card-title {
        font-size: 1rem;
    }

    .font-weight-bold {
        font-size: 1.5rem;
    }

    /* Ajustes para los iconos en las tarjetas */
    .card i {
        color: rgba(255, 255, 255, 0.7);
    }

    /* Estilos para el gráfico de usuarios */
    #usuarios-chart {
        width: 100%;
        height: 400px;
    }

    /* Estilos para el gráfico de documentos por estado */
    #documentos-estado-chart {
        width: 100%;
        height: 300px;
    }

    /* Mejora de la tabla */
    table.dataTable thead th,
    table.dataTable thead td {
        border-bottom: none;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #dee2e6;
    }
</style>
<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Incluir DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Tu script personalizado -->
<script src="scripts/dashboardSuperadmin.js"></script>

<?php
require 'layout/footer.php';
?>