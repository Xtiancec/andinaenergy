<?php
require_once "../modelos/Applicant.php";

class ApplicantController
{
    // Guardar un nuevo postulante
    public function guardar()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $applicant = new Applicant();
    
        // Sanitizar entradas
        $company_id = limpiarCadena($_POST["company_id"] ?? "");
        $area_id = limpiarCadena($_POST["area_id"] ?? "");
        $job_id = limpiarCadena($_POST["job_id"] ?? "");
        $username = limpiarCadena($_POST["username"] ?? "");
        $email = limpiarCadena($_POST["email"] ?? "");
        $lastname = limpiarCadena($_POST["lastname"] ?? "");
        $surname = limpiarCadena($_POST["surname"] ?? "");
        $names = limpiarCadena($_POST["names"] ?? "");
    
        // Validación de campos vacíos
        if (empty($company_id) || empty($area_id) || empty($job_id) || empty($username) || empty($email) || empty($lastname) || empty($surname) || empty($names)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
            exit;
        }
    
        // Validación de formato DNI
        if (!preg_match('/^\d{8}$/', $username)) {
            echo json_encode(['status' => 'error', 'message' => 'El DNI debe contener exactamente 8 dígitos.']);
            exit;
        }
    
        // Validación de formato Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'El email ingresado no es válido.']);
            exit;
        }
    
        // Validación de longitud de nombres
        if (strlen($lastname) > 100 || strlen($surname) > 100 || strlen($names) > 100) {
            echo json_encode(['status' => 'error', 'message' => 'Los apellidos y nombres no deben exceder los 100 caracteres.']);
            exit;
        }
    
        // Inserción con area_id
        $rspta = $applicant->insertar($company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names);
        if ($rspta === "Postulante registrado correctamente y correo enviado.") {
            echo json_encode(['status' => 'success', 'message' => $rspta]);
        } elseif ($rspta === "Postulante creado, pero no se pudo enviar el correo.") {
            echo json_encode(['status' => 'warning', 'message' => $rspta]);
        } else {
            // Suponiendo que cualquier otro mensaje es un error
            echo json_encode(['status' => 'error', 'message' => $rspta]);
        }
    }
    

    // Función para listar los puestos por área
    public function listarPuestosPorArea()
    {
        $applicant = new Applicant();
        $area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
        if (empty($area_id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID del área no especificado.']);
            exit;
        }
        $rspta = $applicant->listarPuestosPorArea($area_id);
        $puestos = $rspta->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $puestos]);
    }

    // Función para editar un postulante
    public function editar()
    {
        $applicant = new Applicant();
    
        // Sanitizar entradas
        $id = limpiarCadena($_POST["idUpdate"] ?? "");
        $company_id = limpiarCadena($_POST["company_idUpdate"] ?? "");
        $area_id = limpiarCadena($_POST["area_idUpdate"] ?? "");
        $job_id = limpiarCadena($_POST["job_idUpdate"] ?? "");
        $username = limpiarCadena($_POST["usernameUpdate"] ?? "");
        $email = limpiarCadena($_POST["emailUpdate"] ?? "");
        $lastname = limpiarCadena($_POST["lastnameUpdate"] ?? "");
        $surname = limpiarCadena($_POST["surnameUpdate"] ?? "");
        $names = limpiarCadena($_POST["namesUpdate"] ?? "");
    
        // Validación de campos vacíos
        if (empty($id) || empty($company_id) || empty($area_id) || empty($job_id) || empty($username) || empty($email) || empty($lastname) || empty($surname) || empty($names)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
            exit;
        }
    
        // Validación de formato DNI
        if (!preg_match('/^\d{8}$/', $username)) {
            echo json_encode(['status' => 'error', 'message' => 'El DNI debe contener exactamente 8 dígitos.']);
            exit;
        }
    
        // Validación de formato Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'El email ingresado no es válido.']);
            exit;
        }
    
        // Validación de longitud de nombres
        if (strlen($lastname) > 100 || strlen($surname) > 100 || strlen($names) > 100) {
            echo json_encode(['status' => 'error', 'message' => 'Los apellidos y nombres no deben exceder los 100 caracteres.']);
            exit;
        }
    
        // Actualizar el postulante
        $rspta = $applicant->editar($id, $company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names);
        if ($rspta) {
            echo json_encode(['status' => 'success', 'message' => 'Postulante actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el postulante.']);
        }
    }
    

    // Función para mostrar los datos de un postulante
    public function mostrar()
    {
        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID del postulante no especificado.']);
            exit;
        }

        $rspta = $applicant->mostrar($id);
        if ($rspta) {
            echo json_encode(['status' => 'success', 'data' => $rspta]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Postulante no encontrado.']);
        }
    }

    // Función para listar los postulantes
    public function listar()
    {
        $applicant = new Applicant();
        $rspta = $applicant->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $full_name = "{$reg->lastname} {$reg->surname} {$reg->names}";
            $data[] = array(
                "ID" => $reg->id,
                "DNI" => htmlspecialchars($reg->username),
                "Email" => htmlspecialchars($reg->email),
                "Nombre Completo" => htmlspecialchars($full_name),
                "Empresa" => htmlspecialchars($reg->company_name),
                "Área" => htmlspecialchars($reg->area_name),
                "Puesto" => htmlspecialchars($reg->position_name),
                "Estado" => $reg->is_active ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>',
                "Opciones" => $reg->is_active
                    ? '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>
                       <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')"><i class="fa fa-close"></i></button>'
                    : '<button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button>'
            );
        }

        $results = array(
            "data" => $data
        );

        echo json_encode($results);
    }

    // Función para desactivar postulante
    public function desactivar()
    {
        // Iniciar sesión si no está iniciada


        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID del postulante no especificado.']);
            exit;
        }

        $rspta = $applicant->desactivar($id);
        if ($rspta) {
            echo json_encode(['status' => 'success', 'message' => 'Postulante desactivado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo desactivar el postulante.']);
        }
    }

    // Función para activar postulante
    public function activar()
    {
 

        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID del postulante no especificado.']);
            exit;
        }

        $rspta = $applicant->activar($id);
        if ($rspta) {
            echo json_encode(['status' => 'success', 'message' => 'Postulante activado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo activar el postulante.']);
        }
    }

    // Función para listar todas las empresas
    public function listarEmpresas()
    {
        $applicant = new Applicant();
        $rspta = $applicant->listarEmpresas();
        $empresas = $rspta->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $empresas]);
    }

    // Función para listar todas las áreas por empresa
    public function listarAreasPorEmpresa()
    {
        $applicant = new Applicant();
        $company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        if (empty($company_id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID de la empresa no especificado.']);
            exit;
        }
        $rspta = $applicant->listarAreasPorEmpresa($company_id);
        $areas = $rspta->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $areas]);
    }

    // Función para listar todos los puestos activos
    public function listarPuestosActivos()
    {
        $applicant = new Applicant();
        $rspta = $applicant->listarPuestosActivos();
        $puestos = $rspta->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $puestos]);
    }

    // Función para listar puestos por área

}

// Instancia y llamada a métodos según la operación
if (isset($_GET["op"])) {
    $controller = new ApplicantController();
    switch ($_GET["op"]) {
        case 'listar':
            $controller->listar();
            break;
        case 'guardar':
            $controller->guardar();
            break;
        case 'editar':
            $controller->editar();
            break;
        case 'mostrar':
            $controller->mostrar();
            break;
        case 'desactivar':
            $controller->desactivar();
            break;
        case 'activar':
            $controller->activar();
            break;
        case 'listarEmpresas':
            $controller->listarEmpresas();
            break;
        case 'listarAreasPorEmpresa':
            $controller->listarAreasPorEmpresa();
            break;
        case 'listarPuestosActivos':
            $controller->listarPuestosActivos();
            break;
        case 'listarPuestosPorArea':
            $controller->listarPuestosPorArea();
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Operación no válida.']);
            break;
    }
}
?>
