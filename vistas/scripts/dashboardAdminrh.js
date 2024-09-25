// scripts/dashboardAdminhr.js

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
            url: '../controlador/DashboardAdminrhController.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Verificar si hay error
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Actualizar tarjetas
                $('#total-users').text(data.totalUsers);
                $('#total-applicants').text(data.totalApplicants);
                $('#pending-documents').text(data.pendingDocumentsUsers + data.pendingDocumentsApplicants);
                $('#evaluated-documents').text(data.evaluatedDocumentsUsers + data.evaluatedDocumentsApplicants);

                // Actualizar gráfico de Usuarios Registrados por Mes
                actualizarGraficoUsers(data.usersPerMonth);

                // Actualizar gráfico de Candidatos Registrados por Mes
                actualizarGraficoApplicants(data.applicantsPerMonth);

                // Actualizar tabla de Actividad Reciente
                actualizarTablaActividad(data.recentActivities);

                // Actualizar gráfico de Documentos Evaluados por Estado (Usuarios)
                actualizarGraficoDocumentsStatusUsers(data.documentsByStatusUsers);

                // Actualizar gráfico de Documentos Evaluados por Estado (Candidatos)
                actualizarGraficoDocumentsStatusApplicants(data.documentsByStatusApplicants);

                // Actualizar gráfico de Usuarios Activos vs Inactivos
                actualizarGraficoUsersStatus(data.usersStatus);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    }

    // Función para actualizar el gráfico de Usuarios Registrados por Mes
    function actualizarGraficoUsers(usersPerMonth) {
        var ctx = document.getElementById('users-chart').getContext('2d');
        var labels = usersPerMonth.map(function (e) {
            return 'Mes ' + e.mes;
        });
        var valores = usersPerMonth.map(function (e) {
            return e.total;
        });

        // Destruir el gráfico anterior si existe
        if (window.usersChart instanceof Chart) {
            window.usersChart.destroy();
        }

        window.usersChart = new Chart(ctx, {
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

    // Función para actualizar el gráfico de Candidatos Registrados por Mes
    function actualizarGraficoApplicants(applicantsPerMonth) {
        var ctx = document.getElementById('applicants-chart').getContext('2d');
        var labels = applicantsPerMonth.map(function (e) {
            return 'Mes ' + e.mes;
        });
        var valores = applicantsPerMonth.map(function (e) {
            return e.total;
        });

        // Destruir el gráfico anterior si existe
        if (window.applicantsChart instanceof Chart) {
            window.applicantsChart.destroy();
        }

        window.applicantsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Candidatos Registrados',
                    data: valores,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
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
                            text: 'Número de Candidatos'
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
            var nombre = item.name + ' (' + item.type + ')';
            table.row.add([
                nombre,
                item.action,
                item.activity_time
            ]).draw(false);
        });
    }

    // Función para actualizar el gráfico de Documentos Evaluados por Estado (Usuarios)
    function actualizarGraficoDocumentsStatusUsers(documentsByStatusUsers) {
        var ctx = document.getElementById('documents-status-chart').getContext('2d');
        var labels = documentsByStatusUsers.map(function (e) {
            return e.estado;
        });
        var valores = documentsByStatusUsers.map(function (e) {
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
        if (window.documentsStatusChartUsers instanceof Chart) {
            window.documentsStatusChartUsers.destroy();
        }

        window.documentsStatusChartUsers = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: backgroundColors.slice(0, labels.length),
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

    // Función para actualizar el gráfico de Documentos Evaluados por Estado (Candidatos)
    function actualizarGraficoDocumentsStatusApplicants(documentsByStatusApplicants) {
        var ctx = document.getElementById('documents-status-chart').getContext('2d');
        var labels = documentsByStatusApplicants.map(function (e) {
            return e.estado;
        });
        var valores = documentsByStatusApplicants.map(function (e) {
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
        if (window.documentsStatusChartApplicants instanceof Chart) {
            window.documentsStatusChartApplicants.destroy();
        }

        window.documentsStatusChartApplicants = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: backgroundColors.slice(0, labels.length),
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

    // Función para actualizar el gráfico de Usuarios Activos vs Inactivos
    function actualizarGraficoUsersStatus(usersStatus) {
        var ctx = document.getElementById('users-status-chart').getContext('2d');
        var labels = ['Activos', 'Inactivos'];
        var valores = [usersStatus.activeUsers, usersStatus.inactiveUsers];

        // Colores
        var backgroundColors = [
            '#28a745', // Verde para Activos
            '#dc3545'  // Rojo para Inactivos
        ];

        // Destruir el gráfico anterior si existe
        if (window.usersStatusChart instanceof Chart) {
            window.usersStatusChart.destroy();
        }

        window.usersStatusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Usuarios',
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
                            text: 'Estado'
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

    // Llamar a la función para actualizar el dashboard al cargar la página
    actualizarDashboard();

    // Opcional: Actualizar el dashboard cada cierto tiempo (ejemplo: cada 5 minutos)
    setInterval(actualizarDashboard, 300000); // 300,000 ms = 5 minutos
});
