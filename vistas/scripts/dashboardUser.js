// scripts/dashboardUser.js

$(document).ready(function () {
    // Inicializar DataTables para la tabla de documentos
    $('#tabla-documentos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true
    });

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardUserController.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    alert(data.error);
                    return;
                }

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

        // Destruir el gráfico anterior si existe
        if (window.documentsTypeChart instanceof Chart) {
            window.documentsTypeChart.destroy();
        }

        window.documentsTypeChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: [
                        '#36A2EB', // Azul para Opcional
                        '#FF6384'  // Rojo para Obligatorio
                    ],
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

    // Función para actualizar el gráfico de Documentos por Estado
    function actualizarGraficoDocumentsStatus(documentsByStatus) {
        var ctx = document.getElementById('documents-status-chart').getContext('2d');
        var labels = documentsByStatus.map(function (e) {
            return e.state_name;
        });
        var valores = documentsByStatus.map(function (e) {
            return e.total;
        });

        // Colores dinámicos
        var backgroundColors = [
            '#28a745', // Verde para Aprobado
            '#ffc107', // Amarillo para Pendiente
            '#dc3545', // Rojo para Rechazado
            '#17a2b8', // Azul para Por Corregir
            '#6c757d'  // Gris para Otros
        ];

        // Destruir el gráfico anterior si existe
        if (window.documentsStatusChart instanceof Chart) {
            window.documentsStatusChart.destroy();
        }

        window.documentsStatusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Documentos',
                    data: valores,
                    backgroundColor: backgroundColors.slice(0, labels.length),
                    borderColor: backgroundColors.slice(0, labels.length),
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
        var table = $('#tabla-documentos').DataTable();
        table.clear().draw();
        documentsList.forEach(function (item) {
            var acciones = `
                <a href="${item.document_path}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-download"></i> Descargar</a>
                <a href="delete_document.php?id=${item.id}" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este documento?');"><i class="fas fa-trash"></i> Eliminar</a>
            `;
            table.row.add([
                item.documentName,
                capitalizeFirstLetter(item.document_type),
                item.state_name,
                item.uploaded_at,
                item.admin_observation ? item.admin_observation : '-',
                acciones
            ]).draw(false);
        });
    }

    // Función para capitalizar la primera letra
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Llamar a la función para actualizar el dashboard al cargar la página
    actualizarDashboard();

    // Opcional: Actualizar el dashboard cada cierto tiempo (ejemplo: cada 5 minutos)
    setInterval(actualizarDashboard, 300000); // 300,000 ms = 5 minutos
});
