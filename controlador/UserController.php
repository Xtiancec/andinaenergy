<?php
require_once "../config/Conexion.php";
require_once "../modelos/User.php";
require_once "../modelos/Jobs.php";

class UserController
{
    public function __construct() {}

    // Función para listar todos los usuarios
    public function listar()
    {
        $user = new User();
        $result = $user->listar();

        if (!$result) {
            error_log("Error al obtener la lista de usuarios.");
            echo json_encode([
                "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            exit();
        }

        $data = array();
        $total = 0;

        while ($reg = $result->fetch_object()) {
            $full_name = "{$reg->lastname} {$reg->surname} {$reg->names}";
            $data[] = array(
                "id" => $reg->id,
                "company_name" => htmlspecialchars($reg->company_name),
                "area_name" => htmlspecialchars($reg->area_name),
                "position_name" => htmlspecialchars($reg->position_name),
                "username" => htmlspecialchars($reg->username),
                "full_name" => htmlspecialchars($full_name),
                "email" => htmlspecialchars($reg->email),
                "role" => htmlspecialchars($reg->role),
                "is_active" => $reg->is_active ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>',
                "options" => $reg->is_active
                    ? '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button> 
                       <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')"><i class="fa fa-close"></i></button> 
                       <button class="btn btn-info btn-sm" onclick="mostrarHistorial(' . $reg->id . ')"><i class="fa fa-clock-o"></i></button>'
                    : '<button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button>'
            );
            $total++;
        }

        // Obtener parámetros enviados por DataTables
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
        $recordsTotal = $total;
        $recordsFiltered = $total;

        $results = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        );

        echo json_encode($results);
    }

    // Función para insertar un nuevo usuario
    public function insertar()
    {
        $user = new User();
    
        // Recibir y limpiar los datos del formulario
        $company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        $area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
        $identification_type = isset($_POST["identification_type"]) ? limpiarCadena($_POST["identification_type"]) : "";
        $username = isset($_POST["username"]) ? limpiarCadena($_POST["username"]) : "";
        $email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
        $lastname = isset($_POST["lastname"]) ? limpiarCadena($_POST["lastname"]) : "";
        $surname = isset($_POST["surname"]) ? limpiarCadena($_POST["surname"]) : "";
        $names = isset($_POST["names"]) ? limpiarCadena($_POST["names"]) : "";
        $nacionality = isset($_POST["nacionality"]) ? limpiarCadena($_POST["nacionality"]) : "";
        $role = isset($_POST["role"]) ? limpiarCadena($_POST["role"]) : "";
        $job_id = isset($_POST["job_id"]) ? limpiarCadena($_POST["job_id"]) : "";
        $is_employee = isset($_POST["is_employee"]) ? limpiarCadena($_POST["is_employee"]) : 1;
    
        $rspta = $user->insertar(
            $company_id,
            $area_id,
            $identification_type,
            $username,
            $email,
            $lastname,
            $surname,
            $names,
            $nacionality,
            $role,
            $job_id,
            $is_employee
        );
    
        // Verificar la respuesta y enviar un mensaje adecuado
        if ($rspta === "Usuario registrado correctamente y correo enviado.") {
            echo json_encode(['success' => true, 'message' => $rspta]);
        } else {
            echo json_encode(['success' => false, 'message' => $rspta]);
        }
    }
    
    // Función para actualizar un usuario
    public function actualizar()
    {
        $user = new User();
    
        // Recibir y limpiar los datos del formulario
        $id = isset($_POST["idUpdate"]) ? limpiarCadena($_POST["idUpdate"]) : "";
        $company_id = isset($_POST["company_idUpdate"]) ? limpiarCadena($_POST["company_idUpdate"]) : "";
        $area_id = isset($_POST["area_idUpdate"]) ? limpiarCadena($_POST["area_idUpdate"]) : "";
        $identification_type = isset($_POST["identification_typeUpdate"]) ? limpiarCadena($_POST["identification_typeUpdate"]) : "";
        $username = isset($_POST["usernameUpdate"]) ? limpiarCadena($_POST["usernameUpdate"]) : "";
        $email = isset($_POST["emailUpdate"]) ? limpiarCadena($_POST["emailUpdate"]) : "";
        $lastname = isset($_POST["lastnameUpdate"]) ? limpiarCadena($_POST["lastnameUpdate"]) : "";
        $surname = isset($_POST["surnameUpdate"]) ? limpiarCadena($_POST["surnameUpdate"]) : "";
        $names = isset($_POST["namesUpdate"]) ? limpiarCadena($_POST["namesUpdate"]) : "";
        $nacionality = isset($_POST["nacionalityUpdate"]) ? limpiarCadena($_POST["nacionalityUpdate"]) : "";
        $role = isset($_POST["roleUpdate"]) ? limpiarCadena($_POST["roleUpdate"]) : "";
        $job_id = isset($_POST["job_idUpdate"]) ? limpiarCadena($_POST["job_idUpdate"]) : "";
        $is_employee = isset($_POST["is_employeeUpdate"]) ? limpiarCadena($_POST["is_employeeUpdate"]) : 1;
    
        $rspta = $user->editar(
            $id,
            $company_id,
            $area_id,
            $identification_type,
            $username,
            $email,
            $lastname,
            $surname,
            $names,
            $nacionality,
            $role,
            $job_id,
            $is_employee
        );
    
        if ($rspta === "Usuario actualizado correctamente.") {
            echo json_encode(['success' => true, 'message' => $rspta]);
        } else {
            echo json_encode(['success' => false, 'message' => $rspta]);
        }
    }
    

    // Función para mostrar un usuario específico
// Función para mostrar un usuario específico
public function mostrar()
{
    $user = new User();
    $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
    
    // Ejecutar la consulta para obtener el usuario
    $rspta = $user->mostrar($id);

    // Verificar si hay un resultado válido
    if ($rspta) {
        echo json_encode($rspta);  // Ya que es un array asociativo, se puede convertir directamente en JSON
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al mostrar el usuario.']);
    }
}


    // Función para activar un usuario
    public function activar()
    {
        $user = new User();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $user->activar($id);

        if ($rspta === "Usuario activado correctamente.") {
            echo json_encode(['success' => true, 'message' => $rspta]);
        } else {
            echo json_encode(['success' => false, 'message' => $rspta]);
        }
    }

    // Función para desactivar un usuario
    public function desactivar()
    {
        $user = new User();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $user->desactivar($id);

        if ($rspta === "Usuario desactivado correctamente.") {
            echo json_encode(['success' => true, 'message' => $rspta]);
        } else {
            echo json_encode(['success' => false, 'message' => $rspta]);
        }
    }

    // Función para listar Áreas por Empresa
    public function listarAreasPorEmpresa()
    {
        $company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        $user = new User();
        $rspta = $user->listarAreasPorEmpresa($company_id);
        if ($rspta) {
            echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode([]);
        }
    }

    // Función para listar Puestos por Área
    public function listarPuestosPorArea()
    {
        $area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
        $jobPositions = new Jobs();
        $rspta = $jobPositions->listar_por_area($area_id);
        if ($rspta) {
            echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode([]);
        }
    }

    // Función para listar todas las empresas (para los selects)
    public function listarEmpresas()
    {
        $user = new User();
        $rspta = $user->listarEmpresas();
        if ($rspta) {
            echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode([]);
        }
    }

    // Función para listar todos los puestos de trabajo activos (para el select)
    public function listarPuestosActivos()
    {
        $jobPositions = new Jobs();
        $rspta = $jobPositions->listarPuestosActivos();
        if ($rspta) {
            echo json_encode($rspta->fetch_all(MYSQLI_ASSOC));
        } else {
            echo json_encode([]);
        }
    }

    // Función para obtener el historial de accesos de un usuario
    public function obtenerHistorialAcceso()
    {
        $userId = isset($_POST['userId']) ? limpiarCadena($_POST['userId']) : "";
        $user = new User();
        $history = $user->obtenerHistorialAcceso($userId);

        if ($history) {
            echo json_encode(['success' => true, 'history' => $history->fetch_all(MYSQLI_ASSOC)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo obtener el historial de accesos.']);
        }
    }


    // Función para cambiar la contraseña de un usuario
    public function cambiarPassword()
    {
        $user = new User();

        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $newPassword = isset($_POST["newPassword"]) ? limpiarCadena($_POST["newPassword"]) : "";

        $rspta = $user->cambiarPassword($id, $newPassword);
        if ($rspta === "Contraseña actualizada correctamente.") {
            echo json_encode(['success' => true, 'message' => $rspta]);
        } else {
            echo json_encode(['success' => false, 'message' => $rspta]);
        }
    }

    // Función para verificar duplicados de username
    public function verificarDuplicado()
    {
        $user = new User();
        $username = isset($_POST["username"]) ? limpiarCadena($_POST["username"]) : "";
        $userId = isset($_POST["userId"]) ? limpiarCadena($_POST["userId"]) : null;
    
        // Verificar si username ya existe, excepto para el usuario actual (cuando es actualización)
        $usernameExiste = $user->verificarDuplicadoUsername($username, $userId);
    
        if ($usernameExiste) {
            echo json_encode(['existsUsername' => true]);
        } else {
            echo json_encode(['existsUsername' => false]);
        }
    }
    
}

// Manejo de operaciones
if (isset($_GET['op'])) {
    $controller = new UserController();
    switch ($_GET['op']) {
        case 'listar':
            $controller->listar();
            break;
        case 'insertar':
            $controller->insertar();
            break;
        case 'actualizar':
            $controller->actualizar();
            break;
        case 'mostrar':
            $controller->mostrar();
            break;
        case 'activar':
            $controller->activar();
            break;
        case 'desactivar':
            $controller->desactivar();
            break;
        case 'listarAreasPorEmpresa':
            $controller->listarAreasPorEmpresa();
            break;
        case 'listarPuestosPorArea':
            $controller->listarPuestosPorArea();
            break;
        case 'listarEmpresas':
            $controller->listarEmpresas();
            break;
        case 'listarPuestosActivos':
            $controller->listarPuestosActivos();
            break;
        case 'obtenerHistorialAcceso':
            $controller->obtenerHistorialAcceso();
            break;
        case 'cambiarPassword':
            $controller->cambiarPassword();
            break;
        case 'verificarDuplicado':
            $controller->verificarDuplicado();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
