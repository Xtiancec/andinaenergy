<?php
require_once "../modelos/DocumentName.php";

$document = new DocumentName();

// Función para limpiar y validar los datos de entrada
function getPostParam($key, $default = "")
{
    return isset($_POST[$key]) ? limpiarCadena($_POST[$key]) : $default;
}

$id = getPostParam("id");
$documentName = getPostParam("documentName");

switch ($_GET["op"]) {
    case 'guardar':
        if (empty($documentName)) {
            echo "El nombre del documento es requerido.";
            exit();
        }
        $rspta = $document->insertar($documentName);
        echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
        break;

    case 'editar':
        if (empty($id) || empty($documentName)) {
            echo "El ID y el nombre del documento son requeridos.";
            exit();
        }
        $rspta = $document->editar($id, $documentName);
        echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
        break;

    case 'desactivar':
        if (empty($id)) {
            echo "El ID es requerido para desactivar.";
            exit();
        }
        $rspta = $document->desactivar($id);
        echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
        break;

    case 'activar':
        if (empty($id)) {
            echo "El ID es requerido para activar.";
            exit();
        }
        $rspta = $document->activar($id);
        echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
        break;

    case 'mostrar':
        if (empty($id)) {
            echo json_encode(["error" => "El ID es requerido para mostrar."]);
            exit();
        }
        $rspta = $document->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $document->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => htmlspecialchars($reg->id),
                "1" => htmlspecialchars($reg->documentName),
                "2" => htmlspecialchars($reg->created_at),
                "3" => htmlspecialchars($reg->updated_at),
                "4" => ($reg->is_active) ? '<span class="badge badge-info">Activado</span>' : '<span class="badge badge-danger">Desactivado</span>',
                "5" => ($reg->is_active) ?
                    '<button class="btn btn-warning btn-circle btn-edit" data-id="' . htmlspecialchars($reg->id) . '">
                        <i class="icon icon-pencil"></i>
                    </button> ' .
                    '<button class="btn btn-danger btn-circle btn-desactivar" data-id="' . htmlspecialchars($reg->id) . '">
                        <i class="mdi mdi-close-box"></i>
                    </button>' :
                    '<button class="btn btn-warning btn-circle btn-edit" data-id="' . htmlspecialchars($reg->id) . '">
                        <i class="icon icon-pencil"></i>
                    </button> ' .
                    '<button class="btn btn-info btn-circle btn-activar" data-id="' . htmlspecialchars($reg->id) . '">
                        <i class="fa fa-check-square"></i>
                    </button>',
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    default:
        echo "Operación no válida.";
        break;
}
?>
