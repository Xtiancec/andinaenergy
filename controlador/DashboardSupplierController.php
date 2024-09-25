<?php
// controlador/DashboardSuppliersController.php

session_start();

// Verificar si el proveedor ha iniciado sesión y tiene el rol adecuado
if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'supplier' ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'proveedor'
) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta

// Obtener el ID del proveedor desde la sesión
if (!isset($_SESSION['supplier_id'])) {
    echo json_encode(['error' => 'ID de proveedor no definido en la sesión.']);
    exit();
}

$supplier_id = $_SESSION['supplier_id']; // ID del proveedor logueado
$response = [];

try {
    // 1. Obtener los IDs de los estados necesarios
    $estadoNombres = ['Aprobado', 'Subido', 'Rechazado'];
    $estadoIDs = [];

    // Consulta para obtener los IDs de los estados
    $sqlEstado = "SELECT id, state_name FROM document_states WHERE state_name IN (?, ?, ?)";
    $resultEstado = ejecutarConsulta($sqlEstado, $estadoNombres);
    
    if ($resultEstado && $resultEstado->num_rows > 0) {
        while ($estado = $resultEstado->fetch_assoc()) {
            $estadoIDs[$estado['state_name']] = $estado['id'];
        }
    }

    // Verificar que todos los estados existan
    foreach ($estadoNombres as $nombreEstado) {
        if (!isset($estadoIDs[$nombreEstado])) {
            $response['error'] = "Estado '$nombreEstado' no encontrado en document_states.";
            echo json_encode($response);
            exit();
        }
    }

    // 2. Total de Documentos Subidos
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE supplier_id = ?";
    $params = [$supplier_id];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['totalDocuments'] = $row['total'] ?? 0;

    // 3. Documentos Aprobados
    $sql = "SELECT COUNT(*) as total 
            FROM documentsupplier 
            WHERE supplier_id = ? AND state_id = ?";
    $params = [$supplier_id, $estadoIDs['Aprobado']];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['approvedDocuments'] = $row['total'] ?? 0;

    // 4. Documentos Pendientes
    $sql = "SELECT COUNT(*) as total 
            FROM documentsupplier 
            WHERE supplier_id = ? AND state_id = ?";
    $params = [$supplier_id, $estadoIDs['Subido']];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['pendingDocuments'] = $row['total'] ?? 0;

    // 5. Documentos Rechazados
    $sql = "SELECT COUNT(*) as total 
            FROM documentsupplier 
            WHERE supplier_id = ? AND state_id = ?";
    $params = [$supplier_id, $estadoIDs['Rechazado']];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $response['rejectedDocuments'] = $row['total'] ?? 0;

    // 6. Documentos Subidos por Tipo (Obligatorio/Opcional)
    // Dado que 'documentsupplier' no tiene 'document_type', necesitamos obtener esta información de otra fuente.
    // Suponiendo que todos los documentos de proveedores son 'obligatorio' o 'opcional' basado en 'mandatory_documents'

    // Primero, obtener la lista de documentos obligatorios para proveedores
    // Dado que 'mandatory_documents' está ligado a 'position_id' y 'suppliers' no tienen 'position_id', necesitamos ajustar esto
    // Para simplificar, asumiremos que todos los documentos en 'documentnamesupplier' son obligatorios para proveedores

    $sql = "SELECT dn.name AS documentName, COUNT(*) as total 
            FROM documentsupplier ds 
            JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id 
            WHERE ds.supplier_id = ? 
            GROUP BY dn.name";
    $result = ejecutarConsulta($sql, [$supplier_id]);
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

    // 7. Documentos por Estado
    $sql = "SELECT ds_state.state_name, COUNT(*) as total 
            FROM documentsupplier ds 
            JOIN document_states ds_state ON ds.state_id = ds_state.id 
            WHERE ds.supplier_id = ? 
            GROUP BY ds_state.state_name";
    $result = ejecutarConsulta($sql, [$supplier_id]);
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

    // 8. Progreso de Documentos Obligatorios
    // Dado que la tabla 'mandatory_documents' está vinculada a 'position_id' y los proveedores no tienen 'position_id',
    // es recomendable definir una lógica específica para proveedores. Por ahora, asumiremos que todos los proveedores
    // deben subir todos los documentos de 'documentnamesupplier' como obligatorios.

    // Obtener el total de documentos obligatorios (asumimos todos son obligatorios)
    $sql = "SELECT COUNT(*) as total FROM documentnamesupplier";
    $row = ejecutarConsultaSimpleFila($sql, []);
    $totalMandatory = $row['total'] ?? 0;

    // Obtener el número de documentos obligatorios aprobados
    $sql = "SELECT COUNT(*) as total 
            FROM documentsupplier ds 
            WHERE ds.supplier_id = ? AND ds.state_id = ?";
    $params = [$supplier_id, $estadoIDs['Aprobado']];
    $row = ejecutarConsultaSimpleFila($sql, $params);
    $approvedMandatory = $row['total'] ?? 0;

    // Calcular el progreso y asegurar que no exceda el 100%
    $progress = ($totalMandatory > 0) ? ($approvedMandatory / $totalMandatory) * 100 : 0;
    $progress = min($progress, 100); // Limitar a 100%
    $response['mandatoryProgress'] = round($progress, 2);
    $response['mandatoryStatus'] = ($approvedMandatory >= $totalMandatory) ? "Has aprobado todos los documentos obligatorios." : "Aún faltan documentos obligatorios por aprobar.";

    // 9. Lista de Documentos para la Tabla
    $sql = "SELECT ds.id, dn.name AS documentName, 
                   CASE 
                       WHEN ds.documentNameSupplier_id IN (SELECT documentName_id FROM mandatory_documents WHERE document_type = 'obligatorio') 
                           THEN 'Obligatorio' 
                       ELSE 'Opcional' 
                   END AS document_type, 
                   ds_state.state_name, ds.created_at AS uploaded_at, ds.admin_observation, ds.documentPath
            FROM documentsupplier ds 
            JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id 
            JOIN document_states ds_state ON ds.state_id = ds_state.id 
            WHERE ds.supplier_id = ? 
            ORDER BY ds.created_at DESC";
    $result = ejecutarConsulta($sql, [$supplier_id]);
    $documentsList = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $documentsList[] = [
                'id' => $row['id'],
                'documentName' => $row['documentName'],
                'document_type' => ucfirst($row['document_type']),
                'state_name' => $row['state_name'],
                'uploaded_at' => $row['uploaded_at'],
                'admin_observation' => $row['admin_observation'] ? $row['admin_observation'] : '-',
                'document_path' => $row['documentPath'] // Para acciones de descarga
            ];
        }
    }
    $response['documentsList'] = $documentsList;

    // 10. Lista de Documentos Obligatorios para Verificar
    $sql = "SELECT dn.name AS documentName 
            FROM documentnamesupplier dn
            WHERE dn.id IN (SELECT documentNameSupplier_id FROM documentsupplier WHERE supplier_id = ? AND state_id = ?)";
    $result = ejecutarConsulta($sql, [$supplier_id, $estadoIDs['Aprobado']]);
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

} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    exit();
}
?>
