// scripts/dashboardApplicant.js

document.addEventListener('DOMContentLoaded', function () {
    const dashboardDataElement = document.getElementById('dashboardData');
    
    if (!dashboardDataElement) {
        console.error('Elemento #dashboardData no encontrado en el DOM.');
        return;
    }

    const applicantId = dashboardDataElement.dataset.applicantId;

    if (!applicantId) {
        console.error('ID de postulante no proporcionado.');
        return;
    }

    // Referencias a los gráficos existentes
    let documentsChart;
    let accessLogsChart;
    let evaluationChart;
    let processChart;
    let educationChart;
    let documentTypeChart;
    let experienceChart;
    let documentsStatusChart; // Nuevo gráfico
    let allApprovedIndicator; // Indicador de aprobación

    // Nuevos gráficos
    let turnoverChartEcharts; // eCharts
    let employeeDepartmentDonut; // Morris.js

    let previousData = null;

    // Función para generar colores aleatorios
    function generateColorArray(length) {
        const colors = [];
        for (let i = 0; i < length; i++) {
            const hue = Math.floor(Math.random() * 360);
            const color = `hsl(${hue}, 70%, 50%)`;
            colors.push(color);
        }
        return colors;
    }

    // Función para cargar y actualizar los datos del dashboard
    function loadDashboardData() {
        console.log('Cargando datos...');

        fetch(`../controlador/DashboardApplicantController.php?applicant_id=${applicantId}&t=${new Date().getTime()}`, {
            cache: 'no-store',
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);

            // Verificar si hay un error en la respuesta
            if (data.error) {
                console.error(`Error del servidor: ${data.error}`);
                return;
            }

            // Comparar los datos actuales con los anteriores para evitar actualizaciones innecesarias
            if (JSON.stringify(previousData) === JSON.stringify(data)) {
                console.log('No hay cambios en los datos. No se actualizan los gráficos.');
                return;
            }

            // Almacenar los nuevos datos para futuras comparaciones
            previousData = data;

            // Actualizar cada gráfico con los nuevos datos
            actualizarProgresoDocumentos(data.documents_progress);
            actualizarHistorialAccesos(data.access_logs);
            actualizarEstadoEvaluacion(data.evaluation);
            actualizarProcesoSeleccion(data.selection_state);
            actualizarProgresoEducativo(data.education);
            actualizarDocumentosPorTipo(data.documents_by_type);
            actualizarExperienciaLaboral(data.experience);
            actualizarEstadoDocumentos(data.documents_status);
            actualizarIndicadorAprobacion(data.all_documents_approved);
            actualizarTurnoverEmpleados(data.turnover_per_month);
            actualizarDistribucionDepartamentos(data.employee_by_department);
        })
        .catch(error => console.error('Error al cargar los datos del dashboard:', error));
    }

    // Funciones para actualizar cada gráfico

    function actualizarProgresoDocumentos(documentsProgress) {
        if (documentsProgress.length > 0 && documentsProgress[0].total_documentos > 0) {
            const ctx = document.getElementById('documentsChart').getContext('2d');
            const subidos = documentsProgress[0].documentos_subidos;
            const faltantes = documentsProgress[0].total_documentos - subidos;

            if (documentsChart) {
                // Actualizar datos
                documentsChart.data.datasets[0].data = [subidos, faltantes];
                documentsChart.update();
            } else {
                // Crear gráfico si no existe
                documentsChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Documentos Subidos', 'Faltantes'],
                        datasets: [{
                            data: [subidos, faltantes],
                            backgroundColor: ['#36A2EB', '#FF6384']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Progreso de Documentos'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarHistorialAccesos(accessLogs) {
        if (accessLogs && accessLogs.length > 0) {
            const ctx = document.getElementById('accessLogsChart').getContext('2d');
            const labels = accessLogs.map(log => log.fecha);
            const accesos = accessLogs.map(log => log.accesos);

            if (accessLogsChart) {
                // Actualizar datos
                accessLogsChart.data.labels = labels;
                accessLogsChart.data.datasets[0].data = accesos;
                accessLogsChart.update();
            } else {
                // Crear gráfico si no existe
                accessLogsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Accesos',
                            data: accesos,
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Historial de Accesos'
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Accesos'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Fecha'
                                }
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarEstadoEvaluacion(evaluation) {
        if (evaluation.length > 0) {
            const ctx = document.getElementById('evaluationChart').getContext('2d');
            const revisados = evaluation[0].revisados;
            const no_revisados = evaluation[0].no_revisados;

            if (evaluationChart) {
                // Actualizar datos
                evaluationChart.data.datasets[0].data = [revisados, no_revisados];
                evaluationChart.update();
            } else {
                // Crear gráfico si no existe
                evaluationChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Revisados', 'No Revisados'],
                        datasets: [{
                            data: [revisados, no_revisados],
                            backgroundColor: ['#36A2EB', '#FF6384']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Estado de Evaluación de Documentos'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarProcesoSeleccion(selectionState) {
        if (selectionState.length > 0) {
            const ctx = document.getElementById('processChart').getContext('2d');
            const stateName = selectionState[0].state_name;

            if (processChart) {
                // Actualizar datos
                processChart.data.datasets[0].label = stateName;
                processChart.update();
            } else {
                // Crear gráfico si no existe
                processChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Progreso'],
                        datasets: [{
                            label: stateName,
                            data: [1],
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Estado del Proceso de Selección'
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 1,
                                ticks: {
                                    stepSize: 1,
                                    display: false
                                },
                                grid: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        } else {
            // Si no hay estado de selección, mostrar mensaje
            const processChartCard = document.getElementById('processChart').parentElement;
            processChartCard.innerHTML = '<p class="text-muted">No disponible</p>';
        }
    }

    function actualizarProgresoEducativo(education) {
        if (education.length > 0) {
            const ctx = document.getElementById('educationChart').getContext('2d');
            const labels = education.map(item => item.education_type);
            const data = education.map(item => item.total);

            if (educationChart) {
                // Actualizar datos
                educationChart.data.labels = labels;
                educationChart.data.datasets[0].data = data;
                educationChart.update();
            } else {
                // Crear gráfico si no existe
                educationChart = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Progreso Educativo',
                            data: data,
                            backgroundColor: 'rgba(54,162,235,0.2)',
                            borderColor: '#36A2EB',
                            pointBackgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Progreso Educativo'
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarDocumentosPorTipo(documentsByType) {
        if (documentsByType.length > 0) {
            const ctx = document.getElementById('documentTypeChart').getContext('2d');
            const labels = documentsByType.map(item => item.document_name);
            const data = documentsByType.map(item => item.total);

            if (documentTypeChart) {
                // Actualizar datos
                documentTypeChart.data.labels = labels;
                documentTypeChart.data.datasets[0].data = data;
                documentTypeChart.update();
            } else {
                // Crear gráfico si no existe
                documentTypeChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: generateColorArray(data.length)
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Documentos Subidos por Tipo'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarExperienciaLaboral(experience) {
        if (experience.length > 0 && experience[0].total_experiencia !== null) {
            const ctx = document.getElementById('experienceChart').getContext('2d');
            const totalYears = experience[0].total_experiencia || 0;

            if (experienceChart) {
                // Actualizar datos
                experienceChart.data.datasets[0].data = [totalYears];
                experienceChart.update();
            } else {
                // Crear gráfico si no existe
                experienceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Experiencia Total (Años)'],
                        datasets: [{
                            label: 'Años de Experiencia',
                            data: [totalYears],
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Total de Años de Experiencia Laboral'
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Años'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Categoría'
                                }
                            }
                        }
                    }
                });
            }
        } else {
            // Si no hay experiencia, mostrar mensaje
            const experienceChartCard = document.getElementById('experienceChart').parentElement;
            experienceChartCard.innerHTML = '<p class="text-muted">No hay experiencia registrada.</p>';
        }
    }

    function actualizarEstadoDocumentos(documentsStatus) {
        if (documentsStatus.length > 0) {
            const ctx = document.getElementById('documentsStatusChart').getContext('2d');
            const labels = documentsStatus.map(item => item.state_name);
            const data = documentsStatus.map(item => item.total);

            if (documentsStatusChart) {
                // Actualizar datos
                documentsStatusChart.data.labels = labels;
                documentsStatusChart.data.datasets[0].data = data;
                documentsStatusChart.update();
            } else {
                // Crear gráfico si no existe
                documentsStatusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: generateColorArray(data.length)
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Estado de los Documentos'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    }

    function actualizarIndicadorAprobacion(allApproved) {
        const indicator = document.getElementById('allApprovedIndicator');
        if (!indicator) {
            console.error('Elemento #allApprovedIndicator no encontrado en el DOM.');
            return;
        }

        if (allApproved) {
            indicator.innerHTML = '&#10004;'; // Símbolo de Check
            indicator.style.color = 'green';
        } else {
            indicator.innerHTML = '&#10060;'; // Símbolo de X
            indicator.style.color = 'red';
        }
    }

    function actualizarTurnoverEmpleados(turnoverPerMonth) {
        if (turnoverPerMonth.length > 0) {
            const chartDom = document.getElementById('turnoverChartEcharts');
            if (!chartDom) {
                console.error('Elemento #turnoverChartEcharts no encontrado en el DOM.');
                return;
            }

            // Inicializar eCharts
            turnoverChartEcharts = echarts.init(chartDom);

            const labels = turnoverPerMonth.map(item => `Mes ${item.mes}`);
            const data = turnoverPerMonth.map(item => item.total);

            const option = {
                title: {
                    text: 'Turnover de Empleados por Mes',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: {
                    type: 'category',
                    data: labels,
                    axisLabel: {
                        rotate: 45
                    },
                    axisTick: {
                        alignWithLabel: true
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Número de Empleados'
                },
                series: [{
                    data: data,
                    type: 'bar',
                    barWidth: '60%',
                    itemStyle: {
                        color: '#ff7f50'
                    }
                }]
            };

            turnoverChartEcharts.setOption(option);
        }
    }

    function actualizarDistribucionDepartamentos(employeeByDepartment) {
        if (employeeByDepartment.length > 0) {
            const element = document.getElementById('employeeDepartmentDonut');
            if (!element) {
                console.error('Elemento #employeeDepartmentDonut no encontrado en el DOM.');
                return;
            }

            // Preparar datos para Morris.js
            const data = employeeByDepartment.map(item => ({
                label: item.departamento,
                value: item.total
            }));

            // Inicializar Morris.js Donut
            employeeDepartmentDonut = new Morris.Donut({
                element: 'employeeDepartmentDonut',
                data: data,
                colors: ['#1abc9c', '#3498db', '#9b59b6', '#e74c3c', '#f1c40f', '#2ecc71'],
                resize: true,
                formatter: function (x, data) { return x }
            });
        }
    }

    // Cargar los datos inicialmente y luego cada 5 segundos
    loadDashboardData();
    setInterval(loadDashboardData, 5000);
});
