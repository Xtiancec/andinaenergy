// scripts/dashboardSuperadmin.js

$(document).ready(function () {
    // Inicializar DataTables

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardSuperadminController.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Actualizar tarjetas
                $('#total-usuarios').text(data.totalUsuarios);
                $('#total-postulantes').text(data.totalPostulantes);
                $('#total-empresas').text(data.totalEmpresas);

                $('#documentos-pendientesUser').text(data.documentosPendientesUser);
                $('#documentos-pendientesApplicant').text(data.documentosPendientesApplicant);
                $('#documentos-pendientesSupplier').text(data.documentosPendientesSupplier);

                // Actualizar gráfico de Usuarios Registrados por Mes
                actualizarGraficoUsuarios(data.usuariosPorMes);

                // Actualizar gráfico de Documentos por Estado
                actualizarGraficoDocumentosEstado(data.documentosPorEstado);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    }

    // Función para actualizar el gráfico de Usuarios Registrados por Mes
    function actualizarGraficoUsuarios(usuariosPorMes) {
        var ctx = document.getElementById('usuarios-chart').getContext('2d');
        var labels = usuariosPorMes.map(function (e) {
            return 'Mes ' + e.mes;
        });
        var valores = usuariosPorMes.map(function (e) {
            return e.total;
        });

        // Destruir el gráfico anterior si existe
        if (window.usuariosChart instanceof Chart) {
            window.usuariosChart.destroy();
        }

        window.usuariosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Usuarios Registrados',
                    data: valores,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                            text: 'Número de Usuarios'
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



    // Función para actualizar el gráfico de Documentos por Estado
    function actualizarGraficoDocumentosEstado(documentosPorEstado) {
        var ctx2 = document.getElementById('documentos-estado-chart').getContext('2d');
        var labels2 = documentosPorEstado.map(function (e) {
            return e.estado;
        });
        var valores2 = documentosPorEstado.map(function (e) {
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
        if (window.documentosEstadoChart instanceof Chart) {
            window.documentosEstadoChart.destroy();
        }

        window.documentosEstadoChart = new Chart(ctx2, {
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
