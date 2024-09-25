// scripts/login_supplier.js

$(document).ready(function () {
    $("#frmAcceso").on("submit", function (e) {
        e.preventDefault(); // Evitar el envío normal del formulario

        let username = $("#logina").val().trim();
        let password = $("#clavea").val().trim();

        if (username === "" || password === "") {
            $("#login-error-message").html("Por favor, rellena todos los campos.");
            return;
        }

        $.ajax({
            url: "../controlador/LoginSupplierController.php?op=verificar",
            method: "POST",
            data: { username: username, password: password },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    // Redirigir al dashboard del proveedor
                    window.location.href = "dashboardSupplier.php"; // Asegúrate de que esta es la URL correcta
                } else {
                    // Mostrar mensaje de error usando SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || "Usuario o contraseña incorrectos.",
                    });
                    // También puedes mostrar el mensaje en el div
                    // $("#login-error-message").html(data.message || "Usuario o contraseña incorrectos.");
                }
            },
            error: function (xhr, status, error) {
                // Manejo de errores en el servidor usando SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "Hubo un problema en el servidor. Por favor, inténtalo de nuevo.",
                });
                console.error(xhr.responseText); // Para depuración
            }
        });
    });

    // Manejo del formulario de recuperación de contraseña
    $("#recoverform").on("submit", function (e) {
        e.preventDefault(); // Evitar el envío normal del formulario

        let email = $("#recover-email").val().trim();

        if (email === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: "Por favor, ingresa tu correo electrónico.",
            });
            return;
        }

        // Aquí puedes agregar la lógica para manejar la recuperación de contraseña
        // Por ejemplo, enviar una solicitud AJAX a un controlador específico
        // Por ahora, mostraremos un mensaje de éxito ficticio

        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: "Si el correo electrónico está registrado, recibirás las instrucciones para restablecer tu contraseña.",
        });

        // Opcional: Limpiar el campo de correo electrónico y mostrar el formulario de login nuevamente
        $("#recover-email").val("");
        $("#recoverform").slideUp();
        $("#loginform").slideDown();
    });
});
