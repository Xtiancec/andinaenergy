<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user' || $_SESSION['user_role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}
require_once "../modelos/Companies.php";

$companies = new Companies();

// Obtener todos los parámetros POST necesarios
$company_id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
$company_name = isset($_POST["company_name"]) ? limpiarCadena($_POST["company_name"]) : "";
$ruc = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
$description = isset($_POST["description"]) ? limpiarCadena($_POST["description"]) : "";

switch ($_GET["op"]) {
    case 'guardar':
        $rspta = $companies->insertar($company_name, $ruc, $description);
        if ($rspta) {
            echo "Datos registrados correctamente";
        } else {
            echo "El RUC ya existe. No se pudo registrar los datos";
        }
        break;

    case 'editar':
        $rspta = $companies->editar($company_id, $company_name, $ruc, $description);
        if ($rspta) {
            echo "Datos actualizados correctamente";
        } else {
            echo "El RUC ya existe en otra empresa. No se pudo actualizar los datos";
        }
        break;

    case 'desactivar':
        $rspta = $companies->desactivar($company_id);
        echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
        break;

    case 'activar':
        $rspta = $companies->activar($company_id);
        echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
        break;

    case 'mostrar':
        $rspta = $companies->mostrar($company_id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $companies->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id,
                "1" => htmlspecialchars($reg->company_name),
                "2" => htmlspecialchars($reg->ruc),
                "3" => htmlspecialchars($reg->description),
                "4" => $reg->created_at,
                "5" => $reg->updated_at,
                "6" => ($reg->is_active) ? '<span class="badge badge-success">Activado</span>' : '<span class="badge badge-danger">Desactivado</span>',
                "7" => ($reg->is_active) ?
                    '<button class="btn btn-warning btn-xs btn-edit" data-id="' . $reg->id . '">
                        <i class="fa fa-edit"></i>
                    </button>' . ' ' .
                    '<button class="btn btn-danger btn-xs btn-desactivar" data-id="' . $reg->id . '">
                        <i class="fa fa-window-close"></i>
                    </button>' :
                    '<button class="btn btn-warning btn-xs btn-edit" data-id="' . $reg->id . '">
                        <i class="fa fa-edit"></i>
                    </button>' . ' ' .
                    '<button class="btn btn-primary btn-xs btn-activar" data-id="' . $reg->id . '">
                        <i class="fa fa-check-square"></i>
                    </button>',
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'verificar_ruc':
        $ruc_verificar = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
        $id_verificar = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : null;

        $rspta = $companies->verificarRuc($ruc_verificar, $id_verificar);
        echo $rspta ? "RUC ya existe" : "RUC disponible";
        break;
}
?>
