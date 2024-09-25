// scripts/login_postulante.js

$(document).ready(function () {
    $("#frmAccesoPostulante").on("submit", function (e) {
        e.preventDefault(); // Evitar el envío normal del formulario

        let username = $("#loginp").val().trim();
        let password = $("#clavep").val().trim();

        // Validación básica
        if (username === "" || password === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campos Vacíos',
                text: 'Por favor, rellena todos los campos.',
            });
            return;
        }

        // Mostrar preloader o deshabilitar el botón si es necesario
        // Por ejemplo:
        // $("#loginp-button").prop('disabled', true);

        $.ajax({
            url: "../controlador/LoginPostulanteController.php?op=verificar",
            method: "POST",
            data: { username: username, password: password },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    window.location.href = "dashboardApplicant.php";
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
                // Por ejemplo:
                // $("#loginp-button").prop('disabled', false);
            }
        });
    });
});
