// scripts/dashboardSuperadmin.js

$(document).ready(function () {
    // Inicializar DataTables para la tabla de actividad
    $('#tabla-actividad').DataTable({
        "language": {
            "url": "assets/js/i18n/es-ES.json" // Asegúrate de que esta ruta sea correcta
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

    // Variables para almacenar los gráficos
    let usuariosChart;
    let documentosEstadoChart;

    let previousHash = null; // Almacena el hash de los datos anteriores

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardSuperadminController.php',
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

                // Actualizar tabla de Actividad Reciente
                actualizarTablaActividad(data.actividadReciente);
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

        if (usuariosChart) {
            // Actualizar datos
            usuariosChart.data.labels = labels;
            usuariosChart.data.datasets[0].data = valores;
            usuariosChart.update();
        } else {
            // Crear el gráfico si no existe
            usuariosChart = new Chart(ctx, {
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
        var backgroundColors = generateColorArray(labels2.length);

        if (documentosEstadoChart) {
            // Actualizar datos
            documentosEstadoChart.data.labels = labels2;
            documentosEstadoChart.data.datasets[0].data = valores2;
            documentosEstadoChart.data.datasets[0].backgroundColor = backgroundColors;
            documentosEstadoChart.update();
        } else {
            // Crear el gráfico si no existe
            documentosEstadoChart = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: labels2,
                    datasets: [{
                        data: valores2,
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

    // Función para actualizar la tabla de Actividad Reciente
    function actualizarTablaActividad(actividadReciente) {
        var table = $('#tabla-actividad').DataTable();
        table.clear().draw();
        actividadReciente.forEach(function (item) {
            table.row.add([
                item.username,
                item.action,
                item.access_time
            ]).draw(false);
        });
    }

    // Función para generar colores aleatorios (opcional)
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
