<?php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id']) && !isset($_SESSION['applicant_id']) && !isset($_SESSION['supplier_id'])) {
    header("Location: ../login.html");
    exit();
}

// Verificar el rol del usuario
if (isset($_SESSION['user_role'])) {
    $user_role = $_SESSION['user_role'];
    if ($user_role !== 'superadmin') {
        echo "No tienes permiso para acceder a esta página.";
        exit();
    }
} else {
    echo "No tienes permiso para acceder a esta página.";
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
            <li class="breadcrumb-item">Super Administrador</li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
