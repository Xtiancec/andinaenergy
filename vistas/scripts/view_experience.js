document.addEventListener('DOMContentLoaded', function () {
    // Inicializar DataTables con Bootstrap 4
    const tablaEducacion = $('#tablaExperienciaEducativa').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        paging: true,
        searching: true,
        ordering: true,
        order: [[2, 'desc']], // Ordenar por Fecha de Inicio descendente
        columnDefs: [
            { 
                targets: 5, // Archivo
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    const tablaTrabajo = $('#tablaExperienciaLaboral').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        paging: true,
        searching: true,
        ordering: true,
        order: [[2, 'desc']], // Ordenar por Fecha de Inicio descendente
        columnDefs: [
            { 
                targets: 4, // Archivo
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
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
                            archivoLink
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
                            archivoLink
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
});
