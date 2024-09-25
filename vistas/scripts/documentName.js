$(document).ready(function () {
    Init();
});

let tabla;

// Función de Inicialización
function Init() {
    listar();
    // Asignar eventos a botones
    $('#btnGuardar').click(guardar);
    $('#btnActualizar').click(actualizar);
}

// Función para listar los documentos
function listar() {
    tabla = $('#tbllistado').DataTable({
        "ajax": {
            url: '../controlador/DocumentNameController.php?op=listar',
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.error("Error al cargar los datos:", e.responseText);
            }
        },
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" },
            { "data": "4" },
            { "data": "5" }
        ],
        "language": {
            "paginate": {
                "previous": '<svg...></svg>',
                "next": '<svg...></svg>'
            },
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "search": '<svg...></svg>',
            "searchPlaceholder": "Buscar...",
            "lengthMenu": "Resultados :  _MENU_",
        },
        "pageLength": 10,
        "lengthMenu": [10, 20, 50],
        "destroy": true,
        "order": [[0, "desc"]],
        "dom": '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f>>>><"col-md-12"rt><"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
        "buttons": [
            { extend: 'copy', className: 'btn btn-secondary' },
            { extend: 'pdf', className: 'btn btn-secondary' },
            { extend: 'excel', className: 'btn btn-secondary' },
            { extend: 'print', className: 'btn btn-secondary' },
            { extend: 'csv', className: 'btn btn-secondary' }
        ]
    });
}

// Función para mostrar los datos en el modal de actualización
function mostrar(id) {
    $.post('../controlador/DocumentNameController.php?op=mostrar', { id: id }, function (data) {
        const result = JSON.parse(data);
        if (result) {
            $('#idUpdate').val(result.id);
            $('#documentNameUpdate').val(result.documentName);
            $('#formularioActualizar').modal('show');
        } else {
            showToast("No se pudo obtener los datos del documento.", "error");
        }
    }).fail(function () {
        showToast("Error en la solicitud de datos.", "error");
    });
}

// Función para guardar un nuevo documento
function guardar() {
    const documentName = $('#documentName').val().trim();

    if (documentName === "") {
        showToast("Complete todos los campos requeridos.", "warning");
        return;
    }

    $.post('../controlador/DocumentNameController.php?op=guardar', { documentName: documentName }, function (response) {
        if (response.includes("correctamente")) {
            $('#formularioregistros').modal('hide');
            showToast(response, "success");
            tabla.ajax.reload();
            $('#formulario')[0].reset();
        } else {
            showToast(response, "error");
        }
    }).fail(function () {
        showToast("Error al guardar el documento.", "error");
    });
}

// Función para actualizar un documento existente
function actualizar() {
    const id = $('#idUpdate').val();
    const documentName = $('#documentNameUpdate').val().trim();

    if (documentName === "") {
        showToast("Complete todos los campos requeridos.", "warning");
        return;
    }

    $.post('../controlador/DocumentNameController.php?op=editar', { id: id, documentName: documentName }, function (response) {
        if (response.includes("correctamente")) {
            $('#formularioActualizar').modal('hide');
            showToast(response, "success");
            tabla.ajax.reload();
            $('#formActualizar')[0].reset();
        } else {
            showToast(response, "error");
        }
    }).fail(function () {
        showToast("Error al actualizar el documento.", "error");
    });
}

// Asignar eventos a los botones dinámicos
$(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    mostrar(id);
});

$(document).on('click', '.btn-desactivar', function () {
    const id = $(this).data('id');
    confirmarEliminacion(id);
});

$(document).on('click', '.btn-activar', function () {
    const id = $(this).data('id');
    confirmarActivacion(id);
});

// Función para mostrar notificaciones
function showToast(message, type) {
    let bgColor = "#17a2b8"; // Default color
    switch (type) {
        case "success":
            bgColor = "#28a745";
            break;
        case "error":
            bgColor = "#dc3545";
            break;
        case "warning":
            bgColor = "#ffc107";
            break;
        default:
            bgColor = "#17a2b8";
    }

    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: bgColor,
        className: "toast-progress",
    }).showToast();
}

// Función para confirmar desactivación usando SweetAlert2
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro de desactivar el registro?',
        text: "¡Este registro se dará de baja hasta que se vuelva a activar!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡desactívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            desactivar(id);
        }
    });
}

// Función para desactivar un documento
function desactivar(id) {
    $.post('../controlador/DocumentNameController.php?op=desactivar', { id: id }, function (response) {
        if (response.includes("correctamente")) {
            showToast(response, "success");
            tabla.ajax.reload(null, false);
        } else {
            showToast(response, "error");
        }
    }).fail(function () {
        showToast("Error al desactivar el documento.", "error");
    });
}

// Función para confirmar activación usando SweetAlert2
function confirmarActivacion(id) {
    Swal.fire({
        title: '¿Está seguro de activar el registro?',
        text: "Este registro se activará nuevamente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡actívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            activar(id);
        }
    });
}

// Función para activar un documento
function activar(id) {
    $.post('../controlador/DocumentNameController.php?op=activar', { id: id }, function (response) {
        if (response.includes("correctamente")) {
            showToast(response, "success");
            tabla.ajax.reload(null, false);
        } else {
            showToast(response, "error");
        }
    }).fail(function () {
        showToast("Error al activar el documento.", "error");
    });
}
