<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Capturar la URL reescrita
$url = isset($_GET['url']) ? $_GET['url'] : null;

// Si no hay una URL específica, cargar la página según el rol del usuario o el login por defecto
if (!$url) {
    if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_role'])) {
        header("Location: /login");
        exit();
    }

    // Manejo de roles de usuario
    switch ($_SESSION['user_type']) {
        case 'applicant':
            if ($_SESSION['user_role'] === 'postulante') {
                $page_content = 'dashboardApplicant.php';
            } else {
                $page_content = 'login_postulantes.php';
            }
            break;

        case 'supplier':
            if ($_SESSION['user_role'] === 'proveedor') {
                $page_content = 'dashboardSupplier.php';
            } else {
                $page_content = 'login_supplier.php';
            }
            break;

        case 'user':
            switch ($_SESSION['user_role']) {
                case 'superadmin':
                    $page_content = 'dashboardSuperadmin.php';
                    break;
                case 'adminrh':
                    $page_content = 'dashboardAdminRH.php';
                    break;
                case 'adminpr':
                    $page_content = 'dashboardAdminPR.php';
                    break;
                case 'user':
                    $page_content = 'dashboardUser.php';
                    break;
                default:
                    $page_content = 'login.php';
            }
            break;

        default:
            header("Location: /login");
            exit();
    }
} else {
    // Manejo de URLs amigables
    $url_parts = explode('/', $url);

    switch ($url_parts[0]) {
        case 'login_postulantes':
            $page_content = 'login_postulantes.php';
            break;
        case 'dashboardApplicant':
            $page_content = 'dashboardApplicant.php';
            break;
        case 'applicant_details':
            $page_content = 'applicant_details.php';
            break;
        case 'experience':
            $page_content = 'experience.php';
            break;
        case 'edit_experience':
            $page_content = 'edit_experience.php';
            break;
        case 'mostrar_experiencia':
            $page_content = 'mostrar_experiencia.php';
            break;
        // Agrega más casos según tus rutas
        default:
            $page_content = 'acceso_denegado.php'; // Página de error 404 personalizada
            break;
    }
}

// Incluir la vista seleccionada
if (!empty($page_content)) {
    include "vistas/$page_content";
} else {
    include "vistas/acceso_denegado.php"; // Página de error 404 personalizada
}
