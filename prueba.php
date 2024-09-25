<?php
// admin_evaluate_documents.php

session_start();

// Verificar si el usuario ha iniciado sesión y es un superadmin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-6 align-self-center">
            <h3 class="text-themecolor"><i class="fa fa-check-circle"></i> Evaluar Documentos</h3>
        </div>
    </div>

    <!-- Filtro por Rango de Fechas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-outline-info">
                <div class="card-header bg-info text-white">
                    <h4 class="m-b-0"><i class="fa fa-filter"></i> Filtro de Fecha</h4>
                </div>

                <div class="card-body">
                    <form id="filtroForm" class="form-inline">
                        <div class="form-group mb-2">
                            <label for="startDate" class="mr-2"><strong>Fecha Inicio:</strong></label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="endDate" class="mr-2"><strong>Fecha Fin:</strong></label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> Filtrar</button>
                        <button type="button" id="resetFilter" class="btn btn-secondary mb-2 ml-2"><i class="fa fa-undo"></i> Resetear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline-info">
                <div class="card-header bg-info text-white">
                    <h4 class="m-b-0"><i class="fa fa-users"></i> Usuarios</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usuariosTable" class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Porcentaje Subidos</th>
                                    <th>Porcentaje Aprobados</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="documentList">
                                <!-- Los usuarios se cargarán dinámicamente aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Documentos de un Usuario -->
    <div class="modal fade" id="modalDocumentosUsuario" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalDocumentosUsuarioLabel">Documentos de <span id="usuarioNombre"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Secciones para CV y Otros Documentos -->
                    <h6>CV</h6>
                    <div id="documentosCVContainer"></div>

                    <hr>

                    <h6>Otros Documentos</h6>
                    <div id="documentosOtrosContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Incluir los scripts necesarios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Incluir DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- CSS adicional para mejorar el diseño -->
<style>
    /* General Colors */
    body {
        background-color: #f9f9f9;
    }

    /* Button Styles */
    .btn-ver-documentos {
        background-color: #17a2b8;
        color: white;
    }

    .btn-ver-documentos:hover {
        background-color: #138496;
        color: white;
    }

    /* Progress Bar */
    .progress-bar {
        height: 20px;
        border-radius: 10px;
    }

    /* Card Styling */
    .card {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.15);
    }

    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #e0e0e0;
    }

    /* Modal Styling */
    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    /* Estadísticas Styling */
    .card-body p {
        font-size: 1.1em;
    }

    /* Container Padding */
    .container-fluid {
        padding: 20px;
    }
</style>

<!-- JavaScript para la Evaluación de Documentos -->
<script>
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
                url: '../controlador/DocumentApplicantController.php?op=listarUsuarios',
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
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    "data": null, 
                    "render": function(data) {
                        return `${data.names} ${data.lastname} (${data.username})`;
                    }
                },
                { "data": "email" },
                { 
                    "data": "porcentaje_subidos",
                    "render": function(data) {
                        if (typeof data !== 'undefined') {
                            return crearBarraProgreso(data, 'bg-info');
                        } else {
                            return '0%';
                        }
                    },
                    "orderable": false
                },
                { 
                    "data": "porcentaje_aprobados",
                    "render": function(data) {
                        if (typeof data !== 'undefined') {
                            return crearBarraProgreso(data, 'bg-success');
                        } else {
                            return '0%';
                        }
                    },
                    "orderable": false
                },
                { 
                    "data": null,
                    "render": function(data) {
                        return `
                            <button class="btn btn-ver-documentos btn-sm" data-id="${data.id}" data-nombre="${data.names} ${data.lastname}">
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
            url: '../controlador/DocumentApplicantController.php?op=documentosUsuario',
            method: 'POST',
            data: { 
                user_id: userId,
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // Categorizar documentos en CV y Otros Documentos
                    var documentosCV = data.documentos.filter(doc => doc.document_name.toLowerCase().includes('cv'));
                    var documentosOtros = data.documentos.filter(doc => !doc.document_name.toLowerCase().includes('cv'));

                    // Renderizar documentos
                    renderizarDocumentos(documentosCV, '#documentosCVContainer', userId, 'cv');
                    renderizarDocumentos(documentosOtros, '#documentosOtrosContainer', userId, 'otros');
                } else {
                    console.error(data.message);
                    $('#documentosCVContainer, #documentosOtrosContainer').html(`<p class="text-danger">${data.message}</p>`);
                }
            },
            error: function (e) {
                console.error("Error al cargar los documentos: ", e.responseText);
                $('#documentosCVContainer, #documentosOtrosContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
            }
        });
    }

    function renderizarDocumentos(documentos, containerSelector, userId, tipo) {
        var html = '';
        if (documentos.length === 0) {
            html = '<p>No hay documentos para mostrar.</p>';
        } else {
            html = '<ul class="list-group">';
            documentos.forEach(function (doc) {
                html += `
                    <li class="list-group-item" id="documento_${doc.document_id}">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>${doc.document_name}</strong> (${doc.state_name})</p>
                                <p><a href="${doc.document_path}" target="_blank">Ver Documento</a></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button class="btn btn-success btn-sm btn-aprobar" data-id="${doc.document_id}" data-user="${userId}">Aprobar</button>
                                <button class="btn btn-warning btn-sm btn-solicitar-correccion" data-id="${doc.document_id}" data-user="${userId}">Solicitar Corrección</button>
                                <button class="btn btn-danger btn-sm btn-rechazar" data-id="${doc.document_id}" data-user="${userId}">Rechazar</button>
                            </div>
                        </div>
                        <div class="mt-2">
                            <!-- Textarea para observación individual -->
                            <div class="form-group">
                                <label for="observacion_${doc.document_id}">Observación:</label>
                                <textarea class="form-control admin-observation" id="observacion_${doc.document_id}" rows="2" placeholder="Ingresa tus observaciones">${doc.user_observation || ''}</textarea>
                            </div>
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
            url: '../controlador/DocumentApplicantController.php?op=cambiarEstadoDocumento',
            method: 'POST',
            data: {
                document_id: documentId,
                estado_id: estadoId,
                observacion: observacion
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // Actualizar los documentos en el modal
                    cargarDocumentosUsuario(userId);
                    // Actualizar el DataTable
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
</script>
