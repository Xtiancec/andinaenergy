$(document).ready(function () {
    init();
});

// Variable global para la tabla
let tabla;

// Función de inicialización
function init() {
    // Inicializar la validación de formularios
    inicializarValidacionFormularios();

    // Inicializar DataTable
    listar();

    // Manejar el evento de mostrar el modal de agregar postulante
    $('#formularioregistros').on('show.bs.modal', function (e) {
        $('#formulario')[0].reset(); // Limpia el formulario
        $('#formulario').removeClass('was-validated'); // Resetear la validación
        cargarEmpresas("#company_id");
        $('#area_id').html('<option value="">Seleccione un Área</option>');
        $('#job_id').html('<option value="">Seleccione un Puesto de Trabajo</option>');
    });

    // Manejar el evento de mostrar el modal de actualizar postulante
    $('#formularioActualizar').on('show.bs.modal', function (e) {
        $('#formActualizar')[0].reset(); // Limpia el formulario
        $('#formActualizar').removeClass('was-validated'); // Resetear la validación
    });

    // Guardar postulante al enviar el formulario
    $("#formulario").on("submit", function (e) {
        guardar(e);
    });

    // Actualizar postulante al enviar el formulario
    $("#formActualizar").on("submit", function (e) {
        actualizar(e);
    });

    // Consultar DNI al cambiar el valor del campo DNI (agregar)
    $("#username").on("change", function () {
        var dni = $(this).val();
        if (/^\d{8}$/.test(dni)) {
            consultarDNI(dni);
        } else {
            mostrarErrorDNI("DNI inválido. Debe contener 8 dígitos.");
            limpiarCamposDNI();
        }
    });

    // Consultar DNI al cambiar el valor del campo DNI (actualizar)
    $("#usernameUpdate").on("change", function () {
        var dni = $(this).val();
        if (/^\d{8}$/.test(dni)) {
            consultarDNIUpdate(dni);
        } else {
            mostrarErrorDNIUpdate("DNI inválido. Debe contener 8 dígitos.");
            limpiarCamposDNIUpdate();
        }
    });

    // Manejar el cambio en el select de empresa para agregar postulante
    $("#company_id").on("change", function () {
        var companyId = $(this).val();
        if (companyId) {
            cargarAreasPorEmpresa(companyId, "#area_id");
        } else {
            $('#area_id').html('<option value="">Seleccione un Área</option>');
            $('#job_id').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        }
    });

    // Manejar el cambio en el select de empresa para actualizar postulante
    $("#company_idUpdate").on("change", function () {
        var companyId = $(this).val();
        if (companyId) {
            cargarAreasPorEmpresa(companyId, "#area_idUpdate");
        } else {
            $('#area_idUpdate').html('<option value="">Seleccione un Área</option>');
            $('#job_idUpdate').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        }
    });

    // Manejar el cambio en el select de área para agregar postulante
    $("#area_id").on("change", function () {
        var areaId = $(this).val();
        if (areaId) {
            cargarPuestosPorArea(areaId, "#job_id");
        } else {
            $('#job_id').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        }
    });

    // Manejar el cambio en el select de área para actualizar postulante
    $("#area_idUpdate").on("change", function () {
        var areaId = $(this).val();
        if (areaId) {
            cargarPuestosPorArea(areaId, "#job_idUpdate");
        } else {
            $('#job_idUpdate').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        }
    });
}

