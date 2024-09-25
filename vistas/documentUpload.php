<?php
// documents_upload.php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Obtener el user_id desde la sesión
$user_id = $_SESSION['user_id'];

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<!-- Definir user_id en JavaScript -->
<script>
    var user_id = <?php echo json_encode($user_id); ?>;
</script>

<div class="row page-titles">
    <div class="col-md-6 col-sm-12">
        <h3 class="text-themecolor">Subir Documentos</h3>
    </div>
    <div class="col-md-6 col-sm-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-md-end">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item">Documentos</li>
                <li class="breadcrumb-item active" aria-current="page">Subir Documentos</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <!-- Barra de progreso para documentos obligatorios -->
    <h4>Progreso de Documentos Obligatorios</h4>
    <div class="progress mb-4" style="height: 25px;">
        <div id="progressObligatorios" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0% Completado
        </div>
    </div>

    <!-- Barra de progreso para documentos opcionales -->
    <h4>Progreso de Documentos Opcionales</h4>
    <div class="progress mb-4" style="height: 25px;">
        <div id="progressOpcionales" class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0% Completado
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="documentTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="obligatorios-tab" data-toggle="tab" href="#obligatorios" role="tab" aria-controls="obligatorios" aria-selected="true">Documentos Obligatorios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="opcionales-tab" data-toggle="tab" href="#opcionales" role="tab" aria-controls="opcionales" aria-selected="false">Documentos Opcionales</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content" id="documentTabsContent">
        <div class="tab-pane fade show active" id="obligatorios" role="tabpanel" aria-labelledby="obligatorios-tab">
            <div class="row" id="document-container-obligatorios">
                <!-- Aquí se cargarán los cards de los documentos obligatorios -->
            </div>
        </div>
        <div class="tab-pane fade" id="opcionales" role="tabpanel" aria-labelledby="opcionales-tab">
            <div class="row" id="document-container-opcionales">
                <!-- Aquí se cargarán los cards de los documentos opcionales -->
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Tu archivo JavaScript -->
<script src="scripts/documentUpload.js"></script>
