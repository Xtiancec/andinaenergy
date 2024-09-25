<?php
// controlador/DashboardUserController.php

session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'user' ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'user'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta

$user_id = $_SESSION['user_id']; // ID del usuario logueado
$response = [];

// 1. Total de Documentos Subidos
$sql = "SELECT COUNT(*) as total FROM documents WHERE user_id = ?";
$params = [$user_id];
$row = ejecutarConsultaSimpleFila($sql, $params);
$response['totalDocuments'] = $row['total'] ?? 0;

// 2. Documentos Aprobados
$sql = "SELECT COUNT(*) as total 
        FROM documents 
        WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Aprobado' LIMIT 1)";
$row = ejecutarConsultaSimpleFila($sql, $params);
$response['approvedDocuments'] = $row['total'] ?? 0;

// 3. Documentos Pendientes
$sql = "SELECT COUNT(*) as total 
        FROM documents 
        WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Subido' LIMIT 1)";
$row = ejecutarConsultaSimpleFila($sql, $params);
$response['pendingDocuments'] = $row['total'] ?? 0;

// 4. Documentos Rechazados
$sql = "SELECT COUNT(*) as total 
        FROM documents 
        WHERE user_id = ? AND state_id = (SELECT id FROM document_states WHERE state_name = 'Rechazado' LIMIT 1)";
$row = ejecutarConsultaSimpleFila($sql, $params);
$response['rejectedDocuments'] = $row['total'] ?? 0;

// 5. Documentos Subidos por Tipo (Obligatorio/Opcional)
$sql = "SELECT dn.documentName, COUNT(*) as total 
        FROM documents d 
        JOIN document_name dn ON d.category_id = dn.id 
        WHERE d.user_id = ? 
        GROUP BY dn.documentName";
$result = ejecutarConsulta($sql, $params);
$documentsByType = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documentsByType[] = [
            'documentName' => $row['documentName'],
            'total' => $row['total']
        ];
    }
}
$response['documentsByType'] = $documentsByType;

// 6. Documentos por Estado
$sql = "SELECT ds.state_name, COUNT(*) as total 
        FROM documents d 
        JOIN document_states ds ON d.state_id = ds.id 
        WHERE d.user_id = ? 
        GROUP BY ds.state_name";
$result = ejecutarConsulta($sql, $params);
$documentsByStatus = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documentsByStatus[] = [
            'state_name' => $row['state_name'],
            'total' => $row['total']
        ];
    }
}
$response['documentsByStatus'] = $documentsByStatus;

// 7. Progreso de Documentos Obligatorios
// Primero, obtener el total de documentos obligatorios que el usuario debe subir según su posición
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
$sql = "SELECT d.id, dn.documentName, d.document_type, ds.state_name, d.uploaded_at, d.admin_observation 
        FROM documents d 
        JOIN document_name dn ON d.category_id = dn.id 
        JOIN document_states ds ON d.state_id = ds.id 
        WHERE d.user_id = ? 
        ORDER BY d.uploaded_at DESC";
$result = ejecutarConsulta($sql, $params);
$documentsList = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documentsList[] = [
            'id' => $row['id'],
            'documentName' => $row['documentName'],
            'document_type' => $row['document_type'],
            'state_name' => $row['state_name'],
            'uploaded_at' => $row['uploaded_at'],
            'admin_observation' => $row['admin_observation']
        ];
    }
}
$response['documentsList'] = $documentsList;

// 9. Lista de Documentos Obligatorios para Verificar
$sql = "SELECT dn.documentName 
        FROM mandatory_documents md 
        JOIN document_name dn ON md.documentName_id = dn.id 
        WHERE md.position_id = (SELECT job_id FROM users WHERE id = ? LIMIT 1)";
$result = ejecutarConsulta($sql, [$user_id]);
$mandatoryDocuments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mandatoryDocuments[] = [
            'documentName' => $row['documentName']
        ];
    }
}
$response['mandatoryDocuments'] = $mandatoryDocuments;

// Enviar la respuesta en formato JSON
echo json_encode($response);
?>
