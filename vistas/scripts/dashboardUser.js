// scripts/dashboardUser.js

$(document).ready(function () {
    // Inicializar DataTables para la tabla de documentos
    var documentosTable = $('#tabla-documentos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    // Variables para almacenar los gráficos
    var documentsTypeChart;
    var documentsStatusChart;

    var previousHash = null; // Almacena el hash de los datos anteriores

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardUserController.php',
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Verificar si los datos han cambiado usando el hash
                if (previousHash === data.dataHash) {
                    console.log('No hay cambios en los datos. No se actualizan los gráficos.');
                    return; // No actualizar si no hay cambios
                }

                // Almacenar el nuevo hash para futuras comparaciones
                previousHash = data.dataHash;

                // Actualizar tarjetas
                $('#total-documents').text(data.totalDocuments);
                $('#approved-documents').text(data.approvedDocuments);
                $('#pending-documents').text(data.pendingDocuments);
                $('#rejected-documents').text(data.rejectedDocuments);

                // Actualizar gráfico de Documentos Subidos por Tipo
                actualizarGraficoDocumentsType(data.documentsByType);

                // Actualizar gráfico de Documentos por Estado
                actualizarGraficoDocumentsStatus(data.documentsByStatus);

                // Actualizar progreso de Documentos Obligatorios
                actualizarProgresoObligatorios(data.mandatoryProgress, data.mandatoryStatus);

                // Actualizar tabla de Documentos
                actualizarTablaDocumentos(data.documentsList);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    }

    // Función para actualizar el gráfico de Documentos Subidos por Tipo
    function actualizarGraficoDocumentsType(documentsByType) {
        var ctx = document.getElementById('documents-type-chart').getContext('2d');
        var labels = documentsByType.map(function (e) {
            return e.documentName;
        });
        var valores = documentsByType.map(function (e) {
            return e.total;
        });

        // Generar colores dinámicos
        var backgroundColors = generateColorArray(labels.length);

        if (documentsTypeChart) {
            // Actualizar datos
            documentsTypeChart.data.labels = labels;
            documentsTypeChart.data.datasets[0].data = valores;
            documentsTypeChart.data.datasets[0].backgroundColor = backgroundColors;
            documentsTypeChart.update();
        } else {
            // Crear el gráfico si no existe
            documentsTypeChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            enabled: true
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Función para actualizar el gráfico de Documentos por Estado
    function actualizarGraficoDocumentsStatus(documentsByStatus) {
        var ctx = document.getElementById('documents-status-chart').getContext('2d');
        var labels = documentsByStatus.map(function (e) {
            return e.state_name;
        });
        var valores = documentsByStatus.map(function (e) {
            return e.total;
        });

        // Generar colores dinámicos
        var backgroundColors = generateColorArray(labels.length);

        if (documentsStatusChart) {
            // Actualizar datos
            documentsStatusChart.data.labels = labels;
            documentsStatusChart.data.datasets[0].data = valores;
            documentsStatusChart.data.datasets[0].backgroundColor = backgroundColors;
            documentsStatusChart.update();
        } else {
            // Crear el gráfico si no existe
            documentsStatusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Documentos',
                        data: valores,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { // Para Chart.js v3 y superior
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Estado del Documento'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: true
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }

    // Función para actualizar el progreso de Documentos Obligatorios
    function actualizarProgresoObligatorios(progress, statusText) {
        var progressBar = $('#mandatory-progress');
        progressBar.css('width', progress + '%');
        progressBar.attr('aria-valuenow', progress);
        progressBar.text(progress + '%');

        $('#mandatory-status').text(statusText);
    }

    // Función para actualizar la tabla de Documentos
    function actualizarTablaDocumentos(documentsList) {
        documentosTable.clear().draw();
        documentsList.forEach(function (item) {
            var acciones = `
                <a href="${item.document_path}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-download"></i> Descargar</a>
                <a href="delete_document.php?id=${item.id}" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este documento?');"><i class="fas fa-trash"></i> Eliminar</a>
            `;
            documentosTable.row.add([
                item.documentName,
                capitalizeFirstLetter(item.document_type),
                item.state_name,
                item.uploaded_at,
                item.admin_observation ? item.admin_observation : '-',
                acciones
            ]);
        });
        documentosTable.draw(false);
    }

    // Función para capitalizar la primera letra
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Función para generar colores aleatorios para los gráficos de pastel y barras
    function generateColorArray(length) {
        const colors = [];
        for (let i = 0; i < length; i++) {
            const color = `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`;
            colors.push(color);
        }
        return colors;
    }

    // Llamar a la función para actualizar el dashboard al cargar la página
    actualizarDashboard();

    // Actualizar el dashboard cada cierto tiempo (ejemplo: cada 5 segundos)
    setInterval(actualizarDashboard, 5000); // 5000 ms = 5 segundos
});
