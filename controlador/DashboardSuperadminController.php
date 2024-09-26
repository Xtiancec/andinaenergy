<?php
// controlador/DashboardSuperadminController.php

session_start();

// Verificar si el usuario es superadministrador
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    $_SESSION['user_role'] !== 'superadmin'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta
require_once '../config/global.php'; // Si es necesario

$response = [];

try {
    // Total de Usuarios
    $sql = "SELECT COUNT(*) as total FROM users";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalUsuarios'] = $row['total'] ?? 0;

    // Total de Postulantes
    $sql = "SELECT COUNT(*) as total FROM applicants";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalPostulantes'] = $row['total'] ?? 0;

    // Total de Empresas
    $sql = "SELECT COUNT(*) as total FROM suppliers";
    $row = ejecutarConsultaSimpleFila($sql);
    $response['totalEmpresas'] = $row['total'] ?? 0;

    // Documentos Pendientes de Revisión para Usuarios
    $sql = "SELECT COUNT(*) as total FROM documents WHERE state_id = ?";
    $params = [1]; // Asumiendo que '1' es 'Subido' o 'Pendiente'
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['documentosPendientesUser'] = $row['total'] ?? 0;

    // Documentos Pendientes de Revisión para Postulantes
    $sql = "SELECT COUNT(*) as total FROM documents_applicants WHERE state_id = ?";
    $params = [1];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['documentosPendientesApplicant'] = $row['total'] ?? 0;

    // Documentos Pendientes de Revisión para Proveedores
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE state_id = ?";
    $params = [1];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['documentosPendientesSupplier'] = $row['total'] ?? 0;

    // Usuarios Registrados por Mes
    $sql = "SELECT MONTH(created_at) as mes, COUNT(*) as total FROM users GROUP BY mes ORDER BY mes";
    $usuariosPorMes = ejecutarConsultaArray($sql);
    $response['usuariosPorMes'] = $usuariosPorMes ? $usuariosPorMes : [];

    // Actividad Reciente
    $sql = "SELECT u.username, a.access_time FROM user_access_logs a JOIN users u ON a.user_id = u.id ORDER BY a.access_time DESC LIMIT 10";
    $actividadReciente = ejecutarConsultaArray($sql);
    $response['actividadReciente'] = array_map(function($item) {
        return [
            'username' => $item['username'],
            'action' => 'Inicio de sesión', // Puedes ajustar según tus datos
            'access_time' => $item['access_time']
        ];
    }, $actividadReciente);

    // Documentos por Estado
    $sql = "SELECT ds.state_name as estado, COUNT(*) as total FROM documents d JOIN document_states ds ON d.state_id = ds.id GROUP BY d.state_id";
    $documentosPorEstado = ejecutarConsultaArray($sql);
    $response['documentosPorEstado'] = $documentosPorEstado ? $documentosPorEstado : [];

    // Generar un hash de los datos para comparación en el frontend
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
