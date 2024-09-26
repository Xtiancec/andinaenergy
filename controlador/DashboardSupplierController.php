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

    // Preparar la consulta con placeholders
    $placeholders = implode(',', array_fill(0, count($estadoNombres), '?'));
    $sqlEstado = "SELECT id, state_name FROM document_states WHERE state_name IN ($placeholders)";
    $stmtEstado = $conexion->prepare($sqlEstado);
    if ($stmtEstado === false) {
        throw new Exception("Error preparando la consulta de estados: " . $conexion->error);
    }

    // Vincular parámetros dinámicamente
    $types = str_repeat('s', count($estadoNombres));
    $stmtEstado->bind_param($types, ...$estadoNombres);
    $stmtEstado->execute();
    $resultEstado = $stmtEstado->get_result();

    if ($resultEstado && $resultEstado->num_rows > 0) {
        while ($estado = $resultEstado->fetch_assoc()) {
            $estadoIDs[$estado['state_name']] = $estado['id'];
        }
    }

    $stmtEstado->close();

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
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de total documentos: " . $conexion->error);
    }
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $response['totalDocuments'] = $row['total'] ?? 0;
    $stmt->close();

    // 3. Documentos Aprobados
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE supplier_id = ? AND state_id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos aprobados: " . $conexion->error);
    }
    $approved_state_id = $estadoIDs['Aprobado'];
    $stmt->bind_param('ii', $supplier_id, $approved_state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $response['approvedDocuments'] = $row['total'] ?? 0;
    $stmt->close();

    // 4. Documentos Pendientes
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE supplier_id = ? AND state_id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos pendientes: " . $conexion->error);
    }
    $pending_state_id = $estadoIDs['Subido'];
    $stmt->bind_param('ii', $supplier_id, $pending_state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $response['pendingDocuments'] = $row['total'] ?? 0;
    $stmt->close();

    // 5. Documentos Rechazados
    $sql = "SELECT COUNT(*) as total FROM documentsupplier WHERE supplier_id = ? AND state_id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos rechazados: " . $conexion->error);
    }
    $rejected_state_id = $estadoIDs['Rechazado'];
    $stmt->bind_param('ii', $supplier_id, $rejected_state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $response['rejectedDocuments'] = $row['total'] ?? 0;
    $stmt->close();

    // 6. Documentos Subidos por Tipo (Obligatorio/Opcional)
    // Aclaración: Según la estructura de la base de datos, 'documentsupplier' no tiene directamente 'document_type'.
    // Suponemos que 'documentnamesupplier' tiene información relevante sobre el tipo de documento.
    // Alternativamente, si existe una relación con 'mandatory_documents', se puede determinar el tipo.

    $sql = "SELECT dn.name AS documentName, COUNT(*) as total 
            FROM documentsupplier ds 
            JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id 
            WHERE ds.supplier_id = ? 
            GROUP BY dn.name";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos por tipo: " . $conexion->error);
    }
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
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
    $stmt->close();

    // 7. Documentos por Estado
    $sql = "SELECT ds_state.state_name, COUNT(*) as total 
            FROM documentsupplier ds 
            JOIN document_states ds_state ON ds.state_id = ds_state.id 
            WHERE ds.supplier_id = ? 
            GROUP BY ds_state.state_name";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos por estado: " . $conexion->error);
    }
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
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
    $stmt->close();

    // 8. Progreso de Documentos Obligatorios
    // Supongamos que todos los documentos en 'documentnamesupplier' son obligatorios para proveedores
    // Alternativamente, si hay una manera de determinar si un documento es obligatorio, se debe ajustar aquí.

    // Obtener el total de documentos obligatorios
    $sql = "SELECT COUNT(*) as total FROM documentnamesupplier WHERE description IS NOT NULL"; // Ajusta la condición según tu lógica
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de total documentos obligatorios: " . $conexion->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalMandatory = $row['total'] ?? 0;
    $stmt->close();

    // Obtener el número de documentos obligatorios aprobados
    $sql = "SELECT COUNT(*) as total FROM documentsupplier ds 
            JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id 
            WHERE ds.supplier_id = ? AND ds.state_id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de documentos obligatorios aprobados: " . $conexion->error);
    }
    $stmt->bind_param('ii', $supplier_id, $approved_state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $approvedMandatory = $row['total'] ?? 0;
    $stmt->close();

    // Calcular el progreso y asegurar que no exceda el 100%
    $progress = ($totalMandatory > 0) ? ($approvedMandatory / $totalMandatory) * 100 : 0;
    $progress = min($progress, 100); // Limitar a 100%
    $response['mandatoryProgress'] = round($progress, 2);
    $response['mandatoryStatus'] = ($approvedMandatory >= $totalMandatory) ? "Has aprobado todos los documentos obligatorios." : "Aún faltan documentos obligatorios por aprobar.";

    // 9. Lista de Documentos para la Tabla
    $sql = "SELECT ds.id, dn.name AS documentName, 
                   CASE 
                       WHEN ds.documentNameSupplier_id IN (SELECT documentNameSupplier_id FROM documentnamesupplier WHERE description IS NOT NULL) 
                           THEN 'Obligatorio' 
                       ELSE 'Opcional' 
                   END AS document_type, 
                   ds_state.state_name, ds.created_at AS uploaded_at, ds.admin_observation, ds.documentPath
            FROM documentsupplier ds 
            JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id 
            JOIN document_states ds_state ON ds.state_id = ds_state.id 
            WHERE ds.supplier_id = ? 
            ORDER BY ds.created_at DESC";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparando la consulta de lista de documentos: " . $conexion->error);
    }
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
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
    $stmt->close();

    // Enviar la respuesta en formato JSON
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
?>
