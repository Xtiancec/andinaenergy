$(document).ready(function () {
    // Inicializar validación de formularios usando Bootstrap
    inicializarValidacionFormularios();

    // Cargar los datos personales si ya existen
    cargarDatosPersonales();

    // Manejar la selección del país en el formulario de registro y actualización
    $("#pais").on("change", function () {
        var pais = $(this).val();
        manejarCambioPais(pais, "register");
    });

    $("#paisUpdate").on("change", function () {
        var pais = $(this).val();
        manejarCambioPais(pais, "update");
    });

    // Formulario para registrar datos
    $("#formApplicantDetailsRegister").on("submit", function (e) {
        e.preventDefault();
        var form = this;
        if (!validarFormulario(form)) return;
        var formData = new FormData(form);

        enviarFormulario("../controlador/ApplicantDetailsController.php?op=guardar", formData, function (jsonResponse) {
            if (jsonResponse.status) {
                Toastify({
                    text: jsonResponse.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
                cargarDatosPersonales();
            } else {
                Toastify({
                    text: jsonResponse.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    });

    // Mostrar formulario de actualización al hacer clic en "Editar Perfil"
    $("#btnEditarPerfil").on("click", function () {
        $("#datosRegistrados").fadeOut(function () {
            $("#formApplicantDetailsUpdate").fadeIn();
            cargarDatosEnFormularioDeActualizacion();
        });
    });

    // Formulario para actualizar datos
    $("#formApplicantDetailsUpdate").on("submit", function (e) {
        e.preventDefault();
        var form = this;
        if (!validarFormulario(form)) return;
        var formData = new FormData(form);

        enviarFormulario("../controlador/ApplicantDetailsController.php?op=actualizar", formData, function (jsonResponse) {
            if (jsonResponse.status) {
                Toastify({
                    text: jsonResponse.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
                cargarDatosPersonales();
            } else {
                Toastify({
                    text: jsonResponse.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    });

    // Regresar al perfil
    $("#btnBackToProfile").on("click", function () {
        $("#formApplicantDetailsUpdate").fadeOut(function () {
            $("#datosRegistrados").fadeIn();
        });
    });

    // Función para manejar cambios en la selección de país
    function manejarCambioPais(pais, formType) {
        if (pais === "Perú") {
            // Cargar departamentos de Perú
            cargarDepartamentos(formType);
        } else {
            // Establecer "Otro" en departamento y provincia, y mostrar campo de dirección
            establecerOtro(formType);
        }
    }

    // Función para cargar departamentos de Perú
    function cargarDepartamentos(formType) {
        // Realizar una solicitud AJAX para obtener los departamentos de Perú
        $.ajax({
            url: "../controlador/ApplicantDetailsController.php?op=obtenerDepartamentos",
            type: "GET",
            dataType: "json",
            success: function (data) {
                var selectId = (formType === "register") ? "#departamento" : "#departamentoUpdate";
                $(selectId).empty().append('<option value="" disabled selected>Selecciona tu departamento</option>');
                $.each(data.departamentos, function (index, departamento) {
                    $(selectId).append('<option value="' + departamento + '">' + departamento + '</option>');
                });
                // Limpiar y deshabilitar el select de provincia hasta que se seleccione un departamento
                var provinciaId = (formType === "register") ? "#provincia" : "#provinciaUpdate";
                $(provinciaId).empty().append('<option value="" disabled selected>Selecciona tu provincia</option>').prop("disabled", true);

                // Mostrar el campo de dirección y ocultar el campo de dirección si se cambia el país
                var direccionId = (formType === "register") ? "#direccion" : "#direccionUpdate";
                $(direccionId).prop("disabled", true); // Dirección sigue siendo un campo de texto

                // Ocultar cualquier otro campo relacionado con "otro" si existiera
            },
            error: function () {
                Toastify({
                    text: "Error al cargar los departamentos.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });

        // Manejar cambio en el select de departamento
        var departamentoId = (formType === "register") ? "#departamento" : "#departamentoUpdate";
        $(departamentoId).off("change").on("change", function () {
            var departamento = $(this).val();
            if (departamento) {
                cargarProvincias(departamento, formType);
            }
        });
    }

    // Función para cargar provincias basadas en el departamento seleccionado
    function cargarProvincias(departamento, formType) {
        // Realizar una solicitud AJAX para obtener las provincias de Perú basadas en el departamento
        $.ajax({
            url: "../controlador/ApplicantDetailsController.php?op=obtenerProvincias&departamento=" + encodeURIComponent(departamento),
            type: "GET",
            dataType: "json",
            success: function (data) {
                var selectId = (formType === "register") ? "#provincia" : "#provinciaUpdate";
                $(selectId).empty().append('<option value="" disabled selected>Selecciona tu provincia</option>');
                $.each(data.provincias, function (index, provincia) {
                    $(selectId).append('<option value="' + provincia + '">' + provincia + '</option>');
                });
                // Habilitar el select de provincia
                $(selectId).prop("disabled", false);
            },
            error: function () {
                Toastify({
                    text: "Error al cargar las provincias.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });

        // Manejar cambio en el select de provincia
        var provinciaId = (formType === "register") ? "#provincia" : "#provinciaUpdate";
        $(provinciaId).off("change").on("change", function () {
            var provincia = $(this).val();
            if (provincia) {
                cargarDireccion(provincia, formType); // En lugar de cargar distritos, simplemente habilitar el campo de dirección
            }
        });
    }

    // Función para habilitar el campo de dirección basado en la provincia seleccionada
    function cargarDireccion(provincia, formType) {
        var direccionId = (formType === "register") ? "#direccion" : "#direccionUpdate";
        $(direccionId).prop("disabled", false);
    }

    // Función para establecer "Otro" en departamento y provincia, y habilitar el campo de dirección
    function establecerOtro(formType) {
        var departamentoId = (formType === "register") ? "#departamento" : "#departamentoUpdate";
        var provinciaId = (formType === "register") ? "#provincia" : "#provinciaUpdate";
        var direccionId = (formType === "register") ? "#direccion" : "#direccionUpdate";

        $(departamentoId).empty().append('<option value="Otro" selected>Otro</option>').prop("disabled", false);
        $(provinciaId).empty().append('<option value="Otro" selected>Otro</option>').prop("disabled", false);
        $(direccionId).val('').prop("disabled", false); // Habilitar el campo de dirección para que el usuario lo ingrese
    }

    // Función para cargar datos del usuario en el panel de detalles
    function cargarDatosPersonales() {
        $.ajax({
            url: "../controlador/ApplicantDetailsController.php?op=mostrar",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status === false) {
                    // Mostrar el formulario de registro si no hay datos
                    $("#formApplicantDetailsRegister").fadeIn();
                    $("#formApplicantDetailsUpdate").hide();
                    $("#datosRegistrados").hide();
                } else {
                    // Mostrar los datos y el botón de editar si ya existen datos
                    $("#formApplicantDetailsRegister").hide();
                    $("#formApplicantDetailsUpdate").hide();
                    $("#datosRegistrados").fadeIn();

                    // Llenar los datos registrados
                    $("#verPhone").text(data.data.phone);
                    $("#verEmergencyPhone").text(data.data.emergency_contact_phone || "No registrado");
                    $("#verContactoEmergencia").text(data.data.contacto_emergencia || "No registrado");
                    $("#verPais").text(data.data.pais);
                    $("#verDepartamento").text(data.data.departamento);
                    $("#verProvincia").text(data.data.provincia);
                    $("#verDireccion").text(data.data.direccion || "No registrada");
                    $("#verGender").text(data.data.gender);
                    $("#verBirthDate").text(data.data.birth_date);
                    $("#verMaritalStatus").text(data.data.marital_status);
                    $("#verChildrenCount").text(data.data.children_count);
                    $("#verNivelEstudio").text(data.data.education_level); // Cambiar 'nivel_estudio' por 'education_level'
                }
            },
            error: function () {
                Toastify({
                    text: "Error al cargar los datos personales.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }


    // Función para cargar los datos en el formulario de actualización
    function cargarDatosEnFormularioDeActualizacion() {
        $.ajax({
            url: "../controlador/ApplicantDetailsController.php?op=mostrar",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status === true) {
                    // Llenar los campos con los datos obtenidos
                    $("#education_levelUpdate").val(data.data.education_level);
                    $("#genderUpdate").val(data.data.gender);
                    $("#phoneUpdate").val(data.data.phone);
                    $("#emergency_contact_phoneUpdate").val(data.data.emergency_contact_phone);
                    $("#contacto_emergenciaUpdate").val(data.data.contacto_emergencia);
                    $("#paisUpdate").val(data.data.pais).trigger("change"); // Disparar el evento change para cargar departamentos/provincias
                    $("#children_countUpdate").val(data.data.children_count);
                    $("#birth_dateUpdate").val(data.data.birth_date);
                    $("#direccionUpdate").val(data.data.direccion || ''); // Rellenar la dirección
                } else {
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            },
            error: function () {
                Toastify({
                    text: "Error al cargar los datos para actualización.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }

    // Inicializar validación de formularios usando Bootstrap
    function inicializarValidacionFormularios() {
        var forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    }

    // Función para validar formulario
    function validarFormulario(form) {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
        return true;
    }

    // Función genérica para enviar formularios
    function enviarFormulario(url, formData, onSuccess) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                onSuccess(jsonResponse);
            },
            error: function () {
                Toastify({
                    text: "Error al procesar la solicitud.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }
});