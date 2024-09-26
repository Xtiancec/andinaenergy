<?php
// adminpr_dashboard.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión y es un adminpr (Administrador de Proveedores) o superadmin
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminpr']) // Permitir 'superadmin' o 'adminpr'
) {
    header("Location: ../login.php"); // Asegúrate de que esta sea la URL correcta de login
    exit();
}

// Definir scripts específicos para esta página
$page_specific_scripts = '
<!-- DataTables Initialization -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="scripts/dashboardAdminpr.js"></script>
';

// Incluir los layouts
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenido del Dashboard del Administrador de Proveedores -->
<div class="container-fluid">
    <!-- Título y Breadcrumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                <i class="fas fa-chart-line"></i> Dashboard AdminPR
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="escritorio.php">Inicio</a></li>
                <li class="breadcrumb-item">Administrador de Proveedores</li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <!-- Total de Proveedores -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Proveedores Totales</h5>
                            <h3 class="font-weight-bold" id="total-suppliers">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-building fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total de Usuarios Proveedores -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Usuarios Proveedores</h5>
                            <h3 class="font-weight-bold" id="total-supplier-users">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-user-shield fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Pendientes de Evaluación -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Docs Pendientes Evaluación</h5>
                            <h3 class="font-weight-bold" id="pending-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos Evaluados -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Docs Evaluados</h5>
                            <h3 class="font-weight-bold" id="evaluated-documents">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-check fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Proveedores Registrados por Mes (Morris.js) -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Proveedores Registrados por Mes</h4>
                    <div id="morris-bar-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Proveedores por Área (eCharts) -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Proveedores por Área</h4>
                    <div id="suppliers-area-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente de Proveedores -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Actividad Reciente de Proveedores</h5>
                    <a href="#" class="btn btn-sm btn-primary">Ver Más</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabla-actividad">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Proveedor</th>
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

    <!-- Gráfico de Documentos Evaluados por Estado (Morris.js Donut) -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Documentos Evaluados por Estado</h4>
                    <div id="morris-donut-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <!-- Puedes añadir más gráficos o componentes aquí -->
    </div>
</div>

<?php
// Incluir el footer, que ya contiene los scripts comunes y el espacio para scripts específicos
require 'layout/footer.php';
?>
