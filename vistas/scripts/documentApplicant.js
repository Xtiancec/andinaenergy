// scripts/documentApplicant.js

$(document).ready(function () {
    // Inicialización de Dropify
    $('.dropify').dropify({
        messages: {
            'default': 'Arrastra y suelta un archivo aquí o haz clic',
            'replace': 'Arrastra y suelta o haz clic para reemplazar',
            'remove': 'Eliminar',
            'error': 'Oops, algo salió mal.'
        },
        error: {
            'fileSize': 'El tamaño del archivo es demasiado grande (5MB max).',
            'fileExtension': 'Este tipo de archivo no está permitido.'
        }
    });

    var documentCounter = 1;

    // Añadir nuevo campo de documento para "otros documentos"
    $('#addDocument').on('click', function () {
        var newRowId = `doc_row_${documentCounter}`;
        var newFileInput = `
            <div class="form-group" id="${newRowId}">
                <label for="other_file_${documentCounter}"><i class="fa fa-upload"></i> Seleccionar Documento</label>
                <input type="file" id="other_file_${documentCounter}" name="other_files[]" class="dropify" data-max-file-size="5M" required />
                <div class="form-group">
                    <label for="other_observation_${documentCounter}"><i class="fa fa-comment"></i> Observación (Opcional)</label>
                    <textarea id="other_observation_${documentCounter}" name="other_observations[]" class="form-control" rows="2" placeholder="Escribe una observación sobre este documento..."></textarea>
                </div>
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-danger btn-sm removeDocumentRow" data-row-id="${newRowId}"><i class="fa fa-trash"></i> Eliminar</button>
                </div>  
            </div>`;
        $('#otherDocsContainer').append(newFileInput);
        $(`#other_file_${documentCounter}`).dropify();
        documentCounter++;
    });

    // Eliminar la fila de documento añadida
    $(document).on('click', '.removeDocumentRow', function () {
        var rowId = $(this).data('row-id');
        $('#' + rowId).remove();
    });

    // Cargar documentos subidos al cargar la página
    cargarDocumentos();

    // Manejar la subida de CV
    $("#formCvUpload").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#cvUploadProgress').css('width', percentComplete + '%');
                        $('#cvUploadProgress').attr('aria-valuenow', percentComplete);
                        $('#cvUploadProgress').find('span').text(percentComplete + '% Complete');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: '../controlador/DocumentApplicantController.php?op=subirCv',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse.status) {
                        Toastify({
                            text: jsonResponse.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#4CAF50",
                            close: true
                        }).showToast();
                        cargarDocumentos();
                        $('#formCvUpload')[0].reset();
                        $('.dropify').dropify(); // Reinicializar Dropify
                        $('#cvUploadProgress').css('width', '0%');
                        $('#cvUploadProgress').find('span').text('0% Complete');
                    } else {
                        Toastify({
                            text: jsonResponse.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#F44336",
                            close: true
                        }).showToast();
                        $('#cvUploadProgress').css('width', '0%');
                        $('#cvUploadProgress').find('span').text('0% Complete');
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta JSON:", e);
                    Toastify({
                        text: "Error inesperado del servidor.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F44336",
                        close: true
                    }).showToast();
                    $('#cvUploadProgress').css('width', '0%');
                    $('#cvUploadProgress').find('span').text('0% Complete');
                }
            },
            error: function () {
                Swal.fire('Error', 'Ocurrió un error al subir el CV.', 'error');
                $('#cvUploadProgress').css('width', '0%');
                $('#cvUploadProgress').find('span').text('0% Complete');
            }
        });
    });

    // Manejar la subida de otros documentos
    $("#formOtherDocsUpload").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#otherDocsUploadProgress').css('width', percentComplete + '%');
                        $('#otherDocsUploadProgress').attr('aria-valuenow', percentComplete);
                        $('#otherDocsUploadProgress').find('span').text(percentComplete + '% Complete');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: '../controlador/DocumentApplicantController.php?op=subirOtrosDocumentos',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse.status) {
                        Toastify({
                            text: jsonResponse.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#4CAF50",
                            close: true
                        }).showToast();
                        cargarDocumentos();
                        $('#formOtherDocsUpload')[0].reset();
                        $('.dropify').dropify(); // Reinicializar Dropify
                        $('#otherDocsUploadProgress').css('width', '0%');
                        $('#otherDocsUploadProgress').find('span').text('0% Complete');
                    } else {
                        Toastify({
                            text: jsonResponse.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#F44336",
                            close: true
                        }).showToast();
                        $('#otherDocsUploadProgress').css('width', '0%');
                        $('#otherDocsUploadProgress').find('span').text('0% Complete');
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta JSON:", e);
                    Toastify({
                        text: "Error inesperado del servidor.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F44336",
                        close: true
                    }).showToast();
                    $('#otherDocsUploadProgress').css('width', '0%');
                    $('#otherDocsUploadProgress').find('span').text('0% Complete');
                }
            },
            error: function () {
                Swal.fire('Error', 'Ocurrió un error al subir los documentos.', 'error');
                $('#otherDocsUploadProgress').css('width', '0%');
                $('#otherDocsUploadProgress').find('span').text('0% Complete');
            }
        });
    });

    // Función para cargar los documentos subidos en la tabla
    function cargarDocumentos() {
        $.ajax({
            url: "../controlador/DocumentApplicantController.php?op=listarDocumentos",
            type: "GET",
            dataType: "json",
            success: function (jsonResponse) {
                if (jsonResponse.status) {
                    var tableBody = $("#documentList");
                    tableBody.empty();

                    if (jsonResponse.documents.length === 0) {
                        tableBody.append('<tr><td colspan="4" class="text-center">No se encontraron documentos subidos.</td></tr>');
                        return;
                    }

                    // Recorrer los documentos y agregarlos a la tabla
                    jsonResponse.documents.forEach(function (doc) {
                        var observation = doc.user_observation ? doc.user_observation : 'Sin observaciones';
                        var row = `
                            <tr>
                                <td>${doc.original_file_name}</td>
                                <td>${observation}</td>
                                <td>${doc.created_at}</td>
                                <td class="text-center">
                                    <a href="${doc.document_path}" target="_blank" class="btn btn-primary btn-sm mr-2"><i class="fa fa-eye"></i> Ver</a>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarDocumento(${doc.id})"><i class="fa fa-trash"></i> Eliminar</button>
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    Swal.fire('Información', jsonResponse.message, 'info');
                    $("#documentList").html('<tr><td colspan="4" class="text-center">No se encontraron documentos subidos.</td></tr>');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error al cargar los documentos.', 'error');
            }
        });
    }

    // Función para eliminar un documento subido
    window.eliminarDocumento = function (id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../controlador/DocumentApplicantController.php?op=eliminarDocumento",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function (jsonResponse) {
                        if (jsonResponse.status) {
                            Toastify({
                                text: jsonResponse.message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4CAF50",
                                close: true
                            }).showToast();
                            cargarDocumentos();
                        } else {
                            Toastify({
                                text: jsonResponse.message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#F44336",
                                close: true
                            }).showToast();
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error al eliminar el documento.', 'error');
                    }
                });
            }
        });
    };
});
