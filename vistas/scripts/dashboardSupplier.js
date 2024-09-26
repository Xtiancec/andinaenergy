// scripts/dashboardSupplier.js

$(document).ready(function () {
    // Inicializar DataTables para la tabla de documentos
    $('#tabla-documentos').DataTable({
        
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    // Variables para almacenar los gráficos
    let documentsTypeChart;
    let documentsStatusChart;
    let mandatoryProgressBar;

    let previousData = null;

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardSupplierController.php',
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Comparar datos anteriores con los nuevos
                if (JSON.stringify(previousData) === JSON.stringify(data)) {
                    console.log('No hay cambios en los datos. No se actualizan los gráficos.');
                    return; // No actualizar si no hay cambios
                }

                // Almacenar los nuevos datos para futuras comparaciones
                previousData = data;

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
        const ctx = document.getElementById('documents-type-chart').getContext('2d');
        const labels = documentsByType.map(e => e.documentName);
        const valores = documentsByType.map(e => e.total);

        // Destruir el gráfico anterior si existe
        if (documentsTypeChart) {
            documentsTypeChart.destroy();
        }

        documentsTypeChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: generateColorArray(valores.length),
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
                    },
                    title: {
                        display: true,
                        text: 'Documentos Subidos por Tipo'
                    }
                }
            }
        });
    }

    // Función para actualizar el gráfico de Documentos por Estado
    function actualizarGraficoDocumentsStatus(documentsByStatus) {
        const ctx = document.getElementById('documents-status-chart').getContext('2d');
        const labels = documentsByStatus.map(e => e.state_name);
        const valores = documentsByStatus.map(e => e.total);

        // Colores personalizados basados en el estado
        const colorMap = {
            'Aprobado': '#28a745',    // Verde
            'Subido': '#17a2b8',      // Azul
            'Rechazado': '#dc3545',   // Rojo
            'Por Corregir': '#ffc107' // Amarillo
            // Añade más si es necesario
        };
        const backgroundColors = labels.map(label => colorMap[label] || '#6c757d'); // Gris por defecto

        // Destruir el gráfico anterior si existe
        if (documentsStatusChart) {
            documentsStatusChart.destroy();
        }

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
                    },
                    title: {
                        display: true,
                        text: 'Documentos por Estado'
                    }
                }
            }
        });
    }

    // Función para actualizar el progreso de Documentos Obligatorios
    function actualizarProgresoObligatorios(progress, statusText) {
        const progressBar = $('#mandatory-progress');
        progressBar.css('width', progress + '%');
        progressBar.attr('aria-valuenow', progress);
        progressBar.text(progress + '%');

        $('#mandatory-status').text(statusText);
    }

    // Función para actualizar la tabla de Documentos
    function actualizarTablaDocumentos(documentsList) {
        const table = $('#tabla-documentos').DataTable();
        table.clear().draw();
        documentsList.forEach(function (item) {
            const acciones = `
                <a href="${item.document_path}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-download"></i> Descargar</a>
                <a href="delete_document_supplier.php?id=${item.id}" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este documento?');"><i class="fas fa-trash"></i> Eliminar</a>
            `;
            table.row.add([
                item.documentName,
                item.document_type,
                item.state_name,
                item.uploaded_at,
                item.admin_observation,
                acciones
            ]).draw(false);
        });
    }

    // Función para generar colores aleatorios para los gráficos de pastel y doughnut
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
