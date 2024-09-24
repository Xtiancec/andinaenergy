<?php
require_once "../modelos/DocumentMandatory.php";

$documentMandatory = new DocumentMandatory();

$id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
$position_id = isset($_POST["position_id"]) ? limpiarCadena($_POST["position_id"]) : "";
$document_type = isset($_POST["document_type"]) ? limpiarCadena($_POST["document_type"]) : "";
$documentName_id = isset($_POST["documentName_id"]) ? limpiarCadena($_POST["documentName_id"]) : "";

switch ($_GET["op"]) {
    case 'guardar':
        $rspta = $documentMandatory->insertar($position_id, $document_type, $documentName_id);
        echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
        break;

    case 'editar':
        $rspta = $documentMandatory->editar($id, $position_id, $document_type, $documentName_id);
        echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
        break;

    case 'desactivar':
        $rspta = $documentMandatory->desactivar($id);
        echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
        break;

    case 'activar':
        $rspta = $documentMandatory->activar($id);
        echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
        break;

    case 'mostrar':
        $rspta = $documentMandatory->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listarDocumentosActivos':
        $rspta = $documentMandatory->listarDocumentosActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }
        echo json_encode($data);
        break;

    case 'guardarAsignacion':
        $position_id = $_POST['position_id'];
        $documentosSeleccionados = json_decode($_POST['documentosSeleccionados'], true);
        $documentosDesmarcados = json_decode($_POST['documentosDesmarcados'], true);

        foreach ($documentosSeleccionados as $doc) {
            $documentName_id = $doc['documentName_id'];
            $document_type = $doc['document_type'];

            // Verificar si ya existe una asignación
            $existente = $documentMandatory->verificarExistencia($position_id, $documentName_id);

            if ($existente) {
                // Actualizar si ya existe
                $documentMandatory->editar($existente['id'], $position_id, $document_type, $documentName_id);
            } else {
                // Insertar si no existe
                $documentMandatory->insertar($position_id, $document_type, $documentName_id);
            }
        }

        // Procesar documentos desmarcados (desactivarlos)
        foreach ($documentosDesmarcados as $doc) {
            $documentName_id = $doc['documentName_id'];
            $existente = $documentMandatory->verificarExistencia($position_id, $documentName_id);

            if ($existente) {
                // Desactivar si existe
                $documentMandatory->desactivar($existente['id']);
            }
        }

        echo "Asignación guardada correctamente.";
        break;

    case 'selectDocumentName':
        require_once "../modelos/DocumentName.php";
        $documentName = new DocumentName();
        $rspta = $documentName->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->documentName . '</option>';
        }
        break;

    case 'listarDocumentosAsignados':
        $position_id = $_POST['position_id'];
        $rspta = $documentMandatory->listarDocumentosAsignados($position_id);
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }
        echo json_encode($data);
        break;

    case 'selectJobPositions':
        require_once "../modelos/Jobs.php";
        $jobPositions = new Jobs();
        $rspta = $jobPositions->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->position_name . '</option>';
        }
        break;
        // En el controlador DocumentMandatoryController.php

    case 'selectCompanies':
        require_once "../modelos/Companies.php"; // Asegúrate de tener un modelo Companies
        $companies = new Companies();
        $rspta = $companies->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->company_name . '</option>';
        }
        break;

    case 'selectJobPositionsByCompany':
        $company_id = $_POST['company_id'];
        require_once "../modelos/Jobs.php";
        $jobPositions = new Jobs();
        $rspta = $jobPositions->select($company_id);
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->position_name . '</option>';
        }
        break;



    case 'listarPuestosConDocumentos':
        $rspta = $documentMandatory->listarPuestosConDocumentos();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->position_name,
                "1" => $reg->documentName,
                "2" => $reg->document_type == 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : '<span class="badge badge-warning">Opcional</span>',
                "3" => $reg->created_at,
                "4" => $reg->updated_at,
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

        // En el controlador DocumentMandatoryController.php

    case 'listarPuestosConDocumentosPorEmpresa':
        $rspta = $documentMandatory->listarPuestosConDocumentosPorEmpresa();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->company_name,
                "1" => $reg->position_name,
                "2" => $reg->documentName,
                "3" => $reg->document_type == 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : '<span class="badge badge-warning">Opcional</span>',
                "4" => $reg->created_at,
                "5" => $reg->updated_at,
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listarPuestosSinDocumentos':
        $sql = "SELECT 
                        companies.company_name,
                        job_positions.id AS position_id, 
                        job_positions.position_name
                    FROM job_positions
                    LEFT JOIN mandatory_documents 
                        ON job_positions.id = mandatory_documents.position_id 
                    INNER JOIN companies 
                        ON companies.id = job_positions.company_id
                    WHERE mandatory_documents.id IS NULL 
                    AND job_positions.is_active = '1'
                    ORDER BY companies.company_name, job_positions.position_name";

        $rspta = ejecutarConsulta($sql);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->company_name,
                "1" => $reg->position_id,
                "2" => $reg->position_name,
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
            $documentName = $reg->documentName ? $reg->documentName : 'Ninguno';
            $documentType = $reg->document_type == 'obligatorio' ? '<span class="badge badge-success">Obligatorio</span>' : ($reg->document_type == 'opcional' ? '<span class="badge badge-warning">Opcional</span>' : 'N/A');

            $data[] = array(
                "0" => $reg->company_name,
                "1" => $reg->position_name,
                "2" => $documentName,
                "3" => $documentType,
                "4" => $asignado,
                "5" => $reg->created_at ? $reg->created_at : 'N/A',
                "6" => $reg->updated_at ? $reg->updated_at : 'N/A'
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;
}
