<?php
session_start();

// Habilitar la visualización de errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar output buffering
ob_start();

header('Content-Type: application/json');

require_once "../modelos/Experience.php";

// Función para limpiar cadenas
// Elimina esta función si ya está definida en Conexion.php
// function limpiarCadena($cadena) {
//     return htmlspecialchars(strip_tags($cadena));
// }

// Verificar si el usuario ha iniciado sesión y es un postulante
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    echo json_encode(['status' => false, 'message' => 'Acceso no autorizado']);
    ob_end_flush();
    exit();
}

class ExperienceController
{
    private $experience;

    public function __construct()
    {
        $this->experience = new Experience();
    }

    private function manejarCargaArchivo($tipo, $applicant_id)
    {
        $archivo = null;

        if (isset($_FILES['file_' . $tipo]) && $_FILES['file_' . $tipo]['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['file_' . $tipo]['name'];
            $tamanioArchivo = $_FILES['file_' . $tipo]['size'];
            $tmpArchivo = $_FILES['file_' . $tipo]['tmp_name'];

            // Validar el tipo de archivo
            $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $extencionArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (!in_array($extencionArchivo, $extensionesPermitidas)) {
                throw new Exception("Tipo de archivo no permitido.");
            }

            // Validar el tamaño del archivo (máximo 5MB)
            $tamanioMaximo = 5 * 1024 * 1024; // 5MB
            if ($tamanioArchivo > $tamanioMaximo) {
                throw new Exception("El archivo excede el tamaño máximo permitido de 5MB.");
            }

            // Crear la estructura de carpetas si no existe
            $rutaBase = "../uploads/applicants/user_" . intval($applicant_id) . "/";

            if ($tipo === 'education') {
                $rutaDestino = $rutaBase . "education_experience/";
            } else {
                $rutaDestino = $rutaBase . "work_experience/";
            }

            if (!file_exists($rutaDestino)) {
                if (!mkdir($rutaDestino, 0755, true)) {
                    throw new Exception("No se pudo crear la carpeta para subir archivos.");
                }
            }

            // Generar un nombre único para el archivo
            $nombreUnico = uniqid() . "_" . basename($nombreArchivo);
            $rutaFinal = $rutaDestino . $nombreUnico;

            // Mover el archivo al destino final
            if (!move_uploaded_file($tmpArchivo, $rutaFinal)) {
                throw new Exception("Error al mover el archivo subido.");
            }

            // Guardar la ruta relativa en la base de datos
            $archivo = "uploads/applicants/user_" . intval($applicant_id) . "/" . ($tipo === 'education' ? "education_experience/" : "work_experience/") . $nombreUnico;
        }

        return $archivo;
    }

    // Guardar o actualizar experiencia educativa
    public function guardarEducacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_SESSION['applicant_id'])) {
                    throw new Exception("ID de postulante no establecido.");
                }

                $applicant_id = intval($_SESSION['applicant_id']);

                $data = [
                    'educacion_id' => isset($_POST['educacion_id']) ? limpiarCadena($_POST['educacion_id']) : null,
                    'applicant_id' => $applicant_id,
                    'institution' => limpiarCadena($_POST['institution']),
                    'education_type' => limpiarCadena($_POST['education_type']),
                    'start_date_education' => $_POST['start_date_education'],
                    'end_date_education' => $_POST['end_date_education'],
                    'duration_education' => $_POST['duration_education'],
                    'duration_unit_education' => $_POST['duration_unit_education']
                ];

                // Manejar la carga del archivo educativo
                $archivoEducacion = $this->manejarCargaArchivo('education', $applicant_id);
                if ($archivoEducacion !== null) {
                    $data['file_path'] = $archivoEducacion;
                } else {
                    // Si no se subió un nuevo archivo y es una actualización, mantener el file_path existente
                    if (!empty($data['educacion_id'])) {
                        $educacionesActuales = $this->experience->mostrarEducacion($applicant_id);
                        foreach ($educacionesActuales as $edu) {
                            if ($edu['id'] == $data['educacion_id']) {
                                $data['file_path'] = $edu['file_path'];
                                break;
                            }
                        }
                    } else {
                        $data['file_path'] = null;
                    }
                }

                $result = $this->experience->guardarEducacion($data);
                ob_clean(); // Limpiar cualquier salida previa
                if ($result) {
                    echo json_encode(['status' => true, 'message' => 'Experiencia educativa guardada correctamente.']);
                } else {
                    echo json_encode(['status' => false, 'message' => 'Error al guardar la experiencia educativa.']);
                }
            } catch (Exception $e) {
                ob_clean(); // Limpiar cualquier salida previa
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }

    // Guardar o actualizar experiencia laboral
    public function guardarTrabajo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_SESSION['applicant_id'])) {
                    throw new Exception("ID de postulante no establecido.");
                }

                $applicant_id = intval($_SESSION['applicant_id']);

                $data = [
                    'trabajo_id' => isset($_POST['trabajo_id']) ? limpiarCadena($_POST['trabajo_id']) : null,
                    'applicant_id' => $applicant_id,
                    'company' => limpiarCadena($_POST['company']),
                    'position' => limpiarCadena($_POST['position']),
                    'start_date_work' => $_POST['start_date_work'],
                    'end_date_work' => $_POST['end_date_work']
                ];

                // Manejar la carga del archivo laboral
                $archivoTrabajo = $this->manejarCargaArchivo('work', $applicant_id);
                if ($archivoTrabajo !== null) {
                    $data['file_path'] = $archivoTrabajo;
                } else {
                    // Si no se subió un nuevo archivo y es una actualización, mantener el file_path existente
                    if (!empty($data['trabajo_id'])) {
                        $trabajosActuales = $this->experience->mostrarTrabajo($applicant_id);
                        foreach ($trabajosActuales as $trab) {
                            if ($trab['id'] == $data['trabajo_id']) {
                                $data['file_path'] = $trab['file_path'];
                                break;
                            }
                        }
                    } else {
                        $data['file_path'] = null;
                    }
                }

                $result = $this->experience->guardarTrabajo($data);
                ob_clean(); // Limpiar cualquier salida previa
                if ($result) {
                    echo json_encode(['status' => true, 'message' => 'Experiencia laboral guardada correctamente.']);
                } else {
                    echo json_encode(['status' => false, 'message' => 'Error al guardar la experiencia laboral.']);
                }
            } catch (Exception $e) {
                ob_clean(); // Limpiar cualquier salida previa
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }

    // Mostrar experiencias educativas
    public function mostrarEducacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                if (!isset($_SESSION['applicant_id'])) {
                    throw new Exception("ID de postulante no establecido.");
                }

                $applicant_id = intval($_SESSION['applicant_id']);
                $educaciones = $this->experience->mostrarEducacion($applicant_id);
                ob_clean();
                echo json_encode($educaciones);
            } catch (Exception $e) {
                ob_clean();
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }

    // Mostrar experiencias laborales
    public function mostrarTrabajo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                if (!isset($_SESSION['applicant_id'])) {
                    throw new Exception("ID de postulante no establecido.");
                }

                $applicant_id = intval($_SESSION['applicant_id']);
                $trabajos = $this->experience->mostrarTrabajo($applicant_id);
                ob_clean();
                echo json_encode($trabajos);
            } catch (Exception $e) {
                ob_clean();
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }

    // Eliminar experiencia educativa
    public function eliminarEducacion()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                // Obtener el ID desde el cuerpo de la solicitud
                parse_str(file_get_contents("php://input"), $delete_vars);
                if (isset($delete_vars['id'])) {
                    $id = intval($delete_vars['id']);
                    $result = $this->experience->eliminarEducacion($id);
                    ob_clean();
                    if ($result) {
                        echo json_encode(['status' => true, 'message' => 'Experiencia educativa eliminada correctamente.']);
                    } else {
                        echo json_encode(['status' => false, 'message' => 'Error al eliminar la experiencia educativa.']);
                    }
                } else {
                    throw new Exception("ID no proporcionado.");
                }
            } catch (Exception $e) {
                ob_clean();
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }

    // Eliminar experiencia laboral
    public function eliminarTrabajo()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                // Obtener el ID desde el cuerpo de la solicitud
                parse_str(file_get_contents("php://input"), $delete_vars);
                if (isset($delete_vars['id'])) {
                    $id = intval($delete_vars['id']);
                    $result = $this->experience->eliminarTrabajo($id);
                    ob_clean();
                    if ($result) {
                        echo json_encode(['status' => true, 'message' => 'Experiencia laboral eliminada correctamente.']);
                    } else {
                        echo json_encode(['status' => false, 'message' => 'Error al eliminar la experiencia laboral.']);
                    }
                } else {
                    throw new Exception("ID no proporcionado.");
                }
            } catch (Exception $e) {
                ob_clean();
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
            exit();
        } else {
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Método no permitido.']);
            exit();
        }
    }
}

// Manejar las operaciones según el parámetro 'op'
if (isset($_GET['op'])) {
    $controller = new ExperienceController();
    switch ($_GET['op']) {
        case 'guardarEducacion':
            $controller->guardarEducacion();
            break;
        case 'guardarTrabajo':
            $controller->guardarTrabajo();
            break;
        case 'mostrarEducacion':
            $controller->mostrarEducacion();
            break;
        case 'mostrarTrabajo':
            $controller->mostrarTrabajo();
            break;
        case 'eliminarEducacion':
            $controller->eliminarEducacion();
            break;
        case 'eliminarTrabajo':
            $controller->eliminarTrabajo();
            break;
        default:
            ob_clean();
            echo json_encode(['status' => false, 'message' => 'Operación no válida.']);
            break;
    }
}

// Finalizar output buffering
ob_end_flush();
?>
