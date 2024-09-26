<?php
// adminhr_dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión y es un adminrh (Administrador de Recursos Humanos)
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminrh']) // Permitir 'superadmin' o 'adminrh'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Definir scripts específicos para esta página
$page_specific_scripts = '
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Tu script personalizado -->
<script src="scripts/dashboardAdminrh.js"></script>
';

// Incluir los layouts
require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Contenido del Dashboard del Administrador de Recursos Humanos -->
<div class="container-fluid">
    <!-- Título y Breadcrumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                <i class="fas fa-chart-line"></i> Dashboard AdminHR
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="escritorio.php">Inicio</a></li>
                <li class="breadcrumb-item">Administrador de Recursos Humanos</li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <!-- Total de Usuarios -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Usuarios Totales</h5>
                            <h3 class="font-weight-bold" id="total-users">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total de Candidatos (Applicants) -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Candidatos Totales</h5>
                            <h3 class="font-weight-bold" id="total-applicants">0</h3>
                        </div>
                        <div>
                            <i class="fas fa-user-tie fa-3x opacity-75"></i>
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

    <!-- Gráfico de Usuarios Registrados por Mes -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Usuarios Registrados por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="users-chart" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- Gráfico de Candidatos Registrados por Mes -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Candidatos Registrados por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="applicants-chart" height="150"></canvas>
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
                                    <th>Usuario/Candidato</th>
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

    <!-- Gráfico de Documentos Evaluados por Estado (Usuarios) -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documentos Evaluados por Estado (Usuarios)</h5>
                </div>
                <div class="card-body">
                    <canvas id="documents-status-chart-users" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- Gráfico de Documentos Evaluados por Estado (Candidatos) -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Documentos Evaluados por Estado (Candidatos)</h5>
                </div>
                <div class="card-body">
                    <canvas id="documents-status-chart-applicants" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico Adicional: Usuarios Activos vs Inactivos -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Usuarios Activos vs Inactivos</h5>
                </div>
                <div class="card-body">
                    <canvas id="users-status-chart" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- Puedes añadir más gráficos o componentes aquí -->
    </div>

    <!-- Nuevos Gráficos Integrados -->
    <div class="row mt-4">
        <!-- Gráfico de Turnover de Empleados por Mes (eCharts) -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Turnover de Empleados por Mes</h5>
                </div>
                <div class="card-body">
                    <div id="turnover-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <!-- Gráfico de Distribución de Empleados por Departamento (Morris.js Donut) -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Distribución de Empleados por Departamento</h5>
                </div>
                <div class="card-body">
                    <div id="employee-department-donut" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor para almacenar dataHash -->
<div id="adminhrDashboardData" data-data-hash=""></div>

<?php
// Incluir el footer, que ya contiene los scripts comunes y el espacio para scripts específicos
require 'layout/footer.php';
?>
