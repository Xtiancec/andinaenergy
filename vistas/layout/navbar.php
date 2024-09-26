<?php
// navbar.php

// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar variables por defecto
$name = 'Usuario';
$display_role = '';

// Verificar si el usuario ha iniciado sesión y determinar el tipo y rol
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

    switch ($user_type) {
        case 'user':
            // Para usuarios normales, obtener el nombre completo
            $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Usuario';
            $display_role = ucfirst(htmlspecialchars($user_role)); // 'Superadmin', 'Adminrh', etc.
            break;
        case 'applicant':
            // Para postulantes, obtener el nombre
            $name = isset($_SESSION['names']) ? $_SESSION['names'] : 'Postulante';
            $display_role = 'Postulante';
            break;
        case 'supplier':
            // Para proveedores, obtener el nombre de la empresa
            $name = isset($_SESSION['companyName']) ? $_SESSION['companyName'] : 'Proveedor';
            $display_role = 'Proveedor';
            break;
        default:
            $name = 'Usuario';
            $display_role = '';
    }
}
?>

<!-- Inicio del HTML de la Navbar -->
<nav class="navbar top-navbar navbar-expand-md navbar-light">
    <div class="navbar-header">
        <a class="navbar-brand" href="escritorio.php">
            <b><img src="app/template/images/icono-andina.png" width="50" height="50" alt="homepage" class="dark-logo" /></b>
            <span><img src="app/template/images/texto-andina.png" width="150" height="50" alt="homepage" class="dark-logo" /></span>
        </a>
    </div>

    <div class="navbar-collapse">
        <ul class="navbar-nav my-lg-0">
            <li class="nav-item dropdown">
                <?php if (isset($_SESSION['user_type'])): ?>
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Bienvenido, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($display_role); ?>)
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated flipInY" aria-labelledby="navbarDropdown">
                        <a href="logout.php" class="dropdown-item">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>




</header>