<?php
// DocumentUpload.php

require_once "../config/Conexion.php";

class DocumentUpload
{
    // Constructor
    public function __construct() {}

    // Listar documentos disponibles para el usuario
    public function listarDocumentosPorUsuario($user_id)
    {
        try {
            global $conexion;
            $user_id = limpiarCadena($user_id);

            // Obtener job_id del usuario
            $sqlJob = "SELECT job_id FROM users WHERE id = '$user_id'";
            $resultJob = ejecutarConsultaSimpleFila($sqlJob);
            $job_id = $resultJob ? $resultJob['job_id'] : null;

            if (!$job_id) {
                return ['success' => false, 'error' => 'Usuario no encontrado o sin job_id'];
            }

            // Obtener documentos asignados al puesto del usuario
            $sql = "SELECT md.id AS document_id, dn.documentName AS name, md.document_type, dn.id AS category_id
                    FROM mandatory_documents md
                    INNER JOIN document_name dn ON md.documentName_id = dn.id
                    WHERE md.position_id = '$job_id' AND md.is_active = 1
                    ORDER BY md.id ASC";

            $result = ejecutarConsulta($sql);

            if ($result) {
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[] = $row;
                }

                return ['success' => true, 'data' => $documents];
            } else {
                return ['success' => false, 'error' => $conexion->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Obtener estado de los documentos subidos por el usuario
    public function obtenerEstadoDocumentos($user_id)
    {
        try {
            global $conexion;
            $user_id = limpiarCadena($user_id);

            $sql = "SELECT category_id AS document_id, document_name, document_path, uploaded_at
                    FROM documents
                    WHERE user_id = '$user_id'";

            $result = ejecutarConsulta($sql);

            if ($result) {
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[$row['document_id']] = $row;
                }

                return ['success' => true, 'data' => $documents];
            } else {
                return ['success' => false, 'error' => $conexion->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Subir un documento
    public function subirDocumento($data)
    {
        try {
            global $conexion;

            $user_id = limpiarCadena($data['user_id']);
            $category_id = limpiarCadena($data['category_id']);
            $document_type = limpiarCadena($data['document_type']);
            $document_name = limpiarCadena($data['document_name']);
            $document_path = limpiarCadena($data['document_path']);
            $user_observation = limpiarCadena($data['user_observation']);
            $state_id = limpiarCadena($data['state_id']);

            // Verificar si ya existe un documento para este usuario y categoría
            $sqlCheck = "SELECT id, document_path FROM documents WHERE user_id = '$user_id' AND category_id = '$category_id'";
            $resultCheck = ejecutarConsultaSimpleFila($sqlCheck);

            if ($resultCheck) {
                // Ya existe, actualizar el registro
                $document_id = $resultCheck['id'];
                $oldDocumentPath = $resultCheck['document_path'];

                // Eliminar el archivo anterior
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }

                // Actualizar el registro
                $sqlUpdate = "UPDATE documents SET 
                                document_type = '$document_type',
                                document_name = '$document_name',
                                document_path = '$document_path',
                                user_observation = '$user_observation',
                                state_id = '$state_id',
                                uploaded_at = NOW()
                              WHERE id = '$document_id'";

                $resultUpdate = ejecutarConsulta($sqlUpdate);

                if ($resultUpdate) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => $conexion->error];
                }
            } else {
                // No existe, insertar nuevo registro
                $sqlInsert = "INSERT INTO documents (user_id, document_type, document_name, document_path, category_id, user_observation, state_id, uploaded_at)
                              VALUES ('$user_id', '$document_type', '$document_name', '$document_path', '$category_id', '$user_observation', '$state_id', NOW())";

                $resultInsert = ejecutarConsulta_retornarID($sqlInsert);

                if ($resultInsert) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => $conexion->error];
                }
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Eliminar un documento
    public function eliminarDocumento($user_id, $category_id)
    {
        try {
            global $conexion;
            $user_id = limpiarCadena($user_id);
            $category_id = limpiarCadena($category_id);

            // Obtener información del documento a eliminar
            $sqlGet = "SELECT id, document_path FROM documents WHERE user_id = '$user_id' AND category_id = '$category_id'";
            $resultGet = ejecutarConsultaSimpleFila($sqlGet);

            if ($resultGet) {
                $document_id = $resultGet['id'];
                $document_path = $resultGet['document_path'];

                // Eliminar el registro de la base de datos
                $sqlDelete = "DELETE FROM documents WHERE id = '$document_id'";
                $resultDelete = ejecutarConsulta($sqlDelete);

                if ($resultDelete) {
                    // Eliminar el archivo del sistema de archivos
                    if (file_exists($document_path)) {
                        unlink($document_path);
                    }
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => $conexion->error];
                }
            } else {
                return ['success' => false, 'error' => 'Documento no encontrado.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Obtener historial de documentos subidos por usuario y categoría
    public function obtenerHistorialDocumentos($user_id, $category_id)
    {
        try {
            global $conexion;
            $user_id = limpiarCadena($user_id);
            $category_id = limpiarCadena($category_id);

            $sql = "SELECT id, document_name, document_path, uploaded_at, state_id
                    FROM documents
                    WHERE user_id = '$user_id' AND category_id = '$category_id'
                    ORDER BY uploaded_at DESC";

            $result = ejecutarConsulta($sql);

            if ($result) {
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[] = $row;
                }

                return ['success' => true, 'data' => $documents];
            } else {
                return ['success' => false, 'error' => $conexion->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
