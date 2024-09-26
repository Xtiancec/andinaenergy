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

    // Variables para almacenar los gráficos
    let morrisBarChart;
    let morrisDonutChart;

    let previousDataHash = null; // Almacena el hash de los datos anteriores

    // Función para actualizar el dashboard
    function actualizarDashboard() {
        $.ajax({
            url: '../controlador/DashboardAdminprController.php',
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
                $('#total-suppliers').text(data.totalSuppliers);
                $('#total-supplier-users').text(data.totalSupplierUsers);
                $('#pending-documents').text(data.pendingDocuments);
                $('#evaluated-documents').text(data.evaluatedDocuments);

                // Actualizar gráfico de Proveedores Registrados por Mes (Morris.js)
                actualizarGraficoMorrisBar(data.suppliersPerMonth);

                // Actualizar gráfico de Proveedores por Área (eCharts)
                actualizarGraficoEchartsSuppliersArea(data.suppliersByArea);

                // Actualizar tabla de Actividad Reciente
                actualizarTablaActividad(data.recentActivities);

                // Actualizar gráfico de Documentos Evaluados por Estado (Morris.js Donut)
                actualizarGraficoMorrisDonut(data.documentsByStatus);
            },
            error: function (xhr, status, error) {
                console.error('Error al obtener datos:', error);
            }
        });
    }

    // Función para actualizar el gráfico de Proveedores Registrados por Mes (Morris.js)
    function actualizarGraficoMorrisBar(suppliersPerMonth) {
        var data = suppliersPerMonth.map(function (e) {
            return { month: 'Mes ' + e.mes, suppliers: e.total };
        });

        if (morrisBarChart) {
            // Actualizar datos
            morrisBarChart.setData(data);
        } else {
            // Crear el gráfico si no existe
            morrisBarChart = new Morris.Bar({
                element: 'morris-bar-chart',
                data: data,
                xkey: 'month',
                ykeys: ['suppliers'],
                labels: ['Proveedores Registrados'],
                gridLineColor: '#eef0f2',
                barColors: ['#1de9b6'],
                hideHover: 'auto',
                resize: true
            });
        }
    }

    // Función para actualizar el gráfico de Proveedores por Área (eCharts)
    function actualizarGraficoEchartsSuppliersArea(suppliersByArea) {
        var areas = suppliersByArea.map(function (e) { return e.area_name; });
        var counts = suppliersByArea.map(function (e) { return e.total; });

        if (echartsSuppliersArea) {
            // Actualizar datos
            echartsSuppliersArea.setOption({
                xAxis: {
                    data: areas
                },
                series: [{
                    name: 'Proveedores',
                    data: counts
                }]
            });
        } else {
            // Crear el gráfico si no existe
            var chartDom = document.getElementById('suppliers-area-chart');
            echartsSuppliersArea = echarts.init(chartDom);
            var option = {
                title: {
                    text: 'Proveedores por Área',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['Proveedores'],
                    top: 30
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: true,
                    data: areas
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Proveedores',
                    type: 'bar',
                    data: counts,
                    itemStyle: {
                        color: '#2d9cdb'
                    }
                }]
            };
            echartsSuppliersArea.setOption(option);
        }
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

    // Función para actualizar el gráfico de Documentos Evaluados por Estado (Morris.js Donut)
    function actualizarGraficoMorrisDonut(documentsByStatus) {
        var data = documentsByStatus.map(function (e) {
            return { label: e.estado, value: e.total };
        });

        if (morrisDonutChart) {
            // Actualizar datos
            morrisDonutChart.setData(data);
        } else {
            // Crear el gráfico si no existe
            morrisDonutChart = new Morris.Donut({
                element: 'morris-donut-chart',
                data: data,
                colors: ['#1de9b6', '#26a69a', '#66bb6a', '#b2dfdb', '#4db6ac'],
                resize: true,
                formatter: function (x, data) { return x }
            });
        }
    }

    // Llamar a la función para actualizar el dashboard al cargar la página
    actualizarDashboard();

    // Actualizar el dashboard cada cierto tiempo (ejemplo: cada 5 segundos)
    setInterval(actualizarDashboard, 5000); // 5000 ms = 5 segundos
});
