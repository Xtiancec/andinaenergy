<?php
require_once '../config/Conexion.php';

class DocumentName
{
    // Inserta un nuevo documento
    public function insertar($documentName)
    {
        $sql = "INSERT INTO document_name (documentName, is_active) VALUES (?, '1')";
        $params = [$documentName];
        return ejecutarConsulta($sql, $params);
    }

    // Edita un documento existente
    public function editar($id, $documentName)
    {
        $sql = "UPDATE document_name SET documentName = ? WHERE id = ?";
        $params = [$documentName, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Desactiva un documento
    public function desactivar($id)
    {
        $sql = "UPDATE document_name SET is_active = '0' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Activa un documento
    public function activar($id)
    {
        $sql = "UPDATE document_name SET is_active = '1' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Muestra un documento específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM document_name WHERE id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Lista todos los documentos
    public function listar()
    {
        $sql = "SELECT * FROM document_name";
        return ejecutarConsulta($sql);
    }

    // Obtiene una lista de documentos activos
    public function select()
    {
        $sql = "SELECT * FROM document_name WHERE is_active = '1' ORDER BY documentName ASC";
        return ejecutarConsulta($sql);
    }
}
?>
