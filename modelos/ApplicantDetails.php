<?php
require_once "../config/Conexion.php";

class ApplicantDetails
{
    // Insertar datos personales del postulante
    public function insertar($applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level)
    {
        $sql = "INSERT INTO applicants_details (applicant_id, phone, emergency_contact_phone, contacto_emergencia, pais, departamento, provincia, direccion, gender, birth_date, marital_status, children_count, education_level) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level];
        return ejecutarConsulta($sql, $params);
    }

    // Actualizar datos personales del postulante
    public function actualizar($id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level)
    {
        $sql = "UPDATE applicants_details 
                SET phone = ?, emergency_contact_phone = ?, contacto_emergencia = ?, pais = ?, departamento = ?, provincia = ?, direccion = ?, gender = ?, birth_date = ?, marital_status = ?, children_count = ?, education_level = ? 
                WHERE id = ?";
        $params = [$phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar detalles personales de un postulante
    public function mostrar($applicant_id)
    {
        $sql = "SELECT * FROM applicants_details WHERE applicant_id = ?";
        $params = [$applicant_id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }
}
?>
