<?php
// modelos/DocumentApplicant.php

require_once "../config/Conexion.php";

class DocumentApplicant
{
    // Insertar documento en la base de datos
    public function insertar($applicant_id, $document_name, $original_file_name, $generated_file_name, $document_path, $user_observation = null)
    {
        $state_id = 1; // Estado 'Subido'
        $sql = "INSERT INTO documents_applicants 
                (applicant_id, document_name, original_file_name, generated_file_name, document_path, state_id, user_observation, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $params = [$applicant_id, $document_name, $original_file_name, $generated_file_name, $document_path, $state_id, $user_observation];
        return ejecutarConsulta($sql, $params);
    }

    // Listar todos los documentos de un postulante
    public function listar($applicant_id)
    {
        $sql = "SELECT 
                    d.id, 
                    d.applicant_id,
                    d.document_name,
                    d.original_file_name,
                    d.generated_file_name,
                    d.document_path,
                    d.created_at,
                    d.uploaded_at,
                    d.state_id,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.applicant_id = ?
                ORDER BY d.created_at DESC";
        $params = [$applicant_id];
        return ejecutarConsulta($sql, $params);
    }

    // Eliminar documento por ID
    public function eliminar($id)
    {
        $sql = "DELETE FROM documents_applicants WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar detalles de un documento por ID
    public function mostrar($id)
    {
        $sql = "SELECT 
                    d.id, 
                    d.applicant_id,
                    d.document_name,
                    d.original_file_name,
                    d.generated_file_name,
                    d.document_path,
                    d.created_at,
                    d.uploaded_at,
                    d.state_id,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Evaluar el documento: Aprobar, Rechazar, Solicitar Corrección
    public function evaluarDocumento($id, $admin_observation, $state_id)
    {
        // Asegurarse de que el campo 'reviewed_at' exista en la tabla 'documents_applicants'
        $sql = "UPDATE documents_applicants 
                SET admin_observation = ?, state_id = ?, admin_reviewed = 1, reviewed_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $params = [$admin_observation, $state_id, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Listar todos los documentos de los postulantes con detalles adicionales y opcionalmente filtrar por fechas
    public function listarTodos($start_date = '', $end_date = '')
    {
        $sql = "SELECT 
<<<<<<< HEAD
    d.id, 
    d.applicant_id,
    a.names AS applicant_name, 
    a.lastname, 
    a.username,
    a.email,
    ad.photo,  -- Columna de la foto del postulante
    d.document_name, 
    d.state_id,
    j.position_name AS job_name,
    c.company_name AS company_name
FROM documents_applicants d
JOIN applicants a ON d.applicant_id = a.id
JOIN jobs j ON a.job_id = j.id
JOIN companies c ON a.company_id = c.id
LEFT JOIN applicants_details ad ON a.id = ad.applicant_id";
     // Unir con la tabla applicants_details
=======
                    d.id, 
                    d.applicant_id,
                    a.names AS applicant_name, 
                    a.lastname, 
                    a.username,
                    a.email,
                    d.document_name, 
                    d.state_id,
                    j.position_name AS job_name,
                    c.company_name AS company_name
                FROM documents_applicants d
                JOIN applicants a ON d.applicant_id = a.id
                JOIN jobs j ON a.job_id = j.id
                JOIN companies c ON a.company_id = c.id";
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733

        $params = [];

        if (!empty($start_date) && !empty($end_date)) {
            $sql .= " WHERE d.created_at BETWEEN ? AND ?";
            $params[] = $start_date . " 00:00:00";
            $params[] = $end_date . " 23:59:59";
        }

        $sql .= " ORDER BY d.created_at DESC";

        $result = ejecutarConsulta($sql, $params);

        if ($result) {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return [];
        }
    }

    // Obtener documentos por usuario
    public function obtenerDocumentosPorUsuario($user_id, $start_date = '', $end_date = '')
    {
        $sql = "SELECT 
                    d.id AS document_id,
                    d.document_name,
                    d.original_file_name,
                    d.document_path,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    d.uploaded_at,
                    d.reviewed_at,
                    d.state_id,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.applicant_id = ?";

        $params = [$user_id];

        if (!empty($start_date) && !empty($end_date)) {
            $sql .= " AND d.created_at BETWEEN ? AND ?";
            $params[] = $start_date . ' 00:00:00';
            $params[] = $end_date . ' 23:59:59';
        }

        $sql .= " ORDER BY d.created_at DESC";

        $result = ejecutarConsulta($sql, $params);
        if ($result) {
            $documentos = [];
            while ($row = $result->fetch_assoc()) {
                $documentos[] = $row;
            }
            return $documentos;
        } else {
            return [];
        }
    }
}
