<?php
// controlador/DashboardAdminprController.php

session_start();

// Verificar si el usuario es 'user' y tiene el rol 'superadmin' o 'adminpr' (Administrador de Proveedores)
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminpr']) // Permitir 'superadmin' o 'adminpr'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta

$response = [];

try {
    // 1. Total de Proveedores Activos
    $sql = "SELECT COUNT(*) as total FROM suppliers WHERE is_active = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalSuppliers'] = $row['total'] ?? 0;

    // 2. Total de Usuarios Proveedores Activos
    $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'adminpr' AND is_active = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalSupplierUsers'] = $row['total'] ?? 0;

    // 3. Documentos Pendientes de Evaluación
    // Asumimos que 'state_id' = 1 es 'Subido' (pendiente de evaluación)
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE state_id = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['pendingDocuments'] = $row['total'] ?? 0;

    // 4. Documentos Evaluados
    // 'state_id' = 2 es 'Aprobado', 'state_id' = 3 es 'Rechazado', etc.
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE state_id IN (2, 3, 4, 5)";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['evaluatedDocuments'] = $row['total'] ?? 0;

    // 5. Proveedores Registrados por Mes
    $sql = "SELECT MONTH(created_at) as mes, COUNT(*) as total 
            FROM suppliers 
            WHERE is_active = 1 
            GROUP BY mes 
            ORDER BY mes";
    $suppliersPerMonth = ejecutarConsultaArray($sql);
    $response['suppliersPerMonth'] = $suppliersPerMonth ? $suppliersPerMonth : [];

    // 6. Actividad Reciente de Proveedores
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

    // 8. Proveedores por Área (Agrupados por Departamento)
    // Dado que no hay una relación directa con 'areas', agrupamos por 'department' en 'suppliers'
    $sql = "SELECT department as area, COUNT(*) as total 
            FROM suppliers 
            WHERE is_active = 1 
            GROUP BY department 
            ORDER BY total DESC";
    $suppliersByArea = ejecutarConsultaArray($sql);
    $response['suppliersByArea'] = $suppliersByArea ? $suppliersByArea : [];

    // 9. Generar un hash de los datos para comparación en el frontend
    $dataHash = md5(json_encode($response));
    $response['dataHash'] = $dataHash;

    echo json_encode($response);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    exit();
}

// Funciones auxiliares para ejecutar consultas
function ejecutarConsulta($sql, $params = []) {
    global $conexion;
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }

    if ($params) {
        // Determinar los tipos de parámetros dinámicamente
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function ejecutarConsultaSimpleFila($sql, $params = []) {
    $result = ejecutarConsulta($sql, $params);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function ejecutarConsultaArray($sql, $params = []) {
    $result = ejecutarConsulta($sql, $params);
    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
?>
