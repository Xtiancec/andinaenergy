<?php
// modelos/DocumentMandatory.php
require_once '../config/Conexion.php';

class DocumentMandatory
{
    // Insertar un nuevo registro en mandatory_documents
    public function insertar($position_id, $document_type, $documentName_id)
    {
        $sql = "INSERT INTO mandatory_documents (position_id, document_type, documentName_id, is_active) 
                VALUES (?, ?, ?, '1')";
        $params = [$position_id, $document_type, $documentName_id];
        return ejecutarConsulta($sql, $params);
    }

    // Editar un registro existente en mandatory_documents
    public function editar($id, $position_id, $document_type, $documentName_id)
    {
        $sql = "UPDATE mandatory_documents 
                SET position_id = ?, document_type = ?, documentName_id = ?
                WHERE id = ?";
        $params = [$position_id, $document_type, $documentName_id, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Desactivar un registro (cambiar is_active a 0)
    public function desactivar($id)
    {
        $sql = "UPDATE mandatory_documents SET is_active = '0' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Activar un registro (cambiar is_active a 1)
    public function activar($id)
    {
        $sql = "UPDATE mandatory_documents SET is_active = '1' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar un registro específico por su id
    public function mostrar($id)
    {
        $sql = "SELECT * FROM mandatory_documents WHERE id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Listar todos los documentos junto con sus puestos y nombres
    public function listar()
    {
        $sql = "SELECT 
                    mandatory_documents.id,
                    mandatory_documents.document_type,
                    mandatory_documents.created_at,
                    mandatory_documents.updated_at,
                    mandatory_documents.is_active,
                    jobs.position_name,
                    document_name.documentName
                FROM mandatory_documents
                INNER JOIN jobs ON jobs.id = mandatory_documents.position_id
                INNER JOIN document_name ON document_name.id = mandatory_documents.documentName_id
                INNER JOIN areas ON jobs.area_id = area.id
                INNER JOIN companies ON companies.id = area.company_id
                ORDER BY companies.company_name ASC, jobs.position_name ASC, mandatory_documents.document_type ASC";
        return ejecutarConsulta($sql);
    }

    // Listar todos los documentos activos
    public function listarDocumentosActivos()
    {
        $sql = "SELECT * FROM document_name WHERE is_active = '1' ORDER BY documentName ASC";
        return ejecutarConsulta($sql);
    }

    // Listar documentos asignados a un puesto específico
    public function listarDocumentosAsignados($position_id)
    {
        $sql = "SELECT 
                    document_name.id AS document_id,
                    document_name.documentName,
                    mandatory_documents.document_type,
                    IF(mandatory_documents.position_id IS NOT NULL, 1, 0) AS asignado
                FROM document_name
                LEFT JOIN mandatory_documents 
                    ON mandatory_documents.documentName_id = document_name.id 
                    AND mandatory_documents.position_id = ? 
                    AND mandatory_documents.is_active = '1'
                WHERE document_name.is_active = '1'
                ORDER BY document_name.documentName ASC";
        $params = [$position_id];
        return ejecutarConsulta($sql, $params);
    }

    // Listar todos los puestos junto con los documentos asignados
    public function listarPuestosConDocumentos()
    {
        $sql = "SELECT 
                    jobs.position_name,
                    document_name.documentName,
                    mandatory_documents.document_type,
                    mandatory_documents.created_at,
                    mandatory_documents.updated_at
                FROM mandatory_documents
                INNER JOIN jobs ON jobs.id = mandatory_documents.position_id
                INNER JOIN document_name ON document_name.id = mandatory_documents.documentName_id
                INNER JOIN areas ON jobs.area_id = area.id
                INNER JOIN companies ON companies.id = area.company_id
                WHERE mandatory_documents.is_active = '1'
                ORDER BY jobs.position_name ASC, mandatory_documents.document_type ASC";
        return ejecutarConsulta($sql);
    }

    // Verificar si ya existe una asignación de documento para un puesto específico
    public function verificarExistencia($position_id, $documentName_id)
    {
        $sql = "SELECT id FROM mandatory_documents 
                WHERE position_id = ? AND documentName_id = ? AND is_active = '1'";
        $params = [$position_id, $documentName_id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Listar todos los puestos activos
    public function listarPuestosActivos()
    {
        $sql = "SELECT id, position_name 
                FROM jobs 
                WHERE is_active = '1' 
                ORDER BY position_name ASC";
        return ejecutarConsulta($sql);
    }

    // Seleccionar puestos por empresa
    public function selectByCompany($company_id)
    {
        $sql = "SELECT jobs.id, jobs.position_name
                FROM jobs
                INNER JOIN areas ON jobs.area_id = area.id
                WHERE areas.company_id = ? AND jobs.is_active = '1'
                ORDER BY jobs.position_name ASC";
        $params = [$company_id];
        return ejecutarConsulta($sql, $params);
    }

    // Listar puestos con documentos por empresa
    public function listarPuestosConDocumentosPorEmpresa()
    {
        $sql = "SELECT 
                    companies.company_name,
                    jobs.position_name,
                    document_name.documentName,
                    mandatory_documents.document_type,
                    mandatory_documents.created_at,
                    mandatory_documents.updated_at
                FROM mandatory_documents
                INNER JOIN jobs ON jobs.id = mandatory_documents.position_id
                INNER JOIN document_name ON document_name.id = mandatory_documents.documentName_id
                INNER JOIN areas ON jobs.area_id = area.id
                INNER JOIN companies ON companies.id = area.company_id
                WHERE mandatory_documents.is_active = '1'
                ORDER BY companies.company_name ASC, jobs.position_name ASC, mandatory_documents.document_type ASC";
        return ejecutarConsulta($sql);
    }

    // Listar puestos con documentos por empresa (completo)
    public function listarPuestosConDocumentosPorEmpresaCompleto()
    {
        $sql = "SELECT 
                    companies.company_name,
                    jobs.position_name,
                    document_name.documentName,
                    mandatory_documents.document_type,
                    mandatory_documents.created_at,
                    mandatory_documents.updated_at,
                    mandatory_documents.id AS doc_asignado
                FROM jobs
                LEFT JOIN mandatory_documents 
                    ON jobs.id = mandatory_documents.position_id 
                    AND mandatory_documents.is_active = '1'
                LEFT JOIN document_name 
                    ON mandatory_documents.documentName_id = document_name.id
                INNER JOIN areas 
                    ON jobs.area_id = area.id
                INNER JOIN companies 
                    ON companies.id = area.company_id
                WHERE jobs.is_active = '1'
                ORDER BY companies.company_name ASC, jobs.position_name ASC, mandatory_documents.document_type ASC";
        return ejecutarConsulta($sql);
    }
}
?>
