// scripts/login.js

$(document).ready(function () {
    $("#frmAcceso").on("submit", function (e) {
        e.preventDefault(); // Evitar el envío normal del formulario

        let username = $("#username").val().trim();
        let password = $("#password").val().trim(); // Asegúrate de que el ID coincide con el HTML

        // Validación básica
        if (username === "" || password === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campos Vacíos',
                text: 'Por favor, rellena todos los campos.',
            });
            return;
        }

        // Enviar la solicitud AJAX
        $.ajax({
            url: "../controlador/LoginController.php?op=verificar",
            method: "POST",
            data: { username: username, password: password }, // Enviar 'password'
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    // Redireccionar según el rol y tipo
                    if (data.type === 'user') {
                        switch (data.role) {
                            case "superadmin":
                                window.location.href = "dashboardSuperadmin.php"; 
                                break;
                            case "adminrh":
                                window.location.href = "dashboardAdminRH.php"; 
                                break;
                            case "adminpr":
                                window.location.href = "dashboardAdminPR.php"; 
                                break;
                            case "user":
                                window.location.href = "dashboardUser.php"; 
                                break;
                            default:
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: "Rol no reconocido.",
                                });
                        }
                    } else if (data.type === 'applicant') {
                        window.location.href = "dashboardApplicant.php"; 
                    } else if (data.type === 'supplier') {
                        window.location.href = "dashboardSupplier.php"; 
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: "Tipo de usuario no reconocido.",
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Autenticación',
                        text: data.message || "Usuario o contraseña incorrectos.",
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: "Hubo un problema en el servidor. Por favor, inténtalo de nuevo.",
                });
                console.error("Error en la solicitud AJAX: ", status, error);
                console.error("Respuesta del servidor: ", xhr.responseText);
            },
            complete: function () {
                // Rehabilitar el botón o ocultar el preloader si es necesario
            }
        });
    });
});
