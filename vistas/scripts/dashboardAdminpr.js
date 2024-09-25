// scripts/dashboardAdminpr.js

$(document).ready(function () {
    // Inicializar DataTables para la tabla de actividad reciente
    $('#tabla-actividad').DataTable({
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
            url: '../controlador/DashboardAdminprController.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Actualizar tarjetas
                $('#total-suppliers').text(data.totalSuppliers);
                $('#total-supplier-users').text(data.totalSupplierUsers);
                $('#pending-documents').text(data.pendingDocuments);
                $('#evaluated-documents').text(data.evaluatedDocuments);

                // Actualizar gráfico de Proveedores Registrados por Mes
                actualizarGraficoSuppliers(data.suppliersPerMonth);

                // Actualizar tabla de Actividad Reciente
                actualizarTablaActividad(data.recentActivities);

                // Actualizar gráfico de Documentos Evaluados por Estado
                actualizarGraficoDocumentsStatus(data.documentsByStatus);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    }

    // Función para actualizar el gráfico de Proveedores Registrados por Mes
    function actualizarGraficoSuppliers(suppliersPerMonth) {
        var ctx = document.getElementById('suppliers-chart').getContext('2d');
        var labels = suppliersPerMonth.map(function (e) {
            return 'Mes ' + e.mes;
        });
        var valores = suppliersPerMonth.map(function (e) {
            return e.total;
        });

        // Destruir el gráfico anterior si existe
        if (window.suppliersChart instanceof Chart) {
            window.suppliersChart.destroy();
        }

        window.suppliersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Proveedores Registrados',
                    data: valores,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
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
                            text: 'Número de Proveedores'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mes'
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

    // Función para actualizar la tabla de Actividad Reciente
    function actualizarTablaActividad(recentActivities) {
        var table = $('#tabla-actividad').DataTable();
        table.clear().draw();
        recentActivities.forEach(function (item) {
            table.row.add([
                item.supplier_name,
                item.action,
                item.activity_time
            ]).draw(false);
        });
    }

    // Función para actualizar el gráfico de Documentos Evaluados por Estado
    function actualizarGraficoDocumentsStatus(documentsByStatus) {
        var ctx2 = document.getElementById('documents-status-chart').getContext('2d');
        var labels2 = documentsByStatus.map(function (e) {
            return e.estado;
        });
        var valores2 = documentsByStatus.map(function (e) {
            return e.total;
        });

        // Colores dinámicos
        var backgroundColors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40'
        ];

        // Destruir el gráfico anterior si existe
        if (window.documentsStatusChart instanceof Chart) {
            window.documentsStatusChart.destroy();
        }

        window.documentsStatusChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: labels2,
                datasets: [{
                    data: valores2,
                    backgroundColor: backgroundColors.slice(0, labels2.length),
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

    // Llamar a la función para actualizar el dashboard al cargar la página
    actualizarDashboard();

    // Opcional: Actualizar el dashboard cada cierto tiempo (ejemplo: cada 5 minutos)
    setInterval(actualizarDashboard, 300000); // 300,000 ms = 5 minutos
});
