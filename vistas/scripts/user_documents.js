// scripts/user_documents.js

$(document).ready(function () {
    listarUsuarios();

    // Manejar el envío del formulario de filtros
    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        listarUsuarios();
    });

    // Manejar el reset del filtro
    $('#resetFilter').on('click', function () {
        $('#startDate').val('');
        $('#endDate').val('');
        listarUsuarios();
    });
});

function listarUsuarios() {
    // Obtener los valores de los filtros
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    var tabla = $('#usuariosTable').DataTable({
        ajax: {
            url: '../controlador/UserDocumentsController.php?op=listarUsuarios',
            type: "GET",
            dataType: "json",
            dataSrc: 'usuarios',
            data: function (d) {
                // Añadir los filtros al objeto de datos
                d.start_date = startDate;
                d.end_date = endDate;
            },
            error: function (e) {
                console.error("Error al cargar los datos: ", e.responseText);
                Toastify({
                    text: "Error al cargar los usuarios.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true,
                }).showToast();
            }
        },
        columns: [
            { 
                "data": null, 
                "render": function(data) {
                    return `${data.names} ${data.lastname} (${data.username})`;
                }
            },
            { "data": "email" },
            { 
                "data": "porcentaje_subidos_mandatory",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_subidos_optional",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_aprobados_mandatory",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_aprobados_optional",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            { 
                "data": null,
                "render": function(data) {
                    return `
                        <button class="btn btn-primary btn-sm btn-ver-documentos" data-id="${data.id}" data-nombre="${data.names} ${data.lastname}">
                            <i class="fa fa-eye"></i> Ver Documentos
                        </button>`;
                },
                "orderable": false
            }
        ],
        language: {
            // Opciones de idioma (personalizar según sea necesario)
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        responsive: true,
        destroy: true,
        order: [[0, "asc"]]
    });
}

function crearBarraProgreso(valor, clase) {
    return `
        <div class="progress" style="height: 20px;">
            <div class="progress-bar ${clase}" role="progressbar" style="width: ${valor}%" aria-valuenow="${valor}" aria-valuemin="0" aria-valuemax="100">${valor}%</div>
        </div>`;
}

// Al hacer clic en "Ver Documentos"
$(document).on('click', '.btn-ver-documentos', function () {
    var userId = $(this).data('id');
    var userName = $(this).data('nombre');
    $('#usuarioNombre').text(userName);
    cargarDocumentosUsuario(userId);
    $('#modalDocumentosUsuario').modal('show');
});

// Cargar los documentos subidos por el usuario
function cargarDocumentosUsuario(userId) {
    // Obtener los valores de los filtros actuales
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
        url: '../controlador/UserDocumentsController.php?op=documentosUsuario',
        method: 'POST',
        data: { 
            user_id: userId,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // Separar documentos por tipo
                var documentosObligatorios = data.documentos.filter(doc => doc.document_type === 'obligatorio');
                var documentosOpcionales = data.documentos.filter(doc => doc.document_type === 'opcional');

                // Renderizar documentos
                renderizarDocumentos(documentosObligatorios, '#documentosObligatoriosContainer', userId);
                renderizarDocumentos(documentosOpcionales, '#documentosOpcionalesContainer', userId);
            } else {
                console.error(data.message);
                $('#documentosObligatoriosContainer, #documentosOpcionalesContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar los documentos: ", e.responseText);
            $('#documentosObligatoriosContainer, #documentosOpcionalesContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
        }
    });
}

function renderizarDocumentos(documentos, containerSelector, userId) {
    var html = '';
    if (documentos.length === 0) {
        html = '<p>No hay documentos para mostrar.</p>';
    } else {
        html = '<ul class="list-group">';
        documentos.forEach(function (doc) {
            html += `
                <li class="list-group-item" id="documento_${doc.document_id}">
                    <div>
                        <p><strong>${doc.document_name}</strong> (${doc.state_name})</p>
                        <p><a href="${doc.document_path}" target="_blank">Ver Documento</a></p>
                    </div>
                    <div class="mt-2">
                        <!-- Textarea para observación individual -->
                        <div class="form-group">
                            <label for="observacion_${doc.document_id}">Observación:</label>
                            <textarea class="form-control admin-observation" id="observacion_${doc.document_id}" rows="2">${doc.admin_observation || ''}</textarea>
                        </div>
                        <!-- Botones de acción -->
                        <button class="btn btn-success btn-sm btn-aprobar" data-id="${doc.document_id}" data-user="${userId}">Aprobar</button>
                        <button class="btn btn-warning btn-sm btn-solicitar-correccion" data-id="${doc.document_id}" data-user="${userId}">Solicitar Corrección</button>
                        <button class="btn btn-danger btn-sm btn-rechazar" data-id="${doc.document_id}" data-user="${userId}">Rechazar</button>
                    </div>
                </li>`;
        });
        html += '</ul>';
    }
    $(containerSelector).html(html);
}

// Eventos para cambiar estado de documentos
$(document).on('click', '.btn-aprobar, .btn-solicitar-correccion, .btn-rechazar', function () {
    var documentId = $(this).data('id');
    var userId = $(this).data('user');
    var estadoId;

    if ($(this).hasClass('btn-aprobar')) {
        estadoId = 2; // Aprobado
    } else if ($(this).hasClass('btn-solicitar-correccion')) {
        estadoId = 4; // Por Corregir
    } else if ($(this).hasClass('btn-rechazar')) {
        estadoId = 3; // Rechazado
    }

    var observacion = $(`#observacion_${documentId}`).val();

    // Validar que la observación no esté vacía
    if (!observacion.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Observación requerida',
            text: 'Por favor, ingresa una observación antes de continuar.',
        });
        return;
    }

    // Confirmación antes de proceder
    var accionTexto = '';
    switch (estadoId) {
        case 2:
            accionTexto = 'aprobar';
            break;
        case 3:
            accionTexto = 'rechazar';
            break;
        case 4:
            accionTexto = 'solicitar corrección';
            break;
        default:
            accionTexto = 'realizar esta acción';
    }

    Swal.fire({
        title: `¿Estás seguro de ${accionTexto} este documento?`,
        text: `Una vez confirmada, la acción no podrá deshacerse.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, estadoId, userId, observacion);
        }
    });
});

// Función para cambiar el estado del documento
function cambiarEstadoDocumento(documentId, estadoId, userId, observacion) {
    $.ajax({
        url: '../controlador/UserDocumentsController.php?op=cambiarEstadoDocumento',
        method: 'POST',
        data: {
            document_id: documentId,
            estado_id: estadoId,
            observacion: observacion
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // Actualizar el modal y el DataTable
                cargarDocumentosUsuario(userId);
                actualizarUsuariosTable();

                // Notificación
                let toastColor = "";
                let toastText = "";
                switch (estadoId) {
                    case 2:
                        toastColor = "#28a745"; // Verde
                        toastText = "Documento aprobado correctamente.";
                        break;
                    case 3:
                        toastColor = "#dc3545"; // Rojo
                        toastText = "Documento rechazado.";
                        break;
                    case 4:
                        toastColor = "#ffc107"; // Amarillo
                        toastText = "Documento marcado para corrección.";
                        break;
                    default:
                        toastColor = "#6c757d"; // Gris
                        toastText = "Estado del documento actualizado.";
                }

                Toastify({
                    text: toastText,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: toastColor,
                    stopOnFocus: true,
                }).showToast();
            } else {
                console.error(data.message);
                Toastify({
                    text: "Error: " + data.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true,
                }).showToast();
            }
        },
        error: function (e) {
            console.error("Error en la solicitud AJAX: ", e.responseText);
            Toastify({
                text: "Error al comunicarse con el servidor.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                stopOnFocus: true,
            }).showToast();
        }
    });
}

// Función para actualizar el DataTable de usuarios
function actualizarUsuariosTable() {
    var table = $('#usuariosTable').DataTable();
    table.ajax.reload(null, false); // false para no resetear la paginación
}
