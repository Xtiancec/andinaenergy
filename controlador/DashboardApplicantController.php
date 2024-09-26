<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/Conexion.php';  // Incluir el archivo de conexión

class DashboardController
{

    public function getDocumentsProgress($applicant_id)
    {
        $sql = "SELECT 
                    COUNT(CASE WHEN admin_reviewed = 1 THEN 1 END) AS documentos_subidos, 
                    COUNT(*) AS total_documentos 
                FROM documents_applicants 
                WHERE applicant_id = ?";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    public function getAccessLogs($applicant_id)
    {
        $sql = "SELECT DATE(access_time) AS fecha, COUNT(*) AS accesos 
                FROM applicant_access_logs 
                WHERE applicant_id = ? 
                GROUP BY DATE(access_time)";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    public function getEvaluationStatus($applicant_id)
    {
        $sql = "SELECT 
                    COUNT(CASE WHEN admin_reviewed = 1 THEN 1 END) AS revisados, 
                    COUNT(CASE WHEN admin_reviewed = 0 THEN 1 END) AS no_revisados 
                FROM documents_applicants 
                WHERE applicant_id = ?";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    public function getSelectionProcessState($applicant_id)
    {
        $sql = "SELECT state_name 
                FROM document_states 
                JOIN document_history ON document_states.id = document_history.state_id 
                WHERE document_id IN (
                    SELECT id FROM documents_applicants WHERE applicant_id = ?
                )
                ORDER BY document_history.changed_at DESC LIMIT 1";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    public function getEducationProgress($applicant_id)
    {
        $sql = "SELECT education_type, COUNT(*) AS total 
                FROM education_experience 
                WHERE applicant_id = ? 
                GROUP BY education_type";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    public function getDocumentsByType($applicant_id)
    {
        $sql = "SELECT document_name, COUNT(*) AS total 
                FROM documents_applicants 
                WHERE applicant_id = ? 
                GROUP BY document_name";  // Cambiado de document_type a document_name

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }


    public function getTotalExperience($applicant_id)
    {
        $sql = "SELECT SUM(TIMESTAMPDIFF(YEAR, start_date, end_date)) AS total_experiencia 
                FROM work_experience 
                WHERE applicant_id = ?";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }


    // Método corregido para obtener el conteo de documentos por estado
    public function getDocumentsStatus($applicant_id)
    {
        $sql = "SELECT ds.state_name, COUNT(*) AS total 
            FROM documents_applicants da
            JOIN document_states ds ON da.state_id = ds.id
            WHERE da.applicant_id = ?
            GROUP BY ds.state_name";

        return $this->ejecutarConsultaArray($sql, [$applicant_id]);
    }

    // Método para verificar si todos los documentos han sido aprobados
    public function allDocumentsApproved($applicant_id)
    {
        $sql = "SELECT COUNT(*) AS total_no_aprobados 
            FROM documents_applicants 
            WHERE applicant_id = ? AND admin_reviewed != 1";

        $result = $this->ejecutarConsultaArray($sql, [$applicant_id]);
        return isset($result[0]['total_no_aprobados']) && $result[0]['total_no_aprobados'] == 0;
    }
    private function ejecutarConsultaArray($sql, $params)
    {
        global $conexion;
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            die("Error preparando la consulta: " . $conexion->error);
        }

        if ($params) {
            $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    public function serveDashboardData($applicant_id) {
        header('Content-Type: application/json');

        $documentsProgress = $this->getDocumentsProgress($applicant_id);
        $accessLogs = $this->getAccessLogs($applicant_id);
        $evaluationStatus = $this->getEvaluationStatus($applicant_id);
        $selectionState = $this->getSelectionProcessState($applicant_id);
        $educationProgress = $this->getEducationProgress($applicant_id);
        $documentsByType = $this->getDocumentsByType($applicant_id);
        $totalExperience = $this->getTotalExperience($applicant_id);
        $documentsStatus = $this->getDocumentsStatus($applicant_id);
        $allApproved = $this->allDocumentsApproved($applicant_id);

        echo json_encode([
            'documents_progress' => $documentsProgress,
            'access_logs' => $accessLogs,
            'evaluation' => $evaluationStatus,
            'selection_state' => $selectionState,
            'education' => $educationProgress,
            'documents_by_type' => $documentsByType,
            'experience' => $totalExperience,
            'documents_status' => $documentsStatus,
            'all_documents_approved' => $allApproved
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['applicant_id'])) {
    $applicant_id = intval($_GET['applicant_id']);
    $dashboardController = new DashboardController();
    $dashboardController->serveDashboardData($applicant_id);
}
