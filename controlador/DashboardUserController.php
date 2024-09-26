<?php
// controlador/DashboardUserController.php

session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    !in_array($_SESSION['user_role'], ['superadmin', 'adminpr','adminrh','user']) // Permitir 'superadmin' o 'adminpr'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta
require_once '../config/global.php'; // Si es necesario

$user_id = $_SESSION['user_id']; // ID del usuario logueado
$response = [];

try {
    // 1. Total de Documentos Subidos
    $sql = "SELECT COUNT(*) as total FROM documents WHERE user_id = ?";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $response['totalDocuments'] = $row['total'] ?? 0;

    // 2. Documentos Aprobados
    $sql = "SELECT COUNT(*) as total 
            FROM documents 
            WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Aprobado' LIMIT 1)";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $response['approvedDocuments'] = $row['total'] ?? 0;

    // 3. Documentos Pendientes
    $sql = "SELECT COUNT(*) as total 
            FROM documents 
            WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Subido' LIMIT 1)";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $response['pendingDocuments'] = $row['total'] ?? 0;

    // 4. Documentos Rechazados
    $sql = "SELECT COUNT(*) as total 
            FROM documents 
            WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Rechazado' LIMIT 1)";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $response['rejectedDocuments'] = $row['total'] ?? 0;

    // 5. Documentos Subidos por Tipo (Obligatorio/Opcional)
    $sql = "SELECT dn.documentName, COUNT(*) as total 
            FROM documents d 
            JOIN document_name dn ON d.category_id = dn.id 
            WHERE d.user_id = ? 
            GROUP BY dn.documentName";
    $documentsByType = ejecutarConsultaArray($sql, [$user_id]);
    $response['documentsByType'] = $documentsByType ? $documentsByType : [];

    // 6. Documentos por Estado
    $sql = "SELECT ds.state_name, COUNT(*) as total 
            FROM documents d 
            JOIN document_states ds ON d.state_id = ds.id 
            WHERE d.user_id = ? 
            GROUP BY ds.state_name";
    $documentsByStatus = ejecutarConsultaArray($sql, [$user_id]);
    $response['documentsByStatus'] = $documentsByStatus ? $documentsByStatus : [];

    // 7. Progreso de Documentos Obligatorios
    // Obtener el total de documentos obligatorios que el usuario debe subir según su posición
    $sql = "SELECT COUNT(*) as total FROM mandatory_documents WHERE position_id = (SELECT job_id FROM users WHERE id = ? LIMIT 1)";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $totalMandatory = $row['total'] ?? 0;

    // Obtener el número de documentos obligatorios aprobados
    $sql = "SELECT COUNT(*) as total 
            FROM documents d 
            JOIN document_name dn ON d.category_id = dn.id 
            JOIN mandatory_documents md ON dn.id = md.documentName_id 
            WHERE d.user_id = ? AND md.document_type = 'obligatorio' 
              AND d.state_id = (SELECT id FROM document_states WHERE state_name = 'Aprobado' LIMIT 1)";
    $row = ejecutarConsultaSimpleFila($sql, [$user_id]);
    $approvedMandatory = $row['total'] ?? 0;

    // Calcular el progreso y asegurar que no exceda el 100%
    $progress = ($totalMandatory > 0) ? ($approvedMandatory / $totalMandatory) * 100 : 0;
    $progress = min($progress, 100); // Limitar a 100%
    $response['mandatoryProgress'] = round($progress, 2);
    $response['mandatoryStatus'] = ($approvedMandatory >= $totalMandatory) ? "Has aprobado todos los documentos obligatorios." : "Aún faltan documentos obligatorios por aprobar.";

    // 8. Lista de Documentos para la Tabla
    $sql = "SELECT d.id, dn.documentName, d.document_type, ds.state_name, d.uploaded_at, d.admin_observation, d.document_path 
            FROM documents d 
            JOIN document_name dn ON d.category_id = dn.id 
            JOIN document_states ds ON d.state_id = ds.id 
            WHERE d.user_id = ? 
            ORDER BY d.uploaded_at DESC";
    $documentsList = ejecutarConsultaArray($sql, [$user_id]);
    $response['documentsList'] = $documentsList ? $documentsList : [];

    // 9. Lista de Documentos Obligatorios para Verificar
    $sql = "SELECT dn.documentName 
            FROM mandatory_documents md 
            JOIN document_name dn ON md.documentName_id = dn.id 
            WHERE md.position_id = (SELECT job_id FROM users WHERE id = ? LIMIT 1)";
    $mandatoryDocuments = ejecutarConsultaArray($sql, [$user_id]);
    $response['mandatoryDocuments'] = $mandatoryDocuments ? $mandatoryDocuments : [];

    // Generar un hash de los datos para comparación en el frontend
    $dataHash = md5(json_encode($response));
    $response['dataHash'] = $dataHash;

    // Enviar la respuesta en formato JSON
    echo json_encode($response);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    exit();
}

// Funciones auxiliares
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
