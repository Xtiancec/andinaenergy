<?php
require_once "../modelos/Applicant.php";

class ApplicantController
{
    // Guardar o actualizar postulante
    public function guardar()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Verificar el token CSRF si lo implementaste
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
            exit;
        }
    
        $applicant = new Applicant();
    
        $company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        $area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
        $job_id = isset($_POST["job_id"]) ? limpiarCadena($_POST["job_id"]) : "";
        $username = isset($_POST["username"]) ? limpiarCadena($_POST["username"]) : "";
        $email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
        $lastname = isset($_POST["lastname"]) ? limpiarCadena($_POST["lastname"]) : "";
        $surname = isset($_POST["surname"]) ? limpiarCadena($_POST["surname"]) : "";
        $names = isset($_POST["names"]) ? limpiarCadena($_POST["names"]) : "";
    
        // Validación adicional en el servidor
        if (empty($company_id) || empty($area_id) || empty($job_id) || empty($username) || empty($email) || empty($lastname) || empty($surname) || empty($names)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
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
        $rspta = $applicant->listarPuestosPorArea($area_id);
        echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
    }

    public function editar()
    {
        $applicant = new Applicant();

        $id = isset($_POST["idUpdate"]) ? limpiarCadena($_POST["idUpdate"]) : "";
        $company_id = isset($_POST["company_idUpdate"]) ? limpiarCadena($_POST["company_idUpdate"]) : "";
        $area_id = isset($_POST["area_idUpdate"]) ? limpiarCadena($_POST["area_idUpdate"]) : ""; // Se agrega el campo area_id
        $job_id = isset($_POST["job_idUpdate"]) ? limpiarCadena($_POST["job_idUpdate"]) : "";
        $username = isset($_POST["usernameUpdate"]) ? limpiarCadena($_POST["usernameUpdate"]) : "";
        $email = isset($_POST["emailUpdate"]) ? limpiarCadena($_POST["emailUpdate"]) : "";
        $lastname = isset($_POST["lastnameUpdate"]) ? limpiarCadena($_POST["lastnameUpdate"]) : "";
        $surname = isset($_POST["surnameUpdate"]) ? limpiarCadena($_POST["surnameUpdate"]) : "";
        $names = isset($_POST["namesUpdate"]) ? limpiarCadena($_POST["namesUpdate"]) : "";

        // Ejecutar actualización con area_id
        $rspta = $applicant->editar($id, $company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names);

        echo $rspta ? "Postulante actualizado correctamente" : "Error al actualizar el postulante";
    }

    // Función para mostrar los datos de un postulante
    public function mostrar()
    {
        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $applicant->mostrar($id);
        echo json_encode($rspta);
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
        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $applicant->desactivar($id);
        echo $rspta ? "Postulante desactivado correctamente" : "No se pudo desactivar el postulante";
    }

    // Función para activar postulante
    public function activar()
    {
        $applicant = new Applicant();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $applicant->activar($id);
        echo $rspta ? "Postulante activado correctamente" : "No se pudo activar el postulante";
    }

    // Función para listar todas las empresas
    public function listarEmpresas()
    {
        $applicant = new Applicant();
        $rspta = $applicant->listarEmpresas();
        echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
    }

    // Función para listar todas las áreas por empresa
    public function listarAreasPorEmpresa()
    {
        $applicant = new Applicant();
        $company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        $rspta = $applicant->listarAreasPorEmpresa($company_id);
        echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
    }

    // Función para listar todos los puestos activos
    public function listarPuestosActivos()
    {
        $applicant = new Applicant();
        $rspta = $applicant->listarPuestosActivos();
        echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
    }
}

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
    }
}
