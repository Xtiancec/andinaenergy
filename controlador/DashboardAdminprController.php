<?php
// controlador/DashboardAdminprController.php

session_start();

// Verificar si el usuario es adminpr (administrador de proveedores)
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    $_SESSION['user_role'] !== 'superadmin'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta

$response = [];

// 1. Total de Proveedores
$sql = "SELECT COUNT(*) as total FROM suppliers WHERE is_active = 1";
$row = ejecutarConsultaSimpleFila($sql);
$response['totalSuppliers'] = $row['total'] ?? 0;

// 2. Total de Usuarios Proveedores
// Nota: No existe una tabla 'supplier_users' en el volcado proporcionado.
// Asumiremos que 'users' con 'is_employee' = 0 son usuarios de proveedores.
// Sin embargo, en el volcado actual, todos los usuarios tienen 'is_employee' = 1.
// Por lo tanto, el resultado será 0 hasta que se creen usuarios proveedores.
$sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user' AND is_employee = 0";
$row = ejecutarConsultaSimpleFila($sql);
$response['totalSupplierUsers'] = $row['total'] ?? 0;

// 3. Documentos Pendientes de Evaluación
// Asumiremos que 'state_id' = 1 es 'Subido' (pendiente de evaluación)
$sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE state_id = 1";
$row = ejecutarConsultaSimpleFila($sql);
$response['pendingDocuments'] = $row['total'] ?? 0;

// 4. Documentos Evaluados
// 'state_id' = 2 es 'Aprobado', 'state_id' = 3 es 'Rechazado', etc.
$sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE state_id IN (2, 3, 4, 5)";
$row = ejecutarConsultaSimpleFila($sql);
$response['evaluatedDocuments'] = $row['total'] ?? 0;

// 5. Proveedores Registrados por Mes
$sql = "SELECT MONTH(created_at) as mes, COUNT(*) as total FROM suppliers WHERE is_active = 1 GROUP BY mes ORDER BY mes";
$suppliersPerMonth = ejecutarConsultaArray($sql);
$response['suppliersPerMonth'] = $suppliersPerMonth ? $suppliersPerMonth : [];

// 6. Actividad Reciente de Proveedores
// Asumiremos que existe una tabla 'supplier_activity_logs'
// Dado que no está en el volcado, usaremos 'supplier_access_logs' para simular actividad
$sql = "SELECT s.companyName as supplier_name, 'Acceso' as action, sal.access_time as activity_time 
        FROM supplier_access_logs sal 
        JOIN suppliers s ON sal.supplier_id = s.id 
        ORDER BY sal.access_time DESC 
        LIMIT 10";
$recentActivities = ejecutarConsultaArray($sql);
$response['recentActivities'] = array_map(function($item) {
    return [
        'supplier_name' => $item['supplier_name'],
        'action' => $item['action'],
        'activity_time' => $item['activity_time']
    ];
}, $recentActivities);

// 7. Documentos Evaluados por Estado
$sql = "SELECT ds.state_name as estado, COUNT(*) as total 
        FROM documentsupplier d 
        JOIN document_states ds ON d.state_id = ds.id 
        GROUP BY d.state_id";
$documentsByStatus = ejecutarConsultaArray($sql);
$response['documentsByStatus'] = $documentsByStatus ? $documentsByStatus : [];

echo json_encode($response);
?>
