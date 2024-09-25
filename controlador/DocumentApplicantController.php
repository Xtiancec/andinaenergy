<?php
// controladores/DocumentApplicantController.php

require_once "../modelos/DocumentApplicant.php";
require "../config/Conexion.php";

session_start();

class DocumentApplicantController {

    // Subir CV
    public function subirCv() {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $applicant_id = $_SESSION['applicant_id'];
        $username = $_SESSION['username'];

        if (isset($_FILES['cv_file'])) {
            $errors = [];
            $success = 0;
            $user_observation = isset($_POST['cv_observation']) ? trim($_POST['cv_observation']) : null;

            foreach ($_FILES['cv_file']['name'] as $index => $original_file_name) {
                // Sanitizar el nombre del archivo
                $original_file_name = basename($original_file_name);
                $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

                // Validar extensión de archivo
                $allowed_extensions = ['pdf', 'doc', 'docx'];
                if (!in_array($file_extension, $allowed_extensions)) {
                    $errors[] = 'Tipo de archivo no permitido: ' . $original_file_name;
                    continue;
                }

                $timestamp = date('YmdHis');
                $generated_file_name = $username . "_CV_" . $timestamp . "_" . $index . "." . $file_extension;

                // Ruta de subida: "uploads/applicants/user_{applicant_id}/cv/"
                $upload_dir = "../uploads/applicants/user_" . $applicant_id . "/cv";
                if (!file_exists($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $errors[] = 'No se pudo crear el directorio de subida para: ' . $original_file_name;
                        continue;
                    }
                }

                $document_path = $upload_dir . "/" . $generated_file_name;

                // Mover el archivo subido
                if (move_uploaded_file($_FILES['cv_file']['tmp_name'][$index], $document_path)) {
                    $documentApplicant = new DocumentApplicant();
                    $insert = $documentApplicant->insertar($applicant_id, 'CV', $original_file_name, $generated_file_name, $document_path, $user_observation);
                    if ($insert) {
                        $success++;
                    } else {
                        $errors[] = 'Error al insertar en la base de datos: ' . $original_file_name;
                        // Opcional: Eliminar el archivo subido si falla la inserción
                        unlink($document_path);
                    }
                } else {
                    $errors[] = 'Error al subir el archivo: ' . $original_file_name;
                }
            }

            if ($success > 0) {
                $message = $success . ' CV(s) subido(s) correctamente.';
                if (!empty($errors)) {
                    $message .= ' Sin embargo, hubo errores: ' . implode(' | ', $errors);
                }
                echo json_encode(['status' => true, 'message' => $message]);
            } else {
                $message = 'No se pudo subir ningún CV. Errores: ' . implode(' | ', $errors);
                echo json_encode(['status' => false, 'message' => $message]);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'No se recibió ningún archivo.']);
        }
    }

    // Subir Otros Documentos
    public function subirOtrosDocumentos() {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $applicant_id = $_SESSION['applicant_id'];
        $username = $_SESSION['username'];

        if (isset($_FILES['other_files'])) {
            $errors = [];
            $success = 0;
            $user_observations = isset($_POST['other_observations']) ? $_POST['other_observations'] : [];

            foreach ($_FILES['other_files']['name'] as $index => $original_file_name) {
                // Sanitizar el nombre del archivo
                $original_file_name = basename($original_file_name);
                $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

                // Validar extensión de archivo
                $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                if (!in_array($file_extension, $allowed_extensions)) {
                    $errors[] = 'Tipo de archivo no permitido: ' . $original_file_name;
                    continue;
                }

                $timestamp = date('YmdHis');
                $generated_file_name = $username . "_Doc_" . $timestamp . "_" . $index . "." . $file_extension;

                // Ruta de subida: "uploads/applicants/user_{applicant_id}/otros/"
                $upload_dir = "../uploads/applicants/user_" . $applicant_id . "/otros";
                if (!file_exists($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $errors[] = 'No se pudo crear el directorio de subida para: ' . $original_file_name;
                        continue;
                    }
                }

                $document_path = $upload_dir . "/" . $generated_file_name;
                $user_observation = isset($user_observations[$index]) ? trim($user_observations[$index]) : null;

                // Mover el archivo subido
                if (move_uploaded_file($_FILES['other_files']['tmp_name'][$index], $document_path)) {
                    $documentApplicant = new DocumentApplicant();
                    $insert = $documentApplicant->insertar($applicant_id, 'Otro', $original_file_name, $generated_file_name, $document_path, $user_observation);
                    if ($insert) {
                        $success++;
                    } else {
                        $errors[] = 'Error al insertar en la base de datos: ' . $original_file_name;
                        // Opcional: Eliminar el archivo subido si falla la inserción
                        unlink($document_path);
                    }
                } else {
                    $errors[] = 'Error al subir el archivo: ' . $original_file_name;
                }
            }

            if ($success > 0) {
                $message = $success . ' documento(s) subido(s) correctamente.';
                if (!empty($errors)) {
                    $message .= ' Sin embargo, hubo errores: ' . implode(' | ', $errors);
                }
                echo json_encode(['status' => true, 'message' => $message]);
            } else {
                $message = 'No se pudo subir ningún documento. Errores: ' . implode(' | ', $errors);
                echo json_encode(['status' => false, 'message' => $message]);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'No se recibió ningún archivo.']);
        }
    }

    // Listar Documentos
    public function listarDocumentos() {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }
    
        $applicant_id = $_SESSION['applicant_id'];
        $documentApplicant = new DocumentApplicant();
        $result = $documentApplicant->listar($applicant_id);
    
        if ($result) {
            $documents = [];
            while ($row = $result->fetch_assoc()) {
                $documents[] = $row;
            }
            echo json_encode(['status' => true, 'documents' => $documents]);
        } else {
            echo json_encode(['status' => false, 'message' => 'No se encontraron documentos subidos.']);
        }
    }

    // Eliminar Documento
    public function eliminarDocumento() {
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => false, 'message' => 'ID de documento no proporcionado.']);
            return;
        }

        $id = intval($_POST['id']);
        $documentApplicant = new DocumentApplicant();
        $document = $documentApplicant->mostrar($id);

        if ($document) {
            // Verificar que el documento pertenezca al usuario actual
            if ($document['applicant_id'] !== $_SESSION['applicant_id']) {
                echo json_encode(['status' => false, 'message' => 'No tienes permiso para eliminar este documento.']);
                return;
            }

            // Eliminar el archivo del servidor
            if (file_exists($document['document_path'])) {
                if (!unlink($document['document_path'])) {
                    echo json_encode(['status' => false, 'message' => 'Error al eliminar el archivo del servidor.']);
                    return;
                }
            }

            // Eliminar el registro de la base de datos
            $result = $documentApplicant->eliminar($id);
            if ($result) {
                echo json_encode(['status' => true, 'message' => 'Documento eliminado correctamente.']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al eliminar el documento de la base de datos.']);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'Documento no encontrado.']);
        }
    }
}

// Manejo de las acciones
if (isset($_GET["op"])) {
    $controller = new DocumentApplicantController();
    switch ($_GET["op"]) {
        case 'subirCv':
            $controller->subirCv();
            break;
        case 'subirOtrosDocumentos':
            $controller->subirOtrosDocumentos();
            break;
        case 'listarDocumentos':
            $controller->listarDocumentos();
            break;
        case 'eliminarDocumento':
            $controller->eliminarDocumento();
            break;
        default:
            echo json_encode(['status' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
