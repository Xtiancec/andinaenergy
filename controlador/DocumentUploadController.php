<?php
// DocumentUploadController.php

require_once "../modelos/DocumentUpload.php";
session_start();

class DocumentUploadController
{
    public function listarDocumentos()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $documentUpload = new DocumentUpload();
        $result = $documentUpload->listarDocumentosPorUsuario($user_id);

        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener los documentos: ' . $result['error']]);
        }
    }

    public function obtenerEstadoDocumentos()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $documentUpload = new DocumentUpload();
        $result = $documentUpload->obtenerEstadoDocumentos($user_id);

        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el estado de los documentos: ' . $result['error']]);
        }
    }

    public function subir()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $document_type = isset($_POST['document_type']) ? limpiarCadena($_POST['document_type']) : '';
        $state_id = 1; // Estado inicial
        $user_observation = isset($_POST['comment']) ? limpiarCadena($_POST['comment']) : '';

        if ($category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de documento inválido.']);
            return;
        }

        if (!isset($_FILES['documentFile']) || $_FILES['documentFile']['error'] != UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un archivo válido.']);
            return;
        }

        $file = $_FILES['documentFile'];
        $originalFileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmpPath = $file['tmp_name'];

        // Validar tamaño del archivo (por ejemplo, máximo 10MB)
        if ($fileSize > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 10MB).']);
            return;
        }

        // Validar tipo de archivo permitido
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido.']);
            return;
        }

        // Generar un nombre único para el archivo
        $newFileName = uniqid('doc_', true) . '.' . $fileExtension;

        // Definir la ruta donde se guardará el archivo
        $uploadDir = '../uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destinationPath = $uploadDir . $newFileName;

        // Mover el archivo a la ubicación final
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            // Preparar los datos para insertar en la base de datos
            $data = [
                'user_id' => $user_id,
                'category_id' => $category_id,
                'document_type' => $document_type,
                'document_name' => $originalFileName,
                'document_path' => $destinationPath,
                'user_observation' => $user_observation,
                'state_id' => $state_id,
            ];

            $documentUpload = new DocumentUpload();
            $result = $documentUpload->subirDocumento($data);

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Documento subido exitosamente.',
                    'documentPath' => $destinationPath,
                    'originalFileName' => $originalFileName
                ]);
            } else {
                // Eliminar el archivo si hubo un error al insertar en la base de datos
                if (file_exists($destinationPath)) {
                    unlink($destinationPath);
                }
                echo json_encode(['success' => false, 'message' => 'Error al guardar el documento: ' . $result['error']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo al directorio de destino.']);
        }
    }

    public function eliminar()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

        if ($category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de documento inválido.']);
            return;
        }

        $documentUpload = new DocumentUpload();
        $result = $documentUpload->eliminarDocumento($user_id, $category_id);

        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Documento eliminado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el documento: ' . $result['error']]);
        }
    }

    public function obtenerHistorialDocumentos()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

        if ($category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de categoría inválido.']);
            return;
        }

        $documentUpload = new DocumentUpload();
        $result = $documentUpload->obtenerHistorialDocumentos($user_id, $category_id);

        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el historial: ' . $result['error']]);
        }
    }
}

// Manejar las solicitudes
if (isset($_GET['op'])) {
    $controller = new DocumentUploadController();
    switch ($_GET['op']) {
        case 'listarDocumentos':
            $controller->listarDocumentos();
            break;
        case 'obtenerEstadoDocumentos':
            $controller->obtenerEstadoDocumentos();
            break;
        case 'subir':
            $controller->subir();
            break;
        case 'eliminar':
            $controller->eliminar();
            break;
        case 'obtenerHistorialDocumentos':
            $controller->obtenerHistorialDocumentos();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
