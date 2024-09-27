<?php
// sidebar.php

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}

$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Función para verificar permisos
function hasAccess($required_roles = [], $required_types = [])
{
    global $user_role, $user_type;
    // Agregar depuración
    error_log("Verificando acceso: Rol actual: $user_role, Tipo actual: $user_type");

    // Verificar roles y tipos de usuario
    $role_access = empty($required_roles) || in_array($user_role, $required_roles);
    $type_access = empty($required_types) || in_array($user_type, $required_types);

    return $role_access && $type_access;
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
                                <li><a href="dashboardSuperadmin.php"><i class="mdi mdi-view-dashboard"></i> Dashboard Superadmin</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminrh'): ?>
                                <li><a href="dashboardAdminRH.php"><i class="mdi mdi-human-male-female"></i> Dashboard Admin RH</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'adminpr'): ?>
                                <li><a href="dashboardAdminPR.php"><i class="mdi mdi-truck"></i> Dashboard Admin PR</a></li>
                            <?php endif; ?>
                            <?php if ($user_role == 'user'): ?>
                                <li><a href="dashboardUser.php"><i class="mdi mdi-account"></i> Dashboard Usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-devider"></li>

                <!-- CONFIGURACIÓN -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-settings"></i>
                            <span class="hide-menu"><b>CONFIGURACIÓN</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">

                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="areaDashboard.php"><i class="mdi mdi-store"></i> Asignacion de Empresa, Areas y Puestos</a></li>
                                <li><a href="documentMandatory.php"><i class="mdi mdi-assignment"></i> Asignar Documentos a Puestos</a></li>
                            <?php endif; ?>

                            <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                                <li><a href="proveedores.php"><i class="mdi mdi-truck"></i> Proveedores</a></li>
                            <?php endif; ?>
                            <li><a href="documentNameSupplier.php"><i class="mdi mdi-file-document"></i> Documentos Proveedores</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- USUARIOS -->
                <?php if (hasAccess(['superadmin', 'adminrh', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-multiple"></i>
                            <span class="hide-menu"><b>USUARIOS</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="registrar_usuario.php"><i class="mdi mdi-account-plus"></i> Registrar Usuario</a></li>
                                <li><a href="actualizar_usuario.php"><i class="mdi mdi-account-edit"></i> Actualizar Usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- MODULO RECURSOS HUMANOS -->
                <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-multiple"></i>
                            <span class="hide-menu"><b>MODULO RECURSOS HUMANOS</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                                <li><a href="evaluarDocumentoPostulante.php"><i class="mdi mdi-account-plus"></i> Evaluar Documentos Postulantes</a></li>
                                <li><a href="evaluarDocumentoUsuario.php"><i class="mdi mdi-account-edit"></i> Evaluar Documentos Usuarios</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- MODULO PROCURA -->
                <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-truck-delivery"></i>
                            <span class="hide-menu"><b>MODULO PROCURA</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <?php if (hasAccess(['superadmin', 'adminpr'])): ?>
                                <li><a href="evaluarDocumentoEmpresa.php"><i class="mdi mdi-account-plus"></i> Evaluar Documentos Empresas</a></li>
                                <li><a href="actualizar_usuario.php"><i class="mdi mdi-account-edit"></i> Actualizar Usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-devider"></li>

                <!-- MENÚ CONDICIONAL SEGÚN TIPO DE USUARIO -->
                <?php if ($user_type == 'applicant'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-box"></i>
                            <span class="hide-menu"><b>POSTULANTES</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="dashboardApplicant.php"><i class="mdi mdi-view-dashboard"></i> Dashboard</a></li>
                            <li><a href="applicant_details.php"><i class="mdi mdi-account-details"></i> Detalles del Postulante</a></li>
                            <li><a href="experience.php"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>
                            <li><a href="mostrar_experiencia.php"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>

                            <li><a href="edit_experience.php"><i class="mdi mdi-file"></i> Documentos del Postulante</a></li>
                        </ul>
                    </li>
                <?php elseif ($user_type == 'supplier'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-truck-delivery"></i>
                            <span class="hide-menu"><b>PROVEEDORES</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="supplier_dashboard.php"><i class="mdi mdi-view-dashboard"></i> Dashboard Proveedor</a></li>
                            <li><a href="supplierDetails.php"><i class="mdi mdi-account-box"></i> Detalles del Proveedor</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- APROBACIÓN/RECHAZO DE DOCUMENTOS (Administradores) -->
                <?php if (hasAccess(['superadmin', 'adminrh'])): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-clipboard-check"></i>
                            <span class="hide-menu"><b>APROBACIÓN/RECHAZO DE DOCUMENTOS</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="postulantes_pendientes.php"><i class="mdi mdi-account-clock"></i> Postulantes Pendientes</a></li>
                            <li><a href="postulantes_aprobados.php"><i class="mdi mdi-account-check"></i> Postulantes Aprobados</a></li>
                            <li><a href="postulantes_rechazados.php"><i class="mdi mdi-account-remove"></i> Postulantes Rechazados</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- CERRAR SESIÓN -->
                <li class="nav-devider"></li>
                <li>
                    <a href="logout.php" class="waves-effect waves-dark">
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