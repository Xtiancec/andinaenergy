$(document).ready(function () {
    init();
});

var tabla;

function init() {
    // Inicializar la tabla de usuarios
    listar();

    // Cargar empresas en los selectores
    cargarEmpresas("#company_id");
    cargarEmpresas("#company_idUpdate");

    // Evento al abrir el modal de agregar usuario
    $('#formularioregistros').on('show.bs.modal', function (e) {
        $('#formulario')[0].reset();
        $('#formulario').removeClass('was-validated');
        resetFeedback('#username_feedback');
    });

    // Evento al abrir el modal de actualizar usuario
    $('#formularioActualizar').on('show.bs.modal', function (e) {
        $('#formActualizar')[0].reset();
        $('#formActualizar').removeClass('was-validated');
        resetFeedback('#usernameUpdate_feedback');
    });

    // Manejar el evento submit para agregar usuario
    $("#formulario").on("submit", function (e) {
        e.preventDefault();
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        verificarYGuardar();
    });

    // Manejar el evento submit para actualizar usuario
    $("#formActualizar").on("submit", function (e) {
        e.preventDefault();
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        actualizar();
    });

    // Verificación en tiempo real de duplicados de username
    setupDuplicateCheck();

    // Configurar selectores dependientes para agregar y actualizar usuarios
    setupDependentSelectors("#company_id", "#area_id", "#job_id");
    setupDependentSelectors("#company_idUpdate", "#area_idUpdate", "#job_idUpdate");

    // Consulta DNI al cambiar el campo del username al agregar
    $("#username").on('change', function () {
        const dni = $(this).val().trim();
        if (dni.length === 8) {  // Asegúrate de que el DNI tiene 8 dígitos
            consultarDNI(dni);
        }
    });

    // Consulta DNI al cambiar el campo del username al actualizar
    $("#usernameUpdate").on('change', function () {
        const dni = $(this).val().trim();
        if (dni.length === 8) {  // Asegúrate de que el DNI tiene 8 dígitos
            consultarDNIUpdate(dni);
        }
    });
}


// Función para resetear los mensajes de feedback
function resetFeedback(...selectors) {
    selectors.forEach(selector => {
        $(selector).html('');
    });
}

// Configuración de verificación en tiempo real de duplicados de username
function setupDuplicateCheck() {
    // Agregar usuario
    $("#username").on('change', function () {
        verificarDuplicadoUsername($(this).val().trim(), null, '#username_feedback');
    });

    // Actualizar usuario
    $("#usernameUpdate").on('change', function () {
        verificarDuplicadoUsername($(this).val().trim(), $("#idUpdate").val(), '#usernameUpdate_feedback');
    });
}

// Configuración de selectores dependientes
function setupDependentSelectors(companySelector, areaSelector, jobSelector) {
    $(companySelector).on('change', function () {
        var company_id = $(this).val();
        if (company_id) {
            cargarAreas(company_id, areaSelector);
        } else {
            resetSelect(areaSelector, 'Área');
            resetSelect(jobSelector, 'Puesto de Trabajo');
        }
    });

    $(areaSelector).on('change', function () {
        var area_id = $(this).val();
        if (area_id) {
            cargarPuestosPorArea(area_id, jobSelector);
        } else {
            resetSelect(jobSelector, 'Puesto de Trabajo');
        }
    });
}

// Función para resetear los selectores
function resetSelect(selector, placeholder) {
    $(selector).html(`<option value="">Seleccione un ${placeholder}</option>`);
}

// Función para listar usuarios en la tabla
function listar() {
    tabla = $("#tbllistado").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: '../controlador/UserController.php?op=listar',
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.log("Error en el listado: ", e.responseText);
                Swal.fire('Error', 'No se pudo cargar la lista de usuarios.', 'error');
            }
        },
        "deferRender": true,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "columns": [
            { "data": "id" },
            { "data": "company_name" },
            { "data": "area_name" },
            { "data": "position_name" },
            { "data": "username" },
            { "data": "full_name" },
            { "data": "email" },
            { "data": "role" },
            { "data": "is_active" },
            { "data": "options", "orderable": false, "searchable": false }
        ],
        "order": [[0, "asc"]],
        "pageLength": 10,
        "destroy": true,
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 'excel', 'csv', 'pdf'
        ]
    });
}

// Función para cargar las empresas en el select
function cargarEmpresas(selector, selectedId = null) {
    $.ajax({
        url: "../controlador/UserController.php?op=listarEmpresas",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let options = "<option value=''>Seleccione una Empresa</option>";
            data.forEach(empresa => {
                options += `<option value="${empresa.id}" ${selectedId == empresa.id ? 'selected' : ''}>${empresa.company_name}</option>`;
            });
            $(selector).html(options);
        },
        error: function (xhr, status, error) {
            console.error(`Error al cargar empresas: ${error}`);
            Swal.fire('Error', 'No se pudieron cargar las empresas.', 'error');
        }
    });
}

// Función para cargar Áreas en el select
function cargarAreas(company_id, selector, selectedId = null) {
    $.ajax({
        url: "../controlador/UserController.php?op=listarAreasPorEmpresa",
        type: "POST",
        data: { company_id: company_id },
        dataType: "json",
        success: function (data) {
            let options = "<option value=''>Seleccione un Área</option>";
            data.forEach(area => {
                options += `<option value="${area.id}" ${selectedId == area.id ? 'selected' : ''}>${area.area_name}</option>`;
            });
            $(selector).html(options);
        },
        error: function (xhr, status, error) {
            console.error(`Error al cargar áreas: ${error}`);
            $(selector).html('<option value="">Seleccione un Área</option>');
            Swal.fire('Error', 'No se pudieron cargar las áreas.', 'error');
        }
    });
}

// Función para cargar Puestos de Trabajo por Área en el select
function cargarPuestosPorArea(area_id, selector, selectedId = null) {
    $.ajax({
        url: "../controlador/UserController.php?op=listarPuestosPorArea",
        type: "POST",
        data: { area_id: area_id },
        dataType: "json",
        success: function (data) {
            let options = "<option value=''>Seleccione un Puesto de Trabajo</option>";
            data.forEach(puesto => {
                options += `<option value="${puesto.id}" ${selectedId == puesto.id ? 'selected' : ''}>${puesto.position_name}</option>`;
            });
            $(selector).html(options);
        },
        error: function (xhr, status, error) {
            console.error(`Error al cargar puestos de trabajo: ${error}`);
            $(selector).html('<option value="">Seleccione un Puesto de Trabajo</option>');
            Swal.fire('Error', 'No se pudieron cargar los puestos de trabajo.', 'error');
        }
    });
}

// Función para verificar duplicados de username
// Función para verificar duplicados antes de guardar o actualizar
// Verify before adding or updating the user
function verificarDuplicadoUsername(username, userId = null, feedbackSelector) {
    if (!username) return;

    $.ajax({
        url: '../controlador/UserController.php?op=verificarDuplicado',
        type: 'POST',
        data: {
            username: username,
            userId: userId  // Pass userId to avoid self-duplication during updates
        },
        dataType: 'json',
        success: function (response) {
            if (response.existsUsername) {
                $(feedbackSelector).html('El nombre de usuario ya está registrado.').addClass('text-danger').removeClass('text-success');
            } else {
                $(feedbackSelector).html('Nombre de usuario disponible.').addClass('text-success').removeClass('text-danger');
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error al verificar username: ${error}`);
            $(feedbackSelector).html('Error al verificar el nombre de usuario.').addClass('text-danger').removeClass('text-success');
        }
    });
}



// Función para verificar duplicados antes de guardar
function verificarYGuardar() {
    var username = $("#username").val().trim();

    $.ajax({
        url: '../controlador/UserController.php?op=verificarDuplicado',
        type: 'POST',
        data: { username: username },
        dataType: 'json',
        success: function (response) {
            if (response.existsUsername) {
                Swal.fire('Error', 'El nombre de usuario ya está registrado.', 'error');
            } else {
                guardarUsuario();
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error al verificar duplicados: ${error}`);
            Swal.fire('Error', 'Error al verificar duplicados.', 'error');
        }
    });
}

function guardarUsuario() {
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../controlador/UserController.php?op=insertar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Mostrar un mensaje de éxito con Swal
                Swal.fire({
                    icon: 'success', // Cambiamos el icono a "success"
                    title: 'Éxito',
                    text: response.message
                });
                $("#formularioregistros").modal("hide");
                tabla.ajax.reload();
            } else {
                // Mostrar un mensaje de error con Swal
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error al guardar usuario: ${error}`);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al registrar el usuario.'
            });
        }
    });
}


// Función para mostrar los datos de un usuario en el formulario de actualización
function mostrar(id) {
    $.ajax({
        url: "../controlador/UserController.php?op=mostrar",
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (data) {
            if (data) {
                $("#formularioActualizar").modal("show");

                // Cargar las empresas, áreas y puestos seleccionados
                cargarEmpresas("#company_idUpdate", data.company_id);
                cargarAreas(data.company_id, "#area_idUpdate", data.area_id);
                cargarPuestosPorArea(data.area_id, "#job_idUpdate", data.job_id);

                // Asignar los valores a los campos
                $("#idUpdate").val(data.id);
                $("#usernameUpdate").val(data.username);
                $("#emailUpdate").val(data.email);
                $("#lastnameUpdate").val(data.lastname);
                $("#surnameUpdate").val(data.surname);
                $("#namesUpdate").val(data.names);
                $("#roleUpdate").val(data.role);
                $("#is_employeeUpdate").val(data.is_employee);

                // Asignar el tipo de identificación seleccionado
                $("#identification_typeUpdate").val(data.identification_type);
                
                // Asignar la nacionalidad seleccionada
                $("#nacionalityUpdate").val(data.nacionality);
            } else {
                Swal.fire('Error', 'No se encontraron datos para el usuario seleccionado.', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al mostrar los datos del usuario: ", error);
            Swal.fire('Error', 'Error al mostrar los datos del usuario.', 'error');
        }
    });
}


// Función para actualizar un usuario
function actualizar() {
    var formData = new FormData($("#formActualizar")[0]);

    var username = $("#usernameUpdate").val().trim(); // Username o DNI
    var userId = $("#idUpdate").val(); // ID del usuario que se está editando

    // Verificar si el DNI ya existe en otro usuario, pero ignorar al mismo usuario que se está editando
    $.ajax({
        url: '../controlador/UserController.php?op=verificarDuplicado',
        type: 'POST',
        data: {
            username: username,
            userId: userId  // Pasamos el ID del usuario para que no se considere como duplicado si es el mismo
        },
        dataType: 'json',
        success: function (response) {
            if (response.existsUsername) {
                Swal.fire('Error', 'El nombre de usuario ya está registrado por otro usuario.', 'error');
            } else {
                // Si no está duplicado, realizamos la actualización
                $.ajax({
                    url: "../controlador/UserController.php?op=actualizar",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success');
                            $("#formularioActualizar").modal("hide");
                            tabla.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(`Error al actualizar usuario: ${error}`);
                        Swal.fire('Error', 'Error al actualizar el usuario.', 'error');
                    }
                });
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error al verificar duplicados: ${error}`);
            Swal.fire('Error', 'Error al verificar duplicados.', 'error');
        }
    });
}

// Función para activar un usuario
function activar(id) {
    Swal.fire({
        title: '¿Estás seguro de activar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controlador/UserController.php?op=activar',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Activado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo activar el usuario.', 'error');
                }
            });
        }
    });
}

// Función para desactivar un usuario
function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro de desactivar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controlador/UserController.php?op=desactivar',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Desactivado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo desactivar el usuario.', 'error');
                }
            });
        }
    });
}

// Función para obtener el historial de acceso del usuario
function mostrarHistorial(userId) {
    $.ajax({
        url: '../controlador/UserController.php?op=obtenerHistorialAcceso',
        type: 'POST',
        data: { userId: userId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                let historyHtml = "";
                response.history.forEach(entry => {
                    historyHtml += `
                        <tr>
                            <td>${entry.access_time}</td>
                            <td>${entry.logout_time ? entry.logout_time : 'Aún no ha cerrado sesión'}</td>
                        </tr>
                    `;
                });
                $('#tblHistorial tbody').html(historyHtml);
                $('#modalHistorial').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Error al obtener el historial de accesos.', 'error');
        }
    });
}


// Function to check DNI when adding a user
function consultarDNI(dni) {
    $.ajax({
        url: 'proxy.php',
        method: 'GET',
        data: { dni: dni },
        dataType: 'json',
        success: function (response) {
            if (response && response.apellidoPaterno && response.apellidoMaterno && response.nombres) {
                $("#lastname").val(response.apellidoPaterno);
                $("#surname").val(response.apellidoMaterno);
                $("#names").val(response.nombres);
            } else {
                Swal.fire('Información', 'No se encontraron datos para el DNI ingresado.', 'info');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching DNI: ", error);
            Swal.fire('Error', 'No se pudieron obtener los datos del DNI.', 'error');
        }
    });
}


// Function to check DNI when updating a user
function consultarDNIUpdate(dni) {
    $.ajax({
        url: 'proxy.php',
        method: 'GET',
        data: { dni: dni },
        dataType: 'json',
        success: function (response) {
            if (response && response.apellidoPaterno && response.apellidoMaterno && response.nombres) {
                $("#lastnameUpdate").val(response.apellidoPaterno);
                $("#surnameUpdate").val(response.apellidoMaterno);
                $("#namesUpdate").val(response.nombres);
            } else {
                Swal.fire('Información', 'No se encontraron datos para el DNI ingresado.', 'info');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching DNI: ", error);
            Swal.fire('Error', 'No se pudieron obtener los datos del DNI.', 'error');
        }
    });
}


// Array con las nacionalidades de los países de América
const nacionalidadesAmerica = ['Peru','Venezuela', 'Bolivia',
    'Argentina', 'Brasil', 'Canadá', 'Chile', 'Colombia', 
    'Costa Rica', 'Cuba', 'Ecuador', 'El Salvador', 'Guatemala', 'Haití', 
    'Honduras', 'Jamaica', 'México', 'Nicaragua', 'Panamá', 'Paraguay', 
    'Perú', 'República Dominicana', 'Uruguay', 'Estados Unidos'
];

// Función para cargar las nacionalidades en el select
function cargarNacionalidades(selector, selectedNacionality = 'Perú') {
    const select = $(selector);
    select.empty(); // Limpiar el select

    // Agregar la opción por defecto
    select.append('<option value="">Seleccione una Nacionalidad</option>');

    // Agregar las nacionalidades del array
    nacionalidadesAmerica.forEach(function(nacionalidad) {
        const selected = (nacionalidad === selectedNacionality) ? 'selected' : '';
        select.append(`<option value="${nacionalidad}" ${selected}>${nacionalidad}</option>`);
    });
}

$(document).ready(function() {
    // Inicializar tabla de usuarios y demás eventos

    // Cargar nacionalidades al abrir el modal de agregar usuario
    $('#formularioregistros').on('show.bs.modal', function (e) {
        cargarNacionalidades('#nacionality');  // Cargar nacionalidades en el modal de agregar
    });

    // Cargar nacionalidades al abrir el modal de actualizar usuario
    $('#formularioActualizar').on('show.bs.modal', function (e) {
        cargarNacionalidades('#nacionalityUpdate');  // Cargar nacionalidades en el modal de actualizar
    });
});
