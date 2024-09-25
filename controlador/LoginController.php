<?php
// Archivo: ../controlador/LoginController.php

require_once "../config/Conexion.php";
require_once "../modelos/User.php";

class LoginController
{
    public function verificar()
    {
        header('Content-Type: application/json');

        try {
            $user = new User();

            // Obtener y limpiar entradas
            $username = isset($_POST['username']) ? limpiarCadena($_POST['username']) : "";
            $password = isset($_POST['password']) ? trim($_POST['password']) : ""; // Solo trim

            // Validar que los campos no estén vacíos
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Por favor, rellena todos los campos.']);
                return;
            }

            $result = $user->autenticar($username, $password);

            if ($result) {
                session_start();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['user_role'] = $result['role'];
                $_SESSION['user_type'] = 'user';
                $_SESSION['full_name'] = $result['names'] . ' ' . $result['surname'] . ' ' . $result['lastname'];

                // Registrar el login
                $user->registrarLogin($result['id']);

                $response = [
                    'success' => true,
                    'role' => $result['role'],
                    'type' => 'user'
                ];
            } else {
                error_log("Intento de login fallido para usuario: $username");
                $response = ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Error interno del servidor. Por favor, inténtalo de nuevo.'
            ];
        }

        echo json_encode($response);
        exit();
    }
}

// Manejador de las solicitudes AJAX
if (isset($_GET['op'])) {
    $controller = new LoginController();
    switch ($_GET['op']) {
        case 'verificar':
            $controller->verificar();
            break;
    }
}
?>
