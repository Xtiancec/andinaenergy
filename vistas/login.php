<?php
// login.php

session_start();

// Si el usuario ya ha iniciado sesión, redirígelo según su tipo y rol
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'user') {
        switch ($_SESSION['user_role']) {
            case "superadmin":
                header("Location: superadmin_dashboard.php");
                break;
            case "adminrh":
                header("Location: adminrh_dashboard.php");
                break;
            case "adminpr":
                header("Location: adminpr_dashboard.php");
                break;
            case "user":
                header("Location: user_dashboard.php");
                break;
            default:
                // Rol no reconocido, cerrar sesión
                header("Location: logout.php");
        }
        exit();
    } else if ($_SESSION['user_type'] === 'applicant') {
        header("Location: dashboardApplicant.php");
        exit();
    } else if ($_SESSION['user_type'] === 'supplier') {
        header("Location: supplier_dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- [Incluye aquí el mismo <head> que en la plantilla proporcionada] -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Responsive a ancho de pantalla -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login de Usuarios">
    <meta name="author" content="Tu Nombre">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../app/template/images/favicon.png">
    <title>Login Usuarios - ANDINA</title>
    <!-- Bootstrap Core CSS -->
    <link href="../app/template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Page CSS -->
    <link href="../app/template/css/pages/login-register-lock.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../app/template/css/style.css" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="../app/template/css/colors/default-dark.css" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- SweetAlert2 para mensajes elegantes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery, Popper.js y Bootstrap JS -->
    <script src="../app/template/plugins/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha512-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGaNfTT4b5gXq1Ua37gHmqZVJ9lOgqTFw/FtIpP9r0CjCBwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../app/template/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- Tu script personalizado -->
    <script src="scripts/login.js"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">ANDINA</p>
        </div>
    </div>
    <!-- End Preloader -->

    <!-- Main Wrapper -->
    <section id="wrapper" class="login-register login-sidebar" style="background-image:url(../app/template/images/background/login-register.jpg);">
        <div class="login-box card">
            <div class="card-body">
                <!-- Formulario de Login -->
                <form class="form-horizontal form-material" id="frmAcceso" method="post">
                    <a href="javascript:void(0)" class="text-center db">
                        <img src="../app/template/images/logo-icon.png" alt="Home" />
                        <br />
                        <img src="../app/template/images/logo-text.png" alt="Home" />
                    </a>
                    <div class="form-group m-t-40">
                        <div class="col-xs-12">
                            <input class="form-control" id="username" name="username" type="text" required placeholder="Usuario" autocomplete="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" id="clavea" name="clavea" type="password" required placeholder="Contraseña" autocomplete="current-password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary pull-left p-t-0">
                                <input id="checkbox-signup" type="checkbox" class="filled-in chk-col-light-blue">
                                <label for="checkbox-signup"> Remember me </label>
                            </div>
                            <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right">
                                <i class="fa fa-lock m-r-5"></i> Forgot pwd?
                            </a>
                        </div>
                    </div>
                    <div id="login-error-message" class="text-danger text-center mb-3"></div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase btn-rounded" type="submit">Ingresar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                            <div class="social">
                                <a href="javascript:void(0)" class="btn btn-facebook" data-toggle="tooltip" title="Login with Facebook">
                                    <i aria-hidden="true" class="fa fa-facebook"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="Login with Google">
                                    <i aria-hidden="true" class="fa fa-google-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            No tienes una cuenta? <a href="pages-register2.php" class="text-primary m-l-5"><b>Regístrate</b></a>
                        </div>
                    </div>
                </form>
                <!-- Formulario de Recuperación de Contraseña -->
                <form class="form-horizontal" id="recoverform" method="post">
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>Recuperar Contraseña</h3>
                            <p class="text-muted">Ingresa tu correo electrónico y recibirás las instrucciones para restablecer tu contraseña.</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" id="recover-email" name="recover-email" type="email" required placeholder="Correo Electrónico">
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Restablecer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- End Main Wrapper -->

    <!-- Custom JavaScript para Preloader y Toggle de Formularios -->
    <script type="text/javascript">
        $(function () {
            $(".preloader").fadeOut();
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

        // Toggle entre el formulario de login y recuperación de contraseña
        $('#to-recover').on("click", function () {
            $("#frmAcceso").slideUp();
            $("#recoverform").slideDown();
        });
    </script>

</body>

</html>
