<?php
<<<<<<< HEAD
class ApplicantDetails
{
    // Insertar datos personales del postulante
    public function insertar($applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $photo)
    {
        // Manejo de la subida de la foto
        $photo_url = null;
        if ($photo && $photo['error'] == UPLOAD_ERR_OK) {
            // Validar el tipo de archivo
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (in_array($photo['type'], $allowed_types)) {
                // Generar un nombre único para el archivo
                $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
                $filename = 'applicant_' . $applicant_id . '_' . time() . '.' . $ext;
                $server_path = $_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/' . $filename; // Ruta física en el servidor
                $web_path = '/rh/uploads/applicant_photos/' . $filename; // Ruta accesible vía web

                // Asegurarse de que el directorio exista
                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/')) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/', 0755, true);
                }

                // Mover el archivo
                if (move_uploaded_file($photo['tmp_name'], $server_path)) {
                    $photo_url = $web_path;
                } else {
                    return "Error al subir la foto.";
                }
            } else {
                return "Tipo de archivo de foto no permitido. Solo se aceptan JPG, PNG y GIF.";
            }
        }

        // Insertar datos en la base de datos
        $sql = "INSERT INTO applicants_details (applicant_id, phone, emergency_contact_phone, contacto_emergencia, pais, departamento, provincia, direccion, gender, birth_date, marital_status, children_count, education_level, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $photo_url];
=======
require_once "../config/Conexion.php";

class ApplicantDetails
{
    // Insertar datos personales del postulante
    public function insertar($applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level)
    {
        $sql = "INSERT INTO applicants_details (applicant_id, phone, emergency_contact_phone, contacto_emergencia, pais, departamento, provincia, direccion, gender, birth_date, marital_status, children_count, education_level) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level];
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        return ejecutarConsulta($sql, $params);
    }

    // Actualizar datos personales del postulante
<<<<<<< HEAD
    public function actualizar($id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $photo)
=======
    public function actualizar($id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level)
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
    {
        // Obtener la ruta actual de la foto
        $sql = "SELECT photo FROM applicants_details WHERE id = ?";
        $params = [$id];
        $current = ejecutarConsultaSimpleFila($sql, $params);
        $current_photo = $current['photo'] ?? null;

        // Manejo de la subida de la foto
        $photo_url = $current_photo;
        if ($photo && $photo['error'] == UPLOAD_ERR_OK) {
            // Validar el tipo de archivo
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (in_array($photo['type'], $allowed_types)) {
                // Generar un nombre único para el archivo
                $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
                $filename = 'applicant_' . $id . '_' . time() . '.' . $ext;
                $server_path = $_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/' . $filename; // Ruta física en el servidor
                $web_path = '/rh/uploads/applicant_photos/' . $filename; // Ruta accesible vía web

                // Asegurarse de que el directorio exista
                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/')) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/rh/uploads/applicant_photos/', 0755, true);
                }

                // Mover el archivo
                if (move_uploaded_file($photo['tmp_name'], $server_path)) {
                    // Eliminar la foto anterior si existe
                    if ($current_photo && file_exists($_SERVER['DOCUMENT_ROOT'] . $current_photo)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $current_photo);
                    }
                    $photo_url = $web_path;
                } else {
                    return "Error al subir la foto.";
                }
            } else {
                return "Tipo de archivo de foto no permitido. Solo se aceptan JPG, PNG y GIF.";
            }
        }

        // Actualizar datos en la base de datos
        $sql = "UPDATE applicants_details 
<<<<<<< HEAD
                SET phone = ?, emergency_contact_phone = ?, contacto_emergencia = ?, pais = ?, departamento = ?, provincia = ?, direccion = ?, gender = ?, birth_date = ?, marital_status = ?, children_count = ?, education_level = ?, photo = ? 
                WHERE id = ?";
        $params = [$phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $photo_url, $id];
=======
                SET phone = ?, emergency_contact_phone = ?, contacto_emergencia = ?, pais = ?, departamento = ?, provincia = ?, direccion = ?, gender = ?, birth_date = ?, marital_status = ?, children_count = ?, education_level = ? 
                WHERE id = ?";
        $params = [$phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $id];
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar detalles personales de un postulante
    public function mostrar($applicant_id)
    {
<<<<<<< HEAD
        // Query to get applicant details and the job position name
        $sql = "SELECT ad.*, a.lastname, a.surname, a.names, j.position_name
                FROM applicants_details ad
                INNER JOIN applicants a ON ad.applicant_id = a.id
                INNER JOIN jobs j ON a.job_id = j.id
                WHERE ad.applicant_id = ?";
        
        $params = [$applicant_id];
        $applicant = ejecutarConsultaSimpleFila($sql, $params);
    
        if ($applicant) {
            // Concatenate full name
            $full_name = "{$applicant['names']} {$applicant['lastname']} {$applicant['surname']}";
            $applicant['nombre_completo'] = $full_name;  // Add full name to the response
    
            return $applicant;
        } else {
            return null;
        }
=======
        $sql = "SELECT * FROM applicants_details WHERE applicant_id = ?";
        $params = [$applicant_id];
        return ejecutarConsultaSimpleFila($sql, $params);
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
    }
    
}
?>
