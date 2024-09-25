// scripts/applicants_documents.js

$(document).ready(function () {
    listarApplicants();

    // Manejar el envío del formulario de filtros
    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        listarApplicants();
    });

    // Manejar el reset del filtro
    $('#resetFilter').on('click', function () {
        $('#startDate').val('');
        $('#endDate').val('');
        listarApplicants();
    });
});

function listarApplicants() {
    // Obtener los valores de los filtros
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    var tabla = $('#applicantsTable').DataTable({
        ajax: {
            url: '../controlador/DocumentEvaluationController.php?op=listarApplicants',
            type: "GET",
            dataType: "json",
            dataSrc: 'applicants',
            data: function (d) {
                // Añadir los filtros al objeto de datos
                d.start_date = startDate;
                d.end_date = endDate;
            },
            error: function (e) {
                console.error("Error al cargar los datos: ", e.responseText);
                Toastify({
                    text: "Error al cargar los postulantes.",
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
                "data": "porcentaje_subidos_cv",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_subidos_other",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_aprobados_cv",
                "render": function(data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            { 
                "data": "porcentaje_aprobados_other",
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
            "sSearch":         "Buscar:",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
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
    var applicantId = $(this).data('id');
    var applicantName = $(this).data('nombre');
    $('#applicantNombre').text(applicantName);
    cargarDocumentosApplicant(applicantId);
    $('#modalDocumentosApplicant').modal('show');
});

// Cargar los documentos subidos por el postulante
function cargarDocumentosApplicant(applicantId) {
    // Obtener los valores de los filtros actuales
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
        url: '../controlador/DocumentEvaluationController.php?op=documentosApplicant',
        method: 'POST',
        data: { 
            applicant_id: applicantId,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // Renderizar documentos
                renderizarDocumentosApplicant(data.cv_documents, data.other_documents, '#documentosApplicantContainer', applicantId);
            } else {
                console.error(data.message);
                $('#documentosApplicantContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar los documentos: ", e.responseText);
            $('#documentosApplicantContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
        }
    });
}

function renderizarDocumentosApplicant(cv_documents, other_documents, containerSelector, applicantId) {
    var html = '';

    html += '<h5>CV</h5>';
    if (cv_documents.length === 0) {
        html += '<p>No hay CV subido.</p>';
    } else {
        html += '<ul class="list-group mb-3">';
        cv_documents.forEach(function (doc) {
            html += generarItemDocumento(doc, applicantId);
        });
        html += '</ul>';
    }

    html += '<h5>Otros Documentos</h5>';
    if (other_documents.length === 0) {
        html += '<p>No hay otros documentos subidos.</p>';
    } else {
        html += '<ul class="list-group">';
        other_documents.forEach(function (doc) {
            html += generarItemDocumento(doc, applicantId);
        });
        html += '</ul>';
    }

    $(containerSelector).html(html);
}

function generarItemDocumento(doc, applicantId) {
    return `
        <li class="list-group-item" id="documento_${doc.document_id}">
            <div>
                <p><strong>${doc.original_file_name}</strong> (${doc.state_name})</p>
                <p><a href="${doc.document_path}" target="_blank">Ver Documento</a></p>
                <p><strong>Observación del Usuario:</strong> ${doc.user_observation || 'Sin observación'}</p>
            </div>
            <div class="mt-2">
                <!-- Textarea para observación individual -->
                <div class="form-group">
                    <label for="observacion_${doc.document_id}">Observación del Administrador:</label>
                    <textarea class="form-control admin-observation" id="observacion_${doc.document_id}" rows="2">${doc.admin_observation || ''}</textarea>
                </div>
                <!-- Botones de acción -->
                <button class="btn btn-success btn-sm btn-aprobar" data-id="${doc.document_id}" data-applicant="${applicantId}">Aprobar</button>
                <button class="btn btn-warning btn-sm btn-solicitar-correccion" data-id="${doc.document_id}" data-applicant="${applicantId}">Solicitar Corrección</button>
                <button class="btn btn-danger btn-sm btn-rechazar" data-id="${doc.document_id}" data-applicant="${applicantId}">Rechazar</button>
            </div>
        </li>`;
}

// Eventos para cambiar estado de documentos
$(document).on('click', '.btn-aprobar, .btn-solicitar-correccion, .btn-rechazar', function () {
    var documentId = $(this).data('id');
    var applicantId = $(this).data('applicant');
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
            cambiarEstadoDocumento(documentId, estadoId, applicantId, observacion);
        }
    });
});

// Función para cambiar el estado del documento
function cambiarEstadoDocumento(documentId, estadoId, applicantId, observacion) {
    $.ajax({
        url: '../controlador/DocumentEvaluationController.php?op=cambiarEstadoDocumento',
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
                cargarDocumentosApplicant(applicantId);
                actualizarApplicantsTable();

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

// Función para actualizar el DataTable de postulantes
function actualizarApplicantsTable() {
    var table = $('#applicantsTable').DataTable();
    table.ajax.reload(null, false); // false para no resetear la paginación
}
