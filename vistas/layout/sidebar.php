<?php
// sidebar.php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}

$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Función para verificar permisos
if (!function_exists('hasAccess')) {
    function hasAccess($required_roles = [], $required_types = [])
    {
        global $user_role, $user_type;

        // Verificar roles y tipos de usuario
        $role_access = empty($required_roles) || in_array($user_role, $required_roles);
        $type_access = empty($required_types) || in_array($user_type, $required_types);

        return $role_access && $type_access;
    }
}
?>
<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- DASHBOARD -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr', 'user'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span class="hide-menu"><b>DASHBOARD</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if ($user_role == 'superadmin'): ?>
                                <li><a href="/dashboardSuperadmin"><i class="mdi mdi-view-dashboard"></i> Dashboard Superadmin</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminrh'): ?>
                                <li><a href="/dashboardAdminRH"><i class="mdi mdi-human-male-female"></i> Dashboard Admin RH</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminpr'): ?>
                                <li><a href="/dashboardAdminPR"><i class="mdi mdi-truck"></i> Dashboard Admin PR</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'user'): ?>
                                <li><a href="/dashboardUser"><i class="mdi mdi-account"></i> Dashboard Usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Resto del menú, asegurándote de que los enlaces utilizan rutas amigables -->
                <!-- Por ejemplo: -->
                <li>
                    <a href="/logout" class="waves-effect waves-dark">
                        <i class="mdi mdi-logout"></i>
                        <span class="hide-menu"><b>Cerrar Sesión</b></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>


<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->

        <div class="">
            <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                <i class="ti-settings text-white"></i>
            </button>
        </div>