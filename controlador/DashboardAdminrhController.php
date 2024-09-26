<?php
// controlador/DashboardAdminhrController.php

session_start();

// Verificar si el usuario es adminrh (Administrador de Recursos Humanos)
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminrh']) // Permitir 'superadmin' o 'adminpr'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta

$response = [];

try {
    // 1. Total de Usuarios
    $sql = "SELECT COUNT(*) as total FROM users WHERE role IN ('user', 'adminpr') AND is_active = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalUsers'] = $row['total'] ?? 0;

    // 2. Total de Candidatos (Applicants)
    $sql = "SELECT COUNT(*) as total FROM applicants WHERE is_active = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalApplicants'] = $row['total'] ?? 0;

    // 3. Documentos Pendientes de Evaluación
    // Asumiremos que 'state_id' = 1 es 'Subido' (pendiente de evaluación)
    $sql = "SELECT COUNT(*) as total FROM documents WHERE state_id = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['pendingDocumentsUsers'] = $row['total'] ?? 0;

    $sql = "SELECT COUNT(*) as total FROM documents_applicants WHERE state_id = 1";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['pendingDocumentsApplicants'] = $row['total'] ?? 0;

    // 4. Documentos Evaluados
    // 'state_id' = 2 es 'Aprobado', 'state_id' = 3 es 'Rechazado', etc.
    $sql = "SELECT COUNT(*) as total FROM documents WHERE state_id IN (2, 3, 4, 5)";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['evaluatedDocumentsUsers'] = $row['total'] ?? 0;

    $sql = "SELECT COUNT(*) as total FROM documents_applicants WHERE state_id IN (2, 3, 4, 5)";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['evaluatedDocumentsApplicants'] = $row['total'] ?? 0;

    // 5. Usuarios Registrados por Mes
    $sql = "SELECT MONTH(created_at) as mes, COUNT(*) as total 
            FROM users 
            WHERE role IN ('user', 'adminpr') AND is_active = 1 
            GROUP BY mes 
            ORDER BY mes";
    $usersPerMonth = ejecutarConsultaArray($sql);
    $response['usersPerMonth'] = $usersPerMonth ? $usersPerMonth : [];

    // 6. Candidatos Registrados por Mes
    $sql = "SELECT MONTH(created_at) as mes, COUNT(*) as total 
            FROM applicants 
            WHERE is_active = 1 
            GROUP BY mes 
            ORDER BY mes";
    $applicantsPerMonth = ejecutarConsultaArray($sql);
    $response['applicantsPerMonth'] = $applicantsPerMonth ? $applicantsPerMonth : [];

    // 7. Actividad Reciente de Usuarios y Candidatos
    // Utilizando user_access_logs y applicant_access_logs

    // Obtener últimas 5 actividades de usuarios
    $sql = "SELECT u.username as name, 'Usuario' as type, 'Acceso' as action, ual.access_time as activity_time 
            FROM user_access_logs ual 
            JOIN users u ON ual.user_id = u.id 
            ORDER BY ual.access_time DESC 
            LIMIT 5";
    $recentUserActivities = ejecutarConsultaArray($sql);

    // Obtener últimas 5 actividades de candidatos
    $sql = "SELECT a.username as name, 'Candidato' as type, 'Acceso' as action, aal.access_time as activity_time 
            FROM applicant_access_logs aal 
            JOIN applicants a ON aal.applicant_id = a.id 
            ORDER BY aal.access_time DESC 
            LIMIT 5";
    $recentApplicantActivities = ejecutarConsultaArray($sql);

    // Combinar ambas actividades
    $response['recentActivities'] = array_merge($recentUserActivities, $recentApplicantActivities);

    // 8. Documentos Evaluados por Estado
    // Para usuarios
    $sql = "SELECT ds.state_name as estado, COUNT(*) as total 
            FROM documents d 
            JOIN document_states ds ON d.state_id = ds.id 
            GROUP BY d.state_id";
    $documentsByStatusUsers = ejecutarConsultaArray($sql);
    $response['documentsByStatusUsers'] = $documentsByStatusUsers ? $documentsByStatusUsers : [];

    // Para candidatos
    $sql = "SELECT ds.state_name as estado, COUNT(*) as total 
            FROM documents_applicants da 
            JOIN document_states ds ON da.state_id = ds.id 
            GROUP BY da.state_id";
    $documentsByStatusApplicants = ejecutarConsultaArray($sql);
    $response['documentsByStatusApplicants'] = $documentsByStatusApplicants ? $documentsByStatusApplicants : [];

    // 9. Usuarios Activos vs Inactivos
    $sql = "SELECT 
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as activeUsers,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactiveUsers
            FROM users 
            WHERE role IN ('user', 'adminpr')";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['usersStatus'] = [
        'activeUsers' => $row['activeUsers'] ?? 0,
        'inactiveUsers' => $row['inactiveUsers'] ?? 0
    ];

    // 10. Generar un hash de los datos para comparación en el frontend
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
