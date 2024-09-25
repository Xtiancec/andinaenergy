<?php
// controlador/DocumentMandatoryController.php
require_once "../modelos/DocumentMandatory.php";

// Instanciar el modelo
$documentMandatory = new DocumentMandatory();

// Función para obtener parámetros POST de manera segura
function getPostParam($key, $default = "")
{
    return isset($_POST[$key]) ? limpiarCadena($_POST[$key]) : $default;
}

// Función auxiliar para responder con JSON
function respond($success, $message, $data = null)
{
    header('Content-Type: application/json; charset=utf-8');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
}

switch ($_GET["op"]) {
    case 'guardar':
        $position_id = getPostParam("position_id");
        $document_type = getPostParam("document_type");
        $documentName_id = getPostParam("documentName_id");
        $id = getPostParam("id");

        if (empty($position_id) || empty($document_type) || empty($documentName_id)) {
            respond(false, "Todos los campos son requeridos.");
            exit();
        }

        // Verificar si la asignación ya existe
        $existente = $documentMandatory->verificarExistencia($position_id, $documentName_id);
        if ($existente) {
            respond(false, "Esta asignación ya existe.");
            exit();
        }

        $rspta = $documentMandatory->insertar($position_id, $document_type, $documentName_id);
        if ($rspta) {
            respond(true, "Datos registrados correctamente.");
        } else {
            respond(false, "No se pudo registrar los datos.");
        }
        break;

    case 'editar':
        $position_id = getPostParam("position_id");
        $document_type = getPostParam("document_type");
        $documentName_id = getPostParam("documentName_id");
        $id = getPostParam("id");

        if (empty($id) || empty($position_id) || empty($document_type) || empty($documentName_id)) {
            respond(false, "Todos los campos son requeridos.");
            exit();
        }

        $rspta = $documentMandatory->editar($id, $position_id, $document_type, $documentName_id);
        if ($rspta) {
            respond(true, "Datos actualizados correctamente.");
        } else {
            respond(false, "No se pudo actualizar los datos.");
        }
        break;

    case 'desactivar':
        $id = getPostParam("id");

        if (empty($id)) {
            respond(false, "El ID es requerido para desactivar.");
            exit();
        }

        $rspta = $documentMandatory->desactivar($id);
        if ($rspta) {
            respond(true, "Datos desactivados correctamente.");
        } else {
            respond(false, "No se pudo desactivar los datos.");
        }
        break;

    case 'activar':
        $id = getPostParam("id");

        if (empty($id)) {
            respond(false, "El ID es requerido para activar.");
            exit();
        }

        $rspta = $documentMandatory->activar($id);
        if ($rspta) {
            respond(true, "Datos activados correctamente.");
        } else {
            respond(false, "No se pudo activar los datos.");
        }
        break;

    case 'mostrar':
        $id = getPostParam("id");

        if (empty($id)) {
            respond(false, "El ID es requerido para mostrar.");
            exit();
        }

        $rspta = $documentMandatory->mostrar($id);
        if ($rspta) {
            respond(true, "Datos obtenidos correctamente.", $rspta);
        } else {
            respond(false, "No se encontraron datos para el ID proporcionado.");
        }
        break;

    case 'listarDocumentosActivos':
        $rspta = $documentMandatory->listarDocumentosActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }
        respond(true, "Lista de documentos activos obtenida correctamente.", $data);
        break;

    case 'guardarAsignacion':
        $position_id = getPostParam('position_id');
        $documentosSeleccionados = isset($_POST['documentosSeleccionados']) ? json_decode($_POST['documentosSeleccionados'], true) : [];
        $documentosDesmarcados = isset($_POST['documentosDesmarcados']) ? json_decode($_POST['documentosDesmarcados'], true) : [];

        if (empty($position_id)) {
            respond(false, "El ID del puesto es requerido.");
            exit();
        }

        // Iniciar transacción
        global $conexion;
        $conexion->begin_transaction();

        try {
            // Procesar documentos seleccionados
            foreach ($documentosSeleccionados as $doc) {
                $docId = isset($doc['documentName_id']) ? limpiarCadena($doc['documentName_id']) : null;
                $docType = isset($doc['document_type']) ? limpiarCadena($doc['document_type']) : null;

                if ($docId && $docType) {
                    $existente = $documentMandatory->verificarExistencia($position_id, $docId);
                    if ($existente) {
                        // Actualizar tipo de documento si existe
                        $documentMandatory->editar($existente['id'], $position_id, $docType, $docId);
                    } else {
                        // Insertar nueva asignación
                        $documentMandatory->insertar($position_id, $docType, $docId);
                    }
                }
            }

            // Procesar documentos desmarcados
            foreach ($documentosDesmarcados as $doc) {
                $docId = isset($doc['documentName_id']) ? limpiarCadena($doc['documentName_id']) : null;
                if ($docId) {
                    $existente = $documentMandatory->verificarExistencia($position_id, $docId);
                    if ($existente) {
                        // Desactivar asignación existente
                        $documentMandatory->desactivar($existente['id']);
                    }
                }
            }

            // Confirmar transacción
            $conexion->commit();
            respond(true, "Asignación guardada correctamente.");
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conexion->rollback();
            respond(false, "Error al guardar la asignación: " . $e->getMessage());
        }
        break;

    case 'selectDocumentName':
        require_once "../modelos/DocumentName.php";
        $documentName = new DocumentName();
        $rspta = $documentName->select();
        if ($rspta) {
            while ($reg = $rspta->fetch_object()) {
                echo '<option value="' . htmlspecialchars($reg->id) . '">' . htmlspecialchars($reg->documentName) . '</option>';
            }
        }
        break;

    case 'listarDocumentosAsignados':
        $position_id = getPostParam('position_id');
        if (empty($position_id)) {
            respond(false, "El ID del puesto es requerido.");
            exit();
        }

        $rspta = $documentMandatory->listarDocumentosAsignados($position_id);
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }
        respond(true, "Documentos asignados obtenidos correctamente.", $data);
        break;

    case 'selectJobPositions':
        require_once "../modelos/Jobs.php";
        $jobPositions = new Jobs();
        $rspta = $jobPositions->select();
        if ($rspta) {
            while ($reg = $rspta->fetch_object()) {
                echo '<option value="' . htmlspecialchars($reg->id) . '">' . htmlspecialchars($reg->position_name) . '</option>';
            }
        }
        break;

    case 'selectCompanies':
        require_once "../modelos/Companies.php"; // Asegúrate de tener un modelo Companies
        $companies = new Companies();
        $rspta = $companies->select();
        if ($rspta) {
            while ($reg = $rspta->fetch_object()) {
                echo '<option value="' . htmlspecialchars($reg->id) . '">' . htmlspecialchars($reg->company_name) . '</option>';
            }
        }
        break;

    case 'selectJobPositionsByCompany':
        $company_id = getPostParam('company_id');
        if (empty($company_id)) {
            respond(false, "El ID de la empresa es requerido.");
            exit();
        }

        require_once "../modelos/Jobs.php";
        $jobPositions = new Jobs();
        $rspta = $jobPositions->select($company_id); // Corregido: Usar select con company_id
        // Nota: Asegúrate de que el método 'select' en Jobs.php acepta company_id como parámetro

        if ($rspta) {
            if ($rspta->num_rows > 0) {
                while ($reg = $rspta->fetch_object()) {
                    echo '<option value="' . htmlspecialchars($reg->id) . '">' . htmlspecialchars($reg->position_name) . '</option>';
                }
            } else {
                echo '<option value="">No hay puestos disponibles</option>';
            }
        } else {
            echo '<option value="">Error al cargar puestos</option>';
        }
        break;

    case 'listarPuestosConDocumentos':
        $rspta = $documentMandatory->listarPuestosConDocumentos();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $badge = $reg->document_type === 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : '<span class="badge badge-warning">Opcional</span>';
            $data[] = array(
                "0" => htmlspecialchars($reg->position_name),
                "1" => htmlspecialchars($reg->documentName),
                "2" => $badge,
                "3" => htmlspecialchars($reg->created_at),
                "4" => htmlspecialchars($reg->updated_at),
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listarPuestosConDocumentosPorEmpresa':
        $rspta = $documentMandatory->listarPuestosConDocumentosPorEmpresa();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $badge = $reg->document_type === 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : '<span class="badge badge-warning">Opcional</span>';
            $data[] = array(
                "0" => htmlspecialchars($reg->company_name),
                "1" => htmlspecialchars($reg->position_name),
                "2" => htmlspecialchars($reg->documentName),
                "3" => $badge,
                "4" => htmlspecialchars($reg->created_at),
                "5" => htmlspecialchars($reg->updated_at),
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listarPuestosSinDocumentos':
        $sql = "SELECT 
                    c.company_name,
                    j.id AS position_id, 
                    j.position_name
                FROM jobs j
                LEFT JOIN mandatory_documents md 
                    ON j.id = md.position_id 
                    AND md.is_active = 1
                INNER JOIN areas a 
                    ON j.area_id = a.id
                INNER JOIN companies c 
                    ON c.id = a.company_id
                WHERE md.id IS NULL 
                AND j.is_active = 1
                ORDER BY c.company_name ASC, j.position_name ASC";

        $rspta = ejecutarConsulta($sql);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => htmlspecialchars($reg->company_name),
                "1" => htmlspecialchars($reg->position_id),
                "2" => htmlspecialchars($reg->position_name),
                "3" => '<span class="badge badge-danger">No asignado</span>',
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listarPuestosConDocumentosPorEmpresaCompleto':
        $rspta = $documentMandatory->listarPuestosConDocumentosPorEmpresaCompleto();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $asignado = $reg->doc_asignado ? '<span class="badge badge-success">Asignado</span>' : '<span class="badge badge-danger">No asignado</span>';
            $documentName = $reg->documentName ? htmlspecialchars($reg->documentName) : 'Ninguno';
            $documentType = $reg->document_type === 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : 
                            ($reg->document_type === 'opcional' ? '<span class="badge badge-warning">Opcional</span>' : 'N/A');

            $data[] = array(
                "0" => htmlspecialchars($reg->company_name),
                "1" => htmlspecialchars($reg->position_name),
                "2" => $documentName,
                "3" => $documentType,
                "4" => $asignado,
                "5" => $reg->created_at ? htmlspecialchars($reg->created_at) : 'N/A',
                "6" => $reg->updated_at ? htmlspecialchars($reg->updated_at) : 'N/A'
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    default:
        respond(false, "Operación no válida.");
        break;
}
?>
