// scripts/documentUpload.js

$(document).ready(function () {
    var documentosCache = [];
    var totalObligatorios = 0;
    var totalOpcionales = 0;
    var uploadedObligatorios = 0;
    var uploadedOpcionales = 0;

    // Cargar y renderizar los documentos al cargar la página
    cargarDocumentosDisponibles();

    function cargarDocumentosDisponibles() {
        $.ajax({
            url: '../controlador/DocumentUploadController.php?op=listarDocumentos',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    documentosCache = data.data;
                    // Contar documentos obligatorios y opcionales
                    totalObligatorios = documentosCache.filter(doc => doc.document_type.toLowerCase() === 'obligatorio').length;
                    totalOpcionales = documentosCache.filter(doc => doc.document_type.toLowerCase() === 'opcional').length;
                    obtenerEstadoDocumentos();
                } else {
                    console.error("Error al obtener los documentos:", data.message);
                    mostrarNotificacion("Error al obtener los documentos: " + data.message, "error");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                mostrarNotificacion("Error en la solicitud al servidor.", "error");
            }
        });
    }

    function obtenerEstadoDocumentos() {
        $.ajax({
            url: '../controlador/DocumentUploadController.php?op=obtenerEstadoDocumentos',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    uploadedObligatorios = 0;
                    uploadedOpcionales = 0;
                    renderizarDocumentos(documentosCache, data.data);
                } else {
                    console.error("Error al obtener el estado de los documentos:", data.message);
                    mostrarNotificacion("Error al obtener el estado de los documentos: " + data.message, "error");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                mostrarNotificacion("Error en la solicitud al servidor.", "error");
            }
        });
    }

    function renderizarDocumentos(documentos, estadoDocumentos) {
        var containerObligatorios = $('#document-container-obligatorios');
        var containerOpcionales = $('#document-container-opcionales');
        containerObligatorios.empty();
        containerOpcionales.empty();

        documentos.forEach(function (doc) {
            var documentId = doc.category_id;
            var archivoSubido = estadoDocumentos[documentId];

            var estado = archivoSubido ? 'subido' : 'pendiente';
            var cardClass = estado === 'subido' ? 'card-outline-inverse' : 'card-outline-danger';
            var iconClass = estado === 'subido' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
            var estadoTexto = estado === 'subido' ? 'Subido Correctamente' : 'Pendiente de Subida';
            var estadoColor = estado === 'subido' ? 'text-success' : 'text-danger';
            var archivoHTML = '';

            // Mostrar información del archivo subido si ya se ha subido uno
            if (archivoSubido) {
                if (doc.document_type.toLowerCase() === 'obligatorio') {
                    uploadedObligatorios++;
                } else {
                    uploadedOpcionales++;
                }
                archivoHTML = `
                    <div class="archivo-subido mb-3">
                        <p><strong>Archivo subido:</strong> <a href="${archivoSubido.document_path}" target="_blank">${archivoSubido.document_name}</a></p>
                        <p>Sube un nuevo archivo si deseas reemplazar el actual.</p>
                        <button type="button" class="btn btn-danger btn-delete mt-2" data-id="${documentId}">
                            <i class="fa fa-trash"></i> Eliminar
                        </button>
                    </div>
                `;
            }

            // Si el documento tiene una plantilla, mostrarla para descargar
            var plantillaHTML = '';
            if (doc.templatePath && doc.templatePath !== '') {
                plantillaHTML = `
                    <p><strong>Descarga esta plantilla y súbela escaneada:</strong></p>
                    <a href="${doc.templatePath}" target="_blank" class="btn btn-outline-info btn-sm mb-2">
                        <i class="fa fa-download"></i> Descargar Plantilla
                    </a>
                `;
            }

            var card = `
                <div class="col-md-6 col-lg-4">
                    <div class="card ${cardClass} mb-4 shadow-sm h-100" id="card_${documentId}">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="m-b-0 text-white">
                                <i class="${iconClass}" id="icon_${documentId}"></i> ${doc.name}
                            </h4>                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-text">${doc.description || 'Sube el documento requerido a continuación.'}</p>
                            ${plantillaHTML}
                            <p class="estado-documento ${estadoColor}"><strong>${estadoTexto}</strong></p>
                            ${archivoHTML}
                            <div class="form-group mt-auto">
                                <label for="file_${documentId}" class="form-label"><i class="fa fa-upload"></i> Selecciona un archivo</label>
                                <input type="file" class="form-control-file dropify" id="file_${documentId}" name="file_${documentId}" data-type="${doc.document_type}" data-category-id="${documentId}">
                            </div>
                            <button type="button" class="btn btn-primary btn-upload mt-auto" data-id="${documentId}">
                                <i class="fa fa-upload"></i> ${archivoSubido ? 'Reemplazar Archivo' : 'Subir Archivo'}
                            </button>
                            <div id="uploadStatus_${documentId}" class="mt-2"></div>
                            <!-- Historial de documentos -->
                            <div class="document-history mt-3" id="history_${documentId}">
                                <!-- Aquí se cargará el historial de documentos -->
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (doc.document_type.toLowerCase() === 'obligatorio') {
                containerObligatorios.append(card);
            } else {
                containerOpcionales.append(card);
            }

            // Cargar el historial de documentos subidos
            cargarHistorialDocumentos(documentId);
        });

        // Inicializar Dropify
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastra y suelta aquí, o haz clic para seleccionar',
                'replace': 'Arrastra y suelta o haz clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Oops, algo salió mal.'
            },
            error: {
                'fileSize': 'El archivo es muy grande (máx. 10MB).',
                'fileExtension': 'Este tipo de archivo no está permitido.'
            }
        });

        actualizarBarraProgreso();
    }

    function cargarHistorialDocumentos(documentId) {
        $.ajax({
            url: '../controlador/DocumentUploadController.php?op=obtenerHistorialDocumentos',
            method: 'POST',
            data: { category_id: documentId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    renderizarHistorialDocumentos(documentId, response.data);
                } else {
                    console.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar el historial de documentos:", xhr.responseText);
            }
        });
    }

    function renderizarHistorialDocumentos(documentId, history) {
        var historyHtml = '<h5>Historial de Documentos:</h5>';
        if (history.length === 0) {
            historyHtml += '<p>No se han subido documentos para este requerimiento.</p>';
        } else {
            historyHtml += '<ul class="list-group">';
            history.forEach(function (doc) {
                historyHtml += `
                    <li class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">${escapeHtml(doc.document_name)}</h5>
                            <small>${escapeHtml(doc.uploaded_at)}</small>
                        </div>
                        <a href="${doc.document_path}" target="_blank">Ver documento</a>
                    </li>
                `;
            });
            historyHtml += '</ul>';
        }

        $(`#history_${documentId}`).html(historyHtml);
    }

    $(document).on('click', '.btn-upload', function () {
        var documentId = $(this).data('id');
        var fileInput = $(`#file_${documentId}`)[0];
        var file = fileInput.files[0];
        var document_type = $(`#file_${documentId}`).data('type');
        var category_id = $(`#file_${documentId}`).data('category-id');

        if (!file) {
            mostrarNotificacion("Por favor, selecciona un archivo para subir.", "warning");
            return;
        }

        var formData = new FormData();
        formData.append('documentFile', file);
        formData.append('category_id', category_id);
        formData.append('document_type', document_type);
        formData.append('comment', ''); // Puedes agregar un campo de comentario si lo deseas

        $.ajax({
            url: '../controlador/DocumentUploadController.php?op=subir',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(`.btn-upload[data-id="${documentId}"]`).html('<div class="spinner-border spinner-border-sm" role="status"></div> Subiendo...');
                $(`#uploadStatus_${documentId}`).html('<div class="spinner-border text-info" role="status"><span class="sr-only">Subiendo...</span></div>');
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    $(`#uploadStatus_${documentId}`).html('<span class="text-success">Subida exitosa</span>');

                    // Cambiar el encabezado del card a verde
                    var cardHeader = $(`#file_${documentId}`).closest('.card').find('.card-header');
                    cardHeader.removeClass('bg-danger').addClass('bg-success');

                    // Actualizar el estado del documento
                    var estadoDocumento = $(`#file_${documentId}`).closest('.card-body').find('.estado-documento');
                    estadoDocumento.removeClass('text-danger').addClass('text-success').html('<strong>Documento subido</strong>');

                    $(`.btn-upload[data-id="${documentId}"]`).html('<i class="fa fa-upload"></i> Reemplazar Archivo');

                    var archivoHTML = `
                        <div class="archivo-subido mb-3">
                            <p><strong>Archivo subido:</strong> <a href="${data.documentPath}" target="_blank">${data.originalFileName}</a></p>
                            <p>Sube un nuevo archivo si deseas reemplazar el actual.</p>
                            <button type="button" class="btn btn-danger btn-delete mt-2" data-id="${documentId}">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </div>
                    `;
                    var cardBody = $(`#file_${documentId}`).closest('.card-body');
                    cardBody.find('.archivo-subido').remove();
                    cardBody.prepend(archivoHTML);

                    // Incrementar el progreso al subir un documento
                    if (document_type.toLowerCase() === 'obligatorio') {
                        uploadedObligatorios++;
                    } else {
                        uploadedOpcionales++;
                    }
                    actualizarBarraProgreso();

                    // Recargar el historial de documentos
                    cargarHistorialDocumentos(documentId);
                } else {
                    mostrarNotificacion(data.message, 'error');
                    $(`#uploadStatus_${documentId}`).html('<span class="text-danger">Error al subir</span>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                mostrarNotificacion('Error en la solicitud al servidor.', 'error');
                $(`#uploadStatus_${documentId}`).html('<span class="text-danger">Error al subir</span>');
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        var documentId = $(this).data('id');
        var document_type = $(`#file_${documentId}`).data('type');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto una vez eliminado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controlador/DocumentUploadController.php?op=eliminar',
                    method: 'POST',
                    data: { category_id: documentId },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'El documento ha sido eliminado exitosamente.', 'success');
                            obtenerEstadoDocumentos();

                            // Cambiar el encabezado del card a rojo
                            var cardHeader = $(`#file_${documentId}`).closest('.card').find('.card-header');
                            cardHeader.removeClass('bg-success').addClass('bg-danger');

                            // Actualizar el estado del documento
                            var estadoDocumento = $(`#file_${documentId}`).closest('.card-body').find('.estado-documento');
                            estadoDocumento.removeClass('text-success').addClass('text-danger').html('<strong>Documento pendiente</strong>');

                            // Decrementar el progreso al eliminar un documento
                            if (document_type.toLowerCase() === 'obligatorio') {
                                uploadedObligatorios--;
                            } else {
                                uploadedOpcionales--;
                            }
                            actualizarBarraProgreso();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Error', 'Error en la solicitud al servidor.', 'error');
                    }
                });
            }
        });
    });

    function actualizarBarraProgreso() {
        var progresoObligatorios = (uploadedObligatorios / totalObligatorios) * 100;
        var progresoOpcionales = (uploadedOpcionales / totalOpcionales) * 100;

        // Actualizar barra de documentos obligatorios
        $('#progressObligatorios').css({
            'width': progresoObligatorios + '%',
        });
        $('#progressObligatorios').attr('aria-valuenow', progresoObligatorios);
        $('#progressObligatorios').text(progresoObligatorios.toFixed(2) + '% Completado');

        // Actualizar barra de documentos opcionales
        $('#progressOpcionales').css({
            'width': progresoOpcionales + '%',
        });
        $('#progressOpcionales').attr('aria-valuenow', progresoOpcionales);
        $('#progressOpcionales').text(progresoOpcionales.toFixed(2) + '% Completado');
    }

    function mostrarNotificacion(mensaje, tipo) {
        var backgroundColor;

        switch (tipo) {
            case 'success':
                backgroundColor = '#4caf50';
                break;
            case 'error':
                backgroundColor = '#f44336';
                break;
            case 'warning':
                backgroundColor = '#ff9800';
                break;
            default:
                backgroundColor = '#2196f3';
        }

        Toastify({
            text: mensaje,
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: backgroundColor
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    }

    // Función para escapar caracteres HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