// Inicializar validación de formularios usando Bootstrap
function inicializarValidacionFormularios() {
    // Validación para el formulario de agregar
    var formularioAgregar = document.getElementById('formulario');
    formularioAgregar.addEventListener('submit', function (event) {
        if (formularioAgregar.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        formularioAgregar.classList.add('was-validated');
    }, false);

    // Validación para el formulario de actualizar
    var formularioActualizar = document.getElementById('formActualizar');
    formularioActualizar.addEventListener('submit', function (event) {
        if (formularioActualizar.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        formularioActualizar.classList.add('was-validated');
    }, false);
}

// Función para listar postulantes en la tabla
function listar() {
    tabla = $("#tbllistado").DataTable({
        "ajax": {
            url: "../controlador/ApplicantController.php?op=listar",
            type: "GET",
            dataType: "json",
            dataSrc: function (json) {
                // Depuración: Verificar la estructura de los datos recibidos
                console.log("Datos recibidos:", json);
                return json.data;
            },
            error: function (e) {
                console.error("Error al cargar los datos:", e.responseText);
                mostrarToast("Error al cargar los datos de los postulantes.", "error");
            }
        },
        "columns": [
            { "data": "ID" },
            { "data": "DNI" },
            { "data": "Email" },
            { "data": "Nombre Completo" },
            { "data": "Empresa" },
            { "data": "Área" },
            { "data": "Puesto" },
            { 
                "data": "Estado",
                "render": function (data, type, row) {
                    return data; // Ya contiene el HTML
                }
            },
            { 
                "data": "Opciones",
                "orderable": false,
                "searchable": false,
                "render": function (data, type, row) {
                    return data; // Ya contiene los botones HTML
                }
            }
        ],
        "order": [[0, "desc"]],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" // Cambiado a HTTPS
        },
        "pageLength": 10,
        "responsive": true,
        "deferRender": true // Mejora el rendimiento con grandes conjuntos de datos
    });
}

// Función para guardar postulante
function guardar(e) {
    e.preventDefault();

    // Verificar si el formulario es válido
    let form = document.getElementById('formulario');
    if (form.checkValidity() === false) {
        e.stopPropagation();
        form.classList.add('was-validated');
        return;
    }

    var formData = new FormData($("#formulario")[0]);

    // Deshabilitar el botón de guardar para prevenir múltiples envíos
    $("#formulario button[type='submit']").prop('disabled', true);

    $.ajax({
        url: "../controlador/ApplicantController.php?op=guardar",
        type: "POST",
        data: formData,
        dataType: "json", // Esperar respuesta en formato JSON
        contentType: false,
        processData: false,
        success: function (response) {
            // Habilitar el botón de guardar nuevamente
            $("#formulario button[type='submit']").prop('disabled', false);

            if (response.status === "success") {
                $('#formularioregistros').modal('hide');
                mostrarToast(response.message, "success");
                $('#tbllistado').DataTable().ajax.reload();
            } else if (response.status === "warning") {
                // Caso en que el postulante se registró pero no se pudo enviar el correo
                mostrarToast(response.message, "warning");
                $('#formularioregistros').modal('hide');
                $('#tbllistado').DataTable().ajax.reload();
            } else {
                // Error, como DNI duplicado
                mostrarToast(response.message, "error");
            }
        },
        error: function (e) {
            // Habilitar el botón de guardar nuevamente
            $("#formulario button[type='submit']").prop('disabled', false);

            mostrarToast("Error al procesar la solicitud.", "error");
            console.error("Error en la solicitud AJAX:", e.responseText);
        }
    });
}

// Función para actualizar postulante
function actualizar(e) {
    e.preventDefault();

    // Verificar si el formulario es válido
    let form = document.getElementById('formActualizar');
    if (form.checkValidity() === false) {
        e.stopPropagation();
        form.classList.add('was-validated');
        return;
    }

    var formData = new FormData($("#formActualizar")[0]);

    // Deshabilitar el botón de guardar cambios para prevenir múltiples envíos
    $("#formActualizar button[type='submit']").prop('disabled', true);

    $.ajax({
        url: "../controlador/ApplicantController.php?op=editar",
        type: "POST",
        data: formData,
        dataType: "json", // Esperar respuesta en formato JSON
        contentType: false,
        processData: false,
        success: function (response) {
            // Habilitar el botón de guardar cambios nuevamente
            $("#formActualizar button[type='submit']").prop('disabled', false);

            if (response.status === "success") {
                $('#formularioActualizar').modal('hide');
                mostrarToast(response.message, "success");
                $('#tbllistado').DataTable().ajax.reload();
            } else if (response.status === "warning") {
                // Caso en que el postulante se actualizó pero no se pudo enviar el correo
                mostrarToast(response.message, "warning");
                $('#formularioActualizar').modal('hide');
                $('#tbllistado').DataTable().ajax.reload();
            } else {
                // Error, como DNI duplicado
                mostrarToast(response.message, "error");
            }
        },
        error: function (e) {
            // Habilitar el botón de guardar cambios nuevamente
            $("#formActualizar button[type='submit']").prop('disabled', false);

            mostrarToast("Error al procesar la solicitud.", "error");
            console.error("Error en la solicitud AJAX:", e.responseText);
        }
    });
}

// Función para desactivar postulante
function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡El postulante será desactivado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../controlador/ApplicantController.php?op=desactivar',  function (response) {
                if (response.status === "success") {
                    Swal.fire('Desactivado', response.message, 'success');
                    tabla.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }, "json").fail(function () {
                Swal.fire('Error', 'Error en la solicitud.', 'error');
            });
        }
    });
}

