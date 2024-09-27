document.addEventListener('DOMContentLoaded', function () {
    // Inicializar DataTables con Bootstrap 4
    const tablaEducacion = $('#tablaExperienciaEducativa').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [5,6] } // Desactivar ordenamiento en columnas Archivo y Acciones
        ]
    });
    const tablaTrabajo = $('#tablaExperienciaLaboral').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [4,5] } // Desactivar ordenamiento en columnas Archivo y Acciones
        ]
    });

    // Función para obtener experiencias educativas
    function obtenerEducacion() {
        fetch('../controlador/ExperienceController.php?op=mostrarEducacion', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Error ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (Array.isArray(data)) {
                    tablaEducacion.clear();
                    data.forEach(item => {
                        const archivoLink = item.file_path ? `
                            <a href="../${item.file_path}" target="_blank" class="btn btn-sm btn-info" title="Ver Archivo">
                                <i class="fa fa-eye"></i>
                            </a>
                        ` : 'N/A';
                        tablaEducacion.row.add([
                            item.institution,
                            item.education_type,
                            item.start_date,
                            item.end_date,
                            `${item.duration} ${item.duration_unit}`,
                            archivoLink,
                            `
                            <button class="btn btn-sm btn-primary editarEducacion" data-id="${item.id}" data-institution="${item.institution}" data-education_type="${item.education_type}" data-start_date="${item.start_date}" data-end_date="${item.end_date}" data-duration="${item.duration}" data-duration_unit="${item.duration_unit}" title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger eliminarEducacion" data-id="${item.id}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                            `
                        ]);
                    });
                    tablaEducacion.draw();
                } else if (data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Respuesta inesperada del servidor.',
                    });
                }
            })
            .catch(error => {
                console.error('Error al obtener experiencias educativas:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar las experiencias educativas.',
                });
            });
    }

    // Función para obtener experiencias laborales
    function obtenerTrabajo() {
        fetch('../controlador/ExperienceController.php?op=mostrarTrabajo', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Error ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (Array.isArray(data)) {
                    tablaTrabajo.clear();
                    data.forEach(item => {
                        const archivoLink = item.file_path ? `
                            <a href="../${item.file_path}" target="_blank" class="btn btn-sm btn-info" title="Ver Archivo">
                                <i class="fa fa-eye"></i>
                            </a>
                        ` : 'N/A';
                        tablaTrabajo.row.add([
                            item.company,
                            item.position,
                            item.start_date,
                            item.end_date,
                            archivoLink,
                            `
                            <button class="btn btn-sm btn-primary editarTrabajo" data-id="${item.id}" data-company="${item.company}" data-position="${item.position}" data-start_date="${item.start_date}" data-end_date="${item.end_date}" title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger eliminarTrabajo" data-id="${item.id}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                            `
                        ]);
                    });
                    tablaTrabajo.draw();
                } else if (data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Respuesta inesperada del servidor.',
                    });
                }
            })
            .catch(error => {
                console.error('Error al obtener experiencias laborales:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar las experiencias laborales.',
                });
            });
    }

    // Inicializar datos al cargar la página
    obtenerEducacion();
    obtenerTrabajo();

    // Manejar el envío del formulario de educación
    document.getElementById('formEducacion').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../controlador/ExperienceController.php?op=guardarEducacion', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    $('#modalEducacion').modal('hide');
                    obtenerEducacion();
                    Toastify({
                        text: "Experiencia educativa guardada correctamente.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        stopOnFocus: true,
                    }).showToast();
                    this.reset(); // Resetear el formulario
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error al guardar experiencia educativa:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al guardar la experiencia educativa.',
                });
            });
    });

    // Manejar el envío del formulario de trabajo
    document.getElementById('formTrabajo').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../controlador/ExperienceController.php?op=guardarTrabajo', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    $('#modalTrabajo').modal('hide');
                    obtenerTrabajo();
                    Toastify({
                        text: "Experiencia laboral guardada correctamente.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        stopOnFocus: true,
                    }).showToast();
                    this.reset(); // Resetear el formulario
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error al guardar experiencia laboral:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al guardar la experiencia laboral.',
                });
            });
    });

    // Manejar acciones de editar y eliminar en la tabla de educación
    document.querySelector('#tablaExperienciaEducativa tbody').addEventListener('click', function (e) {
        // Editar Experiencia Educativa
        if (e.target.closest('.editarEducacion')) {
            const button = e.target.closest('.editarEducacion');
            const id = button.getAttribute('data-id');
            const institution = button.getAttribute('data-institution');
            const education_type = button.getAttribute('data-education_type');
            const start_date = button.getAttribute('data-start_date');
            const end_date = button.getAttribute('data-end_date');
            const duration = button.getAttribute('data-duration');
            const duration_unit = button.getAttribute('data-duration_unit');

            // Rellenar el formulario del modal con los datos existentes
            document.getElementById('modalEducacionLabel').innerText = 'Editar Experiencia Educativa';
            document.getElementById('educacion_id').value = id;
            document.getElementById('institution').value = institution;
            document.getElementById('education_type').value = education_type;
            document.getElementById('start_date_education').value = start_date;
            document.getElementById('end_date_education').value = end_date;
            document.getElementById('duration_education').value = duration;
            document.getElementById('duration_unit_education').value = duration_unit;

            // Mostrar el modal
            $('#modalEducacion').modal('show');
        }

        // Eliminar Experiencia Educativa
        if (e.target.closest('.eliminarEducacion')) {
            const button = e.target.closest('.eliminarEducacion');
            const id = button.getAttribute('data-id');

            // Confirmar eliminación con SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../controlador/ExperienceController.php?op=eliminarEducacion`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}`
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`Error ${response.status}: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status) {
                                obtenerEducacion();
                                Toastify({
                                    text: "Experiencia educativa eliminada correctamente.",
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545",
                                    stopOnFocus: true,
                                }).showToast();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar experiencia educativa:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al eliminar la experiencia educativa.',
                            });
                        });
                }
            });
        }
    });

    // Manejar acciones de editar y eliminar en la tabla de trabajo
    document.querySelector('#tablaExperienciaLaboral tbody').addEventListener('click', function (e) {
        // Editar Experiencia Laboral
        if (e.target.closest('.editarTrabajo')) {
            const button = e.target.closest('.editarTrabajo');
            const id = button.getAttribute('data-id');
            const company = button.getAttribute('data-company');
            const position = button.getAttribute('data-position');
            const start_date = button.getAttribute('data-start_date');
            const end_date = button.getAttribute('data-end_date');

            // Rellenar el formulario del modal con los datos existentes
            document.getElementById('modalTrabajoLabel').innerText = 'Editar Experiencia Laboral';
            document.getElementById('trabajo_id').value = id;
            document.getElementById('company').value = company;
            document.getElementById('position').value = position;
            document.getElementById('start_date_work').value = start_date;
            document.getElementById('end_date_work').value = end_date;

            // Mostrar el modal
            $('#modalTrabajo').modal('show');
        }

        // Eliminar Experiencia Laboral
        if (e.target.closest('.eliminarTrabajo')) {
            const button = e.target.closest('.eliminarTrabajo');
            const id = button.getAttribute('data-id');

            // Confirmar eliminación con SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../controlador/ExperienceController.php?op=eliminarTrabajo`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}`
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`Error ${response.status}: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status) {
                                obtenerTrabajo();
                                Toastify({
                                    text: "Experiencia laboral eliminada correctamente.",
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545",
                                    stopOnFocus: true,
                                }).showToast();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar experiencia laboral:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al eliminar la experiencia laboral.',
                            });
                        });
                }
            });
        }
    });
});
