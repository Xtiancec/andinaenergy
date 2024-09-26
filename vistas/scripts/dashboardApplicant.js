// scripts/dashboardApplicant.js

document.addEventListener('DOMContentLoaded', function () {
    const applicantId = document.getElementById('dashboardData').dataset.applicantId;

    let documentsChart;
    let accessLogsChart;
    let evaluationChart;
    let processChart;
    let educationChart;
    let documentTypeChart;
    let experienceChart;
    let documentsStatusChart; // Nuevo gráfico
    let allApprovedIndicator; // Indicador de aprobación

    let previousData = null;

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
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);

            // Compara los datos actuales con los datos anteriores
            if (JSON.stringify(previousData) === JSON.stringify(data)) {
                console.log('No hay cambios en los datos. No se actualizan los gráficos.');
                return; // No actualiza si los datos no han cambiado
            }

            // Almacena los nuevos datos para futuras comparaciones
            previousData = data;

            // Destruir gráficos existentes si ya están creados
            if (documentsChart) documentsChart.destroy();
            if (accessLogsChart) accessLogsChart.destroy();
            if (evaluationChart) evaluationChart.destroy();
            if (processChart) processChart.destroy();
            if (educationChart) educationChart.destroy();
            if (documentTypeChart) documentTypeChart.destroy();
            if (experienceChart) experienceChart.destroy();
            if (documentsStatusChart) documentsStatusChart.destroy();

            // Progreso de Documentos
            if (data.documents_progress.length > 0 && data.documents_progress[0].total_documentos > 0) {
                const documentsChartCtx = document.getElementById('documentsChart').getContext('2d');
                documentsChart = new Chart(documentsChartCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Documentos Subidos', 'Faltantes'],
                        datasets: [{
                            data: [
                                data.documents_progress[0].documentos_subidos, 
                                data.documents_progress[0].total_documentos - data.documents_progress[0].documentos_subidos
                            ],
                            backgroundColor: ['#36A2EB', '#FF6384']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Progreso de Documentos'
                            }
                        }
                    }
                });
            }

            // Historial de Accesos
            if (data.access_logs && data.access_logs.length > 0) {
                const accessLogsChartCtx = document.getElementById('accessLogsChart').getContext('2d');
                const labels = data.access_logs.map(log => log.fecha);
                const accessData = data.access_logs.map(log => log.accesos);
                accessLogsChart = new Chart(accessLogsChartCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Accesos',
                            data: accessData,
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Historial de Accesos'
                            }
                        },
                        scales: {
                            y: { // Chart.js v3+
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Estado de Evaluación de Documentos
            if (data.evaluation.length > 0) {
                const evaluationChartCtx = document.getElementById('evaluationChart').getContext('2d');
                const evaluationData = [data.evaluation[0].revisados, data.evaluation[0].no_revisados];
                evaluationChart = new Chart(evaluationChartCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Revisados', 'No Revisados'],
                        datasets: [{
                            data: evaluationData,
                            backgroundColor: ['#36A2EB', '#FF6384']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Estado de Evaluación de Documentos'
                            }
                        }
                    }
                });
            }

            // Estado del Proceso de Selección
            if (data.selection_state.length > 0) {
                const processChartCtx = document.getElementById('processChart').getContext('2d');
                const processState = data.selection_state[0].state_name;
                processChart = new Chart(processChartCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Progreso'],
                        datasets: [{
                            label: processState,
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
                            y: { // Chart.js v3+
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
            } else {
                document.getElementById('processChart').parentElement.innerHTML = '<p class="text-muted">No disponible</p>';
            }

            // Progreso Educativo
            if (data.education.length > 0) {
                const educationChartCtx = document.getElementById('educationChart').getContext('2d');
                const educationLabels = data.education.map(item => item.education_type);
                const educationData = data.education.map(item => item.total);
                educationChart = new Chart(educationChartCtx, {
                    type: 'radar',
                    data: {
                        labels: educationLabels,
                        datasets: [{
                            label: 'Progreso Educativo',
                            data: educationData,
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
                            }
                        },
                        scales: {
                            r: { // Radar chart uses 'r' for radial scale
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Documentos Subidos por Tipo
            if (data.documents_by_type.length > 0) {
                const documentTypeChartCtx = document.getElementById('documentTypeChart').getContext('2d');
                const documentTypeLabels = data.documents_by_type.map(item => item.document_name);
                const documentTypeData = data.documents_by_type.map(item => item.total);
                documentTypeChart = new Chart(documentTypeChartCtx, {
                    type: 'pie',
                    data: {
                        labels: documentTypeLabels,
                        datasets: [{
                            data: documentTypeData,
                            backgroundColor: generateColorArray(documentTypeData.length)
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Documentos Subidos por Tipo'
                            }
                        }
                    }
                });
            }

            // Experiencia Laboral Total
            if (data.experience.length > 0 && data.experience[0].total_experiencia !== null) {
                const experienceChartCtx = document.getElementById('experienceChart').getContext('2d');
                const totalExperience = data.experience[0].total_experiencia || 0;
                experienceChart = new Chart(experienceChartCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Experiencia Total (Años)'],
                        datasets: [{
                            label: 'Años de Experiencia',
                            data: [totalExperience],
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Total de Años de Experiencia Laboral'
                            }
                        },
                        scales: {
                            y: { // Chart.js v3+
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            } else {
                document.getElementById('experienceChart').parentElement.innerHTML = '<p class="text-muted">No hay experiencia registrada.</p>';
            }

            // Nuevo: Estado de los Documentos
            if (data.documents_status.length > 0) {
                const documentsStatusChartCtx = document.getElementById('documentsStatusChart').getContext('2d');
                const statusLabels = data.documents_status.map(item => item.state_name);
                const statusData = data.documents_status.map(item => item.total);
                documentsStatusChart = new Chart(documentsStatusChartCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: generateColorArray(statusData.length)
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Estado de los Documentos'
                            }
                        }
                    }
                });
            }

            // Nuevo: Indicador de Todos los Documentos Aprobados
            if (data.all_documents_approved) {
                document.getElementById('allApprovedIndicator').innerHTML = '&#10004;'; // Símbolo de Check
                document.getElementById('allApprovedIndicator').style.color = 'green';
            } else {
                document.getElementById('allApprovedIndicator').innerHTML = '&#10060;'; // Símbolo de X
                document.getElementById('allApprovedIndicator').style.color = 'red';
            }
        })
        .catch(error => console.error('Error al cargar los datos del dashboard:', error));
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

    // Cargar los datos cada 5 segundos y solo actualizar si cambian
    loadDashboardData();
    setInterval(loadDashboardData, 5000);
});