// Función para activar postulante
function activar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡El postulante será activado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../controlador/ApplicantController.php?op=activar',  function (response) {
                if (response.status === "success") {
                    Swal.fire('Activado', response.message, 'success');
                    tabla.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }, "json").fail(function () {
                Swal.fire('Error', 'Error en la solicitud.', 'error');
            });
        }
    });
}

// Función para mostrar los datos de un postulante en el formulario de actualización
function mostrar(id) {
    $.ajax({
        url: "../controlador/ApplicantController.php?op=mostrar",
        type: "POST",
        data: { id: id},
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                var data = response.data;
                $("#formularioActualizar").modal("show");

                cargarEmpresas("#company_idUpdate", data.company_id);
                cargarAreasPorEmpresa(data.company_id, "#area_idUpdate", data.area_id);
                cargarPuestosPorArea(data.area_id, "#job_idUpdate", data.job_id);

                $("#idUpdate").val(data.id);
                $("#usernameUpdate").val(data.username);
                $("#emailUpdate").val(data.email);
                $("#lastnameUpdate").val(data.lastname);
                $("#surnameUpdate").val(data.surname);
                $("#namesUpdate").val(data.names);
            } else {
                mostrarToast(response.message, "error");
            }
        },
        error: function (e) {
            mostrarToast("Error al cargar los datos del postulante.", "error");
            console.error("Error en la solicitud AJAX:", e.responseText);
        }
    });
}

// Función para cargar las empresas en el select
function cargarEmpresas(selector, selectedId = null) {
    $.ajax({
        url: "../controlador/ApplicantController.php?op=listarEmpresas",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let options = "<option value=''>Seleccione una Empresa</option>";
                response.data.forEach(empresa => {
                    options += `<option value="${empresa.id}" ${selectedId == empresa.id ? 'selected' : ''}>${empresa.company_name}</option>`;
                });
                $(selector).html(options);
            } else {
                console.error("Error cargando empresas:", response.message);
                mostrarToast("Error al cargar las empresas.", "error");
            }
        },
        error: function (e) {
            console.error("Error cargando empresas:", e.responseText);
            mostrarToast("Error al cargar las empresas.", "error");
        }
    });
}

// Función para cargar áreas por empresa
function cargarAreasPorEmpresa(companyId, selector, selectedAreaId = null) {
    if (!companyId) {
        $(selector).html('<option value="">Seleccione un Área</option>');
        if (selector === "#area_idUpdate") {
            $('#job_idUpdate').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        } else {
            $('#job_id').html('<option value="">Seleccione un Puesto de Trabajo</option>');
        }
        return;
    }

    $.ajax({
        url: "../controlador/ApplicantController.php?op=listarAreasPorEmpresa",
        type: "POST",
        data: { company_id: companyId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let options = "<option value=''>Seleccione un Área</option>";
                response.data.forEach(area => {
                    options += `<option value="${area.id}" ${selectedAreaId == area.id ? 'selected' : ''}>${area.area_name}</option>`;
                });
                $(selector).html(options);
                // Resetear los puestos de trabajo
                if (selector === "#area_id") {
                    $('#job_id').html('<option value="">Seleccione un Puesto de Trabajo</option>');
                } else if (selector === "#area_idUpdate") {
                    $('#job_idUpdate').html('<option value="">Seleccione un Puesto de Trabajo</option>');
                }
            } else {
                console.error("Error cargando áreas:", response.message);
                mostrarToast("Error al cargar las áreas.", "error");
            }
        },
        error: function (e) {
            console.error("Error cargando áreas:", e.responseText);
            mostrarToast("Error al cargar las áreas.", "error");
        }
    });
}

// Función para cargar puestos por área
function cargarPuestosPorArea(areaId, selector, selectedJobId = null) {
    if (!areaId) {
        $(selector).html('<option value="">Seleccione un Puesto de Trabajo</option>');
        return;
    }

    $.ajax({
        url: "../controlador/ApplicantController.php?op=listarPuestosPorArea",
        type: "POST",
        data: { area_id: areaId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let options = "<option value=''>Seleccione un Puesto de Trabajo</option>";
                response.data.forEach(puesto => {
                    options += `<option value="${puesto.id}" ${selectedJobId == puesto.id ? 'selected' : ''}>${puesto.position_name}</option>`;
                });
                $(selector).html(options);
            } else {
                console.error("Error cargando puestos de trabajo:", response.message);
                mostrarToast("Error al cargar los puestos de trabajo.", "error");
            }
        },
        error: function (e) {
            console.error("Error cargando puestos de trabajo:", e.responseText);
            mostrarToast("Error al cargar los puestos de trabajo.", "error");
        }
    });
}

// Función para consultar DNI al agregar
function consultarDNI(dni) {
    $.ajax({
        url: 'proxy.php?dni=' + dni,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response && response.apellidoPaterno && response.apellidoMaterno && response.nombres) {
                $("#lastname").val(response.apellidoPaterno);
                $("#surname").val(response.apellidoMaterno);
                $("#names").val(response.nombres);
            } else {
                mostrarErrorDNI("No se encontraron datos para el DNI ingresado.");
                limpiarCamposDNI();
            }
        },
        error: function (e) {
            mostrarErrorDNI("Error al consultar el DNI.");
            console.error("Error en la consulta DNI:", e.responseText);
        }
    });
}

// Función para consultar DNI al actualizar
function consultarDNIUpdate(dni) {
    $.ajax({
        url: 'proxy.php?dni=' + dni,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response && response.apellidoPaterno && response.apellidoMaterno && response.nombres) {
                $("#lastnameUpdate").val(response.apellidoPaterno);
                $("#surnameUpdate").val(response.apellidoMaterno);
                $("#namesUpdate").val(response.nombres);
            } else {
                mostrarErrorDNIUpdate("No se encontraron datos para el DNI ingresado.");
                limpiarCamposDNIUpdate();
            }
        },
        error: function (e) {
            mostrarErrorDNIUpdate("Error al consultar el DNI.");
            console.error("Error en la consulta DNI:", e.responseText);
        }
    });
}

// Funciones para mostrar errores al consultar DNI
function mostrarErrorDNI(mensaje) {
    Swal.fire('Error', mensaje, 'error');
}

function mostrarErrorDNIUpdate(mensaje) {
    Swal.fire('Error', mensaje, 'error');
}

// Funciones para limpiar campos después de una consulta fallida
function limpiarCamposDNI() {
    $("#lastname").val('');
    $("#surname").val('');
    $("#names").val('');
}

function limpiarCamposDNIUpdate() {
    $("#lastnameUpdate").val('');
    $("#surnameUpdate").val('');
    $("#namesUpdate").val('');
}

// Función para mostrar mensajes Toast
function mostrarToast(mensaje, tipo) {
    let color;
    switch(tipo) {
        case "success":
            color = "#28a745";
            break;
        case "warning":
            color = "#ffc107";
            break;
        case "error":
            color = "#dc3545";
            break;
        default:
            color = "#17a2b8"; // Información
    }

    Toastify({
        text: mensaje,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: color,
        className: "toast-progress",        
    }).showToast();
}



// Inicializar validación de formularios usando Bootstrap
function inicializarValidacionFormularios() {
    // Seleccionar todos los formularios que necesiten validación
    var forms = document.querySelectorAll('.needs-validation');

    // Iterar sobre ellos y prevenir el envío si no son válidos
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
