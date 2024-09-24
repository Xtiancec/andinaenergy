<?php

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User
{
    // Función para listar todos los usuarios
    public function listar()
    {
        $sql = "SELECT u.id, c.company_name, a.area_name, j.position_name, u.username,
                       u.lastname, u.surname, u.names, 
                       u.email, u.role, u.is_active
                FROM users u
                INNER JOIN companies c ON u.company_id = c.id
                INNER JOIN areas a ON u.area_id = a.id
                INNER JOIN jobs j ON u.job_id = j.id";
        return ejecutarConsulta($sql);
    }

    // Función para insertar un nuevo usuario
    public function insertar($company_id, $area_id, $identification_type, $username, $email, $lastname, $surname, $names, $nacionality, $role, $job_id, $is_employee)
    {
        // Verificar si el nombre de usuario ya existe
        $sql_verificar = "SELECT id FROM users WHERE username = ?";
        $params_verificar = [$username];
        $result_verificar = ejecutarConsultaSimpleFila($sql_verificar, $params_verificar);
    
        if ($result_verificar) {
            return "El nombre de usuario ya está registrado.";
        }
    
        // Generar contraseña aleatoria
        $password = bin2hex(random_bytes(4)); // Generar una contraseña aleatoria de 8 caracteres
        $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña
    
        // Insertar el nuevo usuario en la base de datos
        $sql_insertar = "INSERT INTO users (company_id, area_id, identification_type, username, password, email, lastname, surname, names, nacionality, role, job_id, is_employee)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params_insertar = [$company_id, $area_id, $identification_type, $username, $password_hashed, $email, $lastname, $surname, $names, $nacionality, $role, $job_id, $is_employee];
        $result_insertar = ejecutarConsulta($sql_insertar, $params_insertar);
    
        if ($result_insertar) {
            // Enviar el correo electrónico con las credenciales
            if (!$this->enviarCorreo($email, $names, $username, $password)) {
                return "Usuario creado, pero no se pudo enviar el correo.";
            }
            return "Usuario registrado correctamente y correo enviado.";
        }
    
        return "Error al registrar el usuario.";
    }
    
    // Función para editar un usuario existente
    public function editar($id, $company_id, $area_id, $identification_type, $username, $email, $lastname, $surname, $names, $nacionality, $role, $job_id, $is_employee)
    {
        // Verificar si el job_id existe en la tabla jobs
        $sql_verificar = "SELECT id FROM jobs WHERE id = ?";
        $params_verificar = [$job_id];
        $result_verificar = ejecutarConsultaSimpleFila($sql_verificar, $params_verificar);

        if (!$result_verificar) {
            return "El puesto de trabajo seleccionado no es válido.";
        }

        // Verificar si el username es único (exceptuando el usuario actual)
        $sql_verificar_username = "SELECT id FROM users WHERE username = ? AND id != ?";
        $params_verificar_username = [$username, $id];
        $result_verificar_username = ejecutarConsultaSimpleFila($sql_verificar_username, $params_verificar_username);

        if ($result_verificar_username) {
            return "El nombre de usuario ya está registrado por otro usuario.";
        }

        // Actualizar el usuario en la base de datos
        $sql_editar = "UPDATE users SET company_id = ?, area_id = ?, identification_type = ?, username = ?, email = ?, lastname = ?, surname = ?, names = ?, nacionality = ?, role = ?, job_id = ?, is_employee = ? WHERE id = ?";
        $params_editar = [$company_id, $area_id, $identification_type, $username, $email, $lastname, $surname, $names, $nacionality, $role, $job_id, $is_employee, $id];

        return ejecutarConsulta($sql_editar, $params_editar) ? "Usuario actualizado correctamente." : "Error al actualizar el usuario.";
    }

    // Función para mostrar un usuario específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Función para activar un usuario
    public function activar($id)
    {
        $sql = "UPDATE users SET is_active = 1 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params) ? "Usuario activado correctamente." : "No se pudo activar el usuario.";
    }

    // Función para desactivar un usuario
    public function desactivar($id)
    {
        $sql = "UPDATE users SET is_active = 0 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params) ? "Usuario desactivado correctamente." : "No se pudo desactivar el usuario.";
    }

    // Función para enviar un correo con las credenciales del usuario
    private function enviarCorreo($email,$names, $username, $password)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'mail.exclusivehosting.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'cristhian.espino@andinaenergy.com';
            $mail->Password = 'AndinaEn@BP1zAPx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Configuración del correo
            $mail->setFrom('cristhian.espino@andinaenergy.com', 'Andina Energy');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Creación de Cuenta - Credenciales';
            $mail->CharSet = 'UTF-8';
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #007bff;'>¡Bienvenido a Andina Energy, $names!</h2>
                <p style='font-size: 14px;'>Nos complace informarte que tu cuenta ha sido creada con éxito.</p>
                <p style='font-size: 16px;'><strong>Usuario:</strong> $username</p>
                <p style='font-size: 16px;'><strong>Contraseña:</strong> $password</p>
                <p style='font-size: 12px; color: #555;'>Por favor, cambia tu contraseña la primera vez que inicies sesión para asegurar tu cuenta.</p>
                <div style='margin-top: 20px;'>
                    <a href='https://andinaenergy.com/' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Iniciar sesión</a>
                </div>
                <p style='font-size: 12px; color: #888; margin-top: 20px;'>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
                <p style='font-size: 12px; color: #888;'>Gracias,<br>Equipo de Andina Energy</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Error al enviar mensaje: {$mail->ErrorInfo}";
            return false;
        }
    }

    // Función para listar Áreas por Empresa
    public function listarAreasPorEmpresa($company_id)
    {
        $sql = "SELECT id, area_name FROM areas WHERE company_id = ?";
        $params = [$company_id];
        return ejecutarConsulta($sql, $params);
    }

    // Función para listar Puestos por Área
    public function listarPuestosPorArea($area_id)
    {
        $sql = "SELECT id, position_name FROM jobs WHERE area_id = ?";
        $params = [$area_id];
        return ejecutarConsulta($sql, $params);
    }

    // Función para listar todas las empresas
    public function listarEmpresas()
    {
        $sql = "SELECT id, company_name FROM companies";
        return ejecutarConsulta($sql);
    }

    // Función para listar todos los puestos de trabajo activos
    public function listarPuestosActivos()
    {
        $sql = "SELECT id, position_name FROM jobs WHERE is_active = 1";
        return ejecutarConsulta($sql);
    }

    // Función para obtener el historial de accesos de un usuario
    public function obtenerHistorialAcceso($userId)
    {
        $sql = "SELECT access_time, logout_time FROM user_access_logs WHERE user_id = ? ORDER BY access_time DESC";
        $params = [$userId];
        return ejecutarConsulta($sql, $params);
    }

    // Función para cambiar la contraseña de un usuario
    public function cambiarPassword($id, $newPassword)
    {
        // Hash de la contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $params = [$hashedPassword, $id];
        return ejecutarConsulta($sql, $params) ? "Contraseña actualizada correctamente." : "No se pudo actualizar la contraseña.";
    }

    // Función para verificar duplicados de username
    public function verificarDuplicadoUsername($username, $userId = null)
    {
        if ($userId) {
            // Ignorar al usuario que está siendo editado
            $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
            $params = [$username, $userId];
        } else {
            // Verificar duplicado para nuevos usuarios
            $sql = "SELECT id FROM users WHERE username = ?";
            $params = [$username];
        }

        return ejecutarConsultaSimpleFila($sql, $params) ? true : false;
    }
}
