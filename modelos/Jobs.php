<?php
require_once '../config/Conexion.php';

class Jobs
{
    // Método para insertar un nuevo puesto de trabajo
    public function insertar($position_name, $area_id)
    {
        $position_name = limpiarCadena($position_name);
        $area_id = limpiarCadena($area_id);

        // Verificar si el puesto ya existe en el área
        $sql_verificar = "SELECT id FROM jobs WHERE position_name = ? AND area_id = ?";
        $params_verificar = [$position_name, $area_id];
        $rspta_verificar = ejecutarConsulta($sql_verificar, $params_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // Puesto ya existe
        }

        $sql = "INSERT INTO jobs (position_name, area_id) VALUES (?, ?)";
        $params = [$position_name, $area_id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para editar un puesto de trabajo existente
    public function editar($id, $position_name, $area_id)
    {
        $id = limpiarCadena($id);
        $position_name = limpiarCadena($position_name);
        $area_id = limpiarCadena($area_id);

        // Verificar si el puesto ya existe en el área
        $sql_verificar = "SELECT id FROM jobs WHERE position_name = ? AND area_id = ? AND id != ?";
        $params_verificar = [$position_name, $area_id, $id];
        $rspta_verificar = ejecutarConsulta($sql_verificar, $params_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // Puesto ya existe en otra entrada
        }

        $sql = "UPDATE jobs SET position_name = ?, area_id = ? WHERE id = ?";
        $params = [$position_name, $area_id, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para desactivar un puesto de trabajo
    public function desactivar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE jobs SET is_active = 0 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para activar un puesto de trabajo
    public function activar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE jobs SET is_active = 1 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para mostrar un puesto de trabajo específico
    public function mostrar($id)
    {
        $id = limpiarCadena($id);
        $sql = "SELECT j.*, a.area_name, c.company_name, a.company_id FROM jobs j 
                INNER JOIN areas a ON j.area_id = a.id 
                INNER JOIN companies c ON a.company_id = c.id 
                WHERE j.id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Método para listar todos los puestos de trabajo
    public function listar()
    {
        $sql = "SELECT j.*, a.area_name, c.company_name FROM jobs j 
                INNER JOIN areas a ON j.area_id = a.id 
                INNER JOIN companies c ON a.company_id = c.id 
                WHERE j.is_active = 1
                ORDER BY c.company_name ASC, j.position_name ASC";
        return ejecutarConsulta($sql);
    }

    // Método para obtener una lista de puestos de trabajo activos para un select
    public function select($company_id = null)
    {
        if ($company_id) {
            $company_id = limpiarCadena($company_id);
            $sql = "SELECT j.id, j.position_name FROM jobs j
                    INNER JOIN areas a ON j.area_id = a.id
                    WHERE j.is_active = 1 AND a.company_id = ?
                    ORDER BY j.position_name ASC";
            $params = [$company_id];
            return ejecutarConsulta($sql, $params);
        } else {
            $sql = "SELECT id, position_name FROM jobs WHERE is_active = 1 ORDER BY position_name ASC";
            return ejecutarConsulta($sql);
        }
    }

    // Método para verificar si un puesto de trabajo ya existe
    public function verificarPuesto($position_name, $area_id, $id = null)
    {
        $position_name = limpiarCadena($position_name);
        $area_id = limpiarCadena($area_id);
        if ($id) {
            $id = limpiarCadena($id);
            $sql = "SELECT id FROM jobs WHERE position_name = ? AND area_id = ? AND id != ?";
            $params = [$position_name, $area_id, $id];
        } else {
            $sql = "SELECT id FROM jobs WHERE position_name = ? AND area_id = ?";
            $params = [$position_name, $area_id];
        }
        $result = ejecutarConsulta($sql, $params);
        if ($result && $result->num_rows > 0) {
            return true; // Puesto ya existe
        }
        return false; // Puesto no existe
    }

    // Método para listar puestos de trabajo por Área
    public function listar_por_area($area_id)
    {
        $area_id = limpiarCadena($area_id);
        $sql = "SELECT id, position_name FROM jobs WHERE is_active = 1 AND area_id = ? ORDER BY position_name ASC";
        $params = [$area_id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para listar puestos de trabajo activos
    public function listarPuestosActivos()
    {
        $sql = "SELECT id, position_name FROM jobs WHERE is_active = 1 ORDER BY position_name ASC";
        return ejecutarConsulta($sql);
    }

    // Método para seleccionar puestos por empresa
    public function selectByCompany($company_id)
    {
        $sql = "SELECT j.id, j.position_name
                FROM jobs j
                INNER JOIN areas a ON j.area_id = a.id
                WHERE a.company_id = ? AND j.is_active = 1
                ORDER BY j.position_name ASC";
        $params = [$company_id];
        return ejecutarConsulta($sql, $params);
    }

    

    public function listarFiltrado($company_id, $area_id, $position_id)
    {
        $sql = "SELECT j.*, a.area_name, c.company_name FROM jobs j 
            INNER JOIN areas a ON j.area_id = a.id 
            INNER JOIN companies c ON a.company_id = c.id 
            WHERE 1=1";

        if (!empty($company_id)) {
            $sql .= " AND c.id = '$company_id'";
        }

        if (!empty($area_id)) {
            $sql .= " AND a.id = '$area_id'";
        }

        if (!empty($position_id)) {
            $sql .= " AND j.id = '$position_id'";
        }

        return ejecutarConsulta($sql);
    }

}
?>
