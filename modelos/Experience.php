<?php
require_once "../config/Conexion.php";

class Experience
{
    // Insertar o actualizar experiencia educativa
    public function guardarEducacion($data)
    {
        if (isset($data['educacion_id']) && !empty($data['educacion_id'])) {
            // Actualizar experiencia educativa existente
            $sql = "UPDATE education_experience 
                    SET institution = ?, education_type = ?, start_date = ?, end_date = ?, duration = ?, duration_unit = ?, file_path = ?
                    WHERE id = ?";
            $params = [
                $data['institution'],
                $data['education_type'],
                $data['start_date_education'],
                $data['end_date_education'],
                $data['duration_education'],
                $data['duration_unit_education'],
                isset($data['file_path']) ? $data['file_path'] : null, // Manejar file_path opcionalmente
                $data['educacion_id']
            ];
            return ejecutarConsulta($sql, $params);
        } else {
            // Insertar nueva experiencia educativa
            $sql = "INSERT INTO education_experience (applicant_id, institution, education_type, start_date, end_date, duration, duration_unit, file_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $data['applicant_id'],
                $data['institution'],
                $data['education_type'],
                $data['start_date_education'],
                $data['end_date_education'],
                $data['duration_education'],
                $data['duration_unit_education'],
                isset($data['file_path']) ? $data['file_path'] : null // Manejar file_path opcionalmente
            ];
            return ejecutarConsulta($sql, $params);
        }
    }

    // Insertar o actualizar experiencia laboral
    public function guardarTrabajo($data)
    {
        if (isset($data['trabajo_id']) && !empty($data['trabajo_id'])) {
            // Actualizar experiencia laboral existente
            $sql = "UPDATE work_experience 
                    SET company = ?, position = ?, start_date = ?, end_date = ?, file_path = ?
                    WHERE id = ?";
            $params = [
                $data['company'],
                $data['position'],
                $data['start_date_work'],
                $data['end_date_work'],
                isset($data['file_path']) ? $data['file_path'] : null, // Manejar file_path opcionalmente
                $data['trabajo_id']
            ];
            return ejecutarConsulta($sql, $params);
        } else {
            // Insertar nueva experiencia laboral
            $sql = "INSERT INTO work_experience (applicant_id, company, position, start_date, end_date, file_path) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [
                $data['applicant_id'],
                $data['company'],
                $data['position'],
                $data['start_date_work'],
                $data['end_date_work'],
                isset($data['file_path']) ? $data['file_path'] : null // Manejar file_path opcionalmente
            ];
            return ejecutarConsulta($sql, $params);
        }
    }

    // Mostrar experiencias educativas
    public function mostrarEducacion($applicant_id)
    {
        $applicant_id = intval($applicant_id);
        $sql = "SELECT * FROM education_experience WHERE applicant_id = $applicant_id";
        $result = ejecutarConsulta($sql);

        if ($result !== false && $result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else {
            return []; // Retorna un array vacío si no hay resultados
        }
    }

    // Mostrar experiencias laborales
    public function mostrarTrabajo($applicant_id)
    {
        $applicant_id = intval($applicant_id);
        $sql = "SELECT * FROM work_experience WHERE applicant_id = $applicant_id";
        $result = ejecutarConsulta($sql);

        if ($result !== false && $result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else {
            return []; // Retorna un array vacío si no hay resultados
        }
    }

    // Eliminar experiencia educativa
    public function eliminarEducacion($id)
    {
        $id = intval($id);
        $sql = "DELETE FROM education_experience WHERE id = $id";
        return ejecutarConsulta($sql);
    }

    // Eliminar experiencia laboral
    public function eliminarTrabajo($id)
    {
        $id = intval($id);
        $sql = "DELETE FROM work_experience WHERE id = $id";
        return ejecutarConsulta($sql);
    }
}
?>
