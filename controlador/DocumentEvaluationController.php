<?php
// controlador/DocumentEvaluationController.php

require "../config/Conexion.php";
require_once "../modelos/DocumentApplicant.php";
require_once "../modelos/Experience.php";

class DocumentEvaluationController
{
    // Constructor para iniciar la sesión
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Método para verificar si el usuario es superadministrador
    private function verificarSuperadmin()
    {
        if (
            !isset($_SESSION['user_type']) ||
            $_SESSION['user_type'] !== 'user' ||
            !isset($_SESSION['user_role']) ||
            $_SESSION['user_role'] !== 'superadmin'
        ) {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            exit();
        }
    }

    // Listar postulantes con porcentajes de documentos subidos y aprobados
    public function listarApplicants()
    {
        $this->verificarSuperadmin();
<<<<<<< HEAD
    
        // Obtener los filtros de fecha si existen
        $start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;
    
        $documentApplicant = new DocumentApplicant();
        $documents = $documentApplicant->listarTodos($start_date, $end_date);
    
=======

        // Obtener los filtros de fecha si existen
        $start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

        $documentApplicant = new DocumentApplicant();
        $documents = $documentApplicant->listarTodos($start_date, $end_date);

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (!empty($documents)) {
            // Agrupar documentos por postulante
            $applicants = [];
            foreach ($documents as $doc) {
                $applicantId = $doc['applicant_id'];
                if (!isset($applicants[$applicantId])) {
                    $applicants[$applicantId] = [
                        'company_name' => $doc['company_name'],
                        'job_name' => $doc['job_name'],
                        'id' => $applicantId,
                        'names' => $doc['applicant_name'],
                        'lastname' => $doc['lastname'],
                        'username' => $doc['username'] ?? 'N/A',
                        'email' => $doc['email'] ?? 'N/A',
<<<<<<< HEAD
                        'photo' => $doc['photo'] ?? null, // Agregando la foto aquí
=======
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
                        'total_required_cv' => 1, // Solo 1 CV
                        'total_uploaded_cv' => 0,
                        'total_approved_cv' => 0,
                        'total_required_other' => 10, // Máximo 10 Otros Documentos
                        'total_uploaded_other' => 0,
                        'total_approved_other' => 0
                    ];
                }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
                // Determinar si el documento es CV o Otro Documento
                if (stripos($doc['document_name'], 'cv') !== false) {
                    $applicants[$applicantId]['total_uploaded_cv'] += 1;
                    if ($doc['state_id'] == 2) { // Aprobado
                        $applicants[$applicantId]['total_approved_cv'] += 1;
                    }
                } else {
                    $applicants[$applicantId]['total_uploaded_other'] += 1;
                    if ($doc['state_id'] == 2) { // Aprobado
                        $applicants[$applicantId]['total_approved_other'] += 1;
                    }
                }
            }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
            // Calcular porcentajes
            $applicantsList = [];
            foreach ($applicants as $applicant) {
                // Porcentaje de CV subidos
                $porcentaje_subidos_cv = $applicant['total_required_cv'] > 0 ? round(min($applicant['total_uploaded_cv'] / $applicant['total_required_cv'], 1) * 100, 2) : 0;
<<<<<<< HEAD
    
                // Porcentaje de CV aprobados
                $porcentaje_aprobados_cv = $applicant['total_required_cv'] > 0 ? round(min($applicant['total_approved_cv'] / $applicant['total_required_cv'], 1) * 100, 2) : 0;
    
                // Porcentaje de Otros Documentos subidos (hasta máximo 10)
                $porcentaje_subidos_other = $applicant['total_required_other'] > 0 ? round(min($applicant['total_uploaded_other'] / $applicant['total_required_other'], 1) * 100, 2) : 0;
    
                // Porcentaje de Otros Documentos aprobados
                $porcentaje_aprobados_other = $applicant['total_required_other'] > 0 ? round(min($applicant['total_approved_other'] / $applicant['total_required_other'], 1) * 100, 2) : 0;
    
=======

                // Porcentaje de CV aprobados
                $porcentaje_aprobados_cv = $applicant['total_required_cv'] > 0 ? round(min($applicant['total_approved_cv'] / $applicant['total_required_cv'], 1) * 100, 2) : 0;

                // Porcentaje de Otros Documentos subidos (hasta máximo 10)
                $porcentaje_subidos_other = $applicant['total_required_other'] > 0 ? round(min($applicant['total_uploaded_other'] / $applicant['total_required_other'], 1) * 100, 2) : 0;

                // Porcentaje de Otros Documentos aprobados
                $porcentaje_aprobados_other = $applicant['total_required_other'] > 0 ? round(min($applicant['total_approved_other'] / $applicant['total_required_other'], 1) * 100, 2) : 0;

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
                $applicantsList[] = [
                    'company_name' => $applicant['company_name'],
                    'job_name' => $applicant['job_name'],
                    'id' => $applicant['id'],
                    'names' => $applicant['names'],
                    'lastname' => $applicant['lastname'],
                    'username' => $applicant['username'],
                    'email' => $applicant['email'],
<<<<<<< HEAD
                    'photo' => $applicant['photo'], // Incluir la foto aquí en la lista final
=======
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
                    'porcentaje_subidos_cv' => $porcentaje_subidos_cv,
                    'porcentaje_aprobados_cv' => $porcentaje_aprobados_cv,
                    'porcentaje_subidos_other' => $porcentaje_subidos_other,
                    'porcentaje_aprobados_other' => $porcentaje_aprobados_other
                ];
            }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
            echo json_encode(['success' => true, 'applicants' => $applicantsList]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron postulantes.']);
        }
    }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
    // Obtener documentos subidos por un postulante
    public function documentosApplicant()
    {
        $this->verificarSuperadmin();

        // Obtener los filtros de fecha si existen
        $applicant_id = isset($_POST['applicant_id']) ? intval($_POST['applicant_id']) : 0;
        $start_date = isset($_POST['start_date']) && !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? $_POST['end_date'] : null;

        $documentApplicant = new DocumentApplicant();
        $documents = $documentApplicant->obtenerDocumentosPorUsuario($applicant_id, $start_date, $end_date);

        if (!empty($documents)) {
            // Separar los documentos en CV y Otros
            $cv_documents = [];
            $other_documents = [];
            foreach ($documents as $doc) {
                if (stripos($doc['document_name'], 'cv') !== false) {
                    $cv_documents[] = $doc;
                } else {
                    $other_documents[] = $doc;
                }
            }

            echo json_encode([
                'success' => true,
                'cv_documents' => $cv_documents,
                'other_documents' => $other_documents
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron documentos para este postulante.']);
        }
    }

    // Cambiar estado de un documento
    public function cambiarEstadoDocumento()
    {
        $this->verificarSuperadmin();

        $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
        $estado_id = isset($_POST['estado_id']) ? intval($_POST['estado_id']) : 0;
        $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : NULL;

        // Validar que el estado_id sea válido
        $valid_estados = [2, 3, 4]; // 2: Aprobado, 3: Rechazado, 4: Por Corregir
        if (!in_array($estado_id, $valid_estados)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido.']);
            exit();
        }

        $documentApplicant = new DocumentApplicant();
        $document = $documentApplicant->mostrar($document_id);

        if (!$document) {
            echo json_encode(['success' => false, 'message' => 'Documento no encontrado.']);
            exit();
        }

        // Actualizar el estado del documento
        $result = $documentApplicant->evaluarDocumento($document_id, $observacion, $estado_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
    }

    // Obtener experiencia educativa de un postulante
    public function obtenerEducacion()
    {
        $this->verificarSuperadmin();

        $applicantId = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

        if ($applicantId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $experience = new Experience();
        $educaciones = $experience->mostrarEducacion($applicantId);

        echo json_encode([
            'success' => true,
            'educaciones' => $educaciones
        ]);
    }

    // Obtener experiencia laboral de un postulante
    public function obtenerTrabajo()
    {
        $this->verificarSuperadmin();

        $applicantId = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

        if ($applicantId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $experience = new Experience();
        $trabajos = $experience->mostrarTrabajo($applicantId);

        echo json_encode([
            'success' => true,
            'trabajos' => $trabajos
        ]);
    }
}

// Manejo de las acciones
if (isset($_GET['op'])) {
    $controller = new DocumentEvaluationController();
    switch ($_GET['op']) {
        case 'listarApplicants':
            $controller->listarApplicants();
            break;
        case 'documentosApplicant':
            $controller->documentosApplicant();
            break;
        case 'cambiarEstadoDocumento':
            $controller->cambiarEstadoDocumento();
            break;
        case 'obtenerEducacion':
            $controller->obtenerEducacion();
            break;
        case 'obtenerTrabajo':
            $controller->obtenerTrabajo();
            break;
        // Otros casos si los hay
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
