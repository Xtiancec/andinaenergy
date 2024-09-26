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

    // Variables para almacenar los gráficos
    let usersChart;
    let applicantsChart;
    let documentsStatusChartUsers;
    let documentsStatusChartApplicants;
    let usersStatusChart;

    let previousDataHash = null; // Almacena el hash de los datos anteriores

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardAdminrhController.php',
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
                if (previousDataHash === data.dataHash) {
                    console.log('No hay cambios en los datos. No se actualizan los gráficos.');
                    return; // No actualizar si no hay cambios
                }

                // Almacenar el nuevo hash para futuras comparaciones
                previousDataHash = data.dataHash;

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

        if (usersChart) {
            // Actualizar datos
            usersChart.data.labels = labels;
            usersChart.data.datasets[0].data = valores;
            usersChart.update();
        } else {
            // Crear el gráfico si no existe
            usersChart = new Chart(ctx, {
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

        if (applicantsChart) {
            // Actualizar datos
            applicantsChart.data.labels = labels;
            applicantsChart.data.datasets[0].data = valores;
            applicantsChart.update();
        } else {
            // Crear el gráfico si no existe
            applicantsChart = new Chart(ctx, {
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
        var ctx = document.getElementById('documents-status-chart-users').getContext('2d');
        var labels = documentsByStatusUsers.map(function (e) {
            return e.estado;
        });
        var valores = documentsByStatusUsers.map(function (e) {
            return e.total;
        });

        if (documentsStatusChartUsers) {
            // Actualizar datos
            documentsStatusChartUsers.data.labels = labels;
            documentsStatusChartUsers.data.datasets[0].data = valores;
            documentsStatusChartUsers.update();
        } else {
            // Crear el gráfico si no existe
            documentsStatusChartUsers = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: generateColorArray(labels.length),
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

    // Función para actualizar el gráfico de Documentos Evaluados por Estado (Candidatos)
    function actualizarGraficoDocumentsStatusApplicants(documentsByStatusApplicants) {
        var ctx = document.getElementById('documents-status-chart-applicants').getContext('2d');
        var labels = documentsByStatusApplicants.map(function (e) {
            return e.estado;
        });
        var valores = documentsByStatusApplicants.map(function (e) {
            return e.total;
        });

        if (documentsStatusChartApplicants) {
            // Actualizar datos
            documentsStatusChartApplicants.data.labels = labels;
            documentsStatusChartApplicants.data.datasets[0].data = valores;
            documentsStatusChartApplicants.update();
        } else {
            // Crear el gráfico si no existe
            documentsStatusChartApplicants = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: generateColorArray(labels.length),
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

    // Función para actualizar el gráfico de Usuarios Activos vs Inactivos
    function actualizarGraficoUsersStatus(usersStatus) {
        var ctx = document.getElementById('users-status-chart').getContext('2d');
        var labels = ['Activos', 'Inactivos'];
        var valores = [usersStatus.activeUsers, usersStatus.inactiveUsers];

        if (usersStatusChart) {
            // Actualizar datos
            usersStatusChart.data.datasets[0].data = valores;
            usersStatusChart.update();
        } else {
            // Crear el gráfico si no existe
            usersStatusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Usuarios',
                        data: valores,
                        backgroundColor: [
                            '#28a745', // Verde para Activos
                            '#dc3545'  // Rojo para Inactivos
                        ],
                        borderColor: [
                            '#28a745',
                            '#dc3545'
                        ],
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
