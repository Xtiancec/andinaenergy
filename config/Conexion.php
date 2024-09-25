<?php 
require_once "global.php";

$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');

// Muestra posible error en la conexión
if (mysqli_connect_errno()) {
    printf("Falló en la conexión con la base de datos: %s\n", mysqli_connect_error());
    exit();
}

if (!function_exists('ejecutarConsulta')) {

    function ejecutarConsulta($sql, $params = [])
    {
        global $conexion;
        
        // Preparar la consulta
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            echo "Error en la preparación de la consulta: " . $conexion->error;
            return false;
        }
        
        // Vincular los parámetros si existen
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Modificar según los tipos de los parámetros
            $stmt->bind_param($types, ...$params);  // Desempaquetar el array de parámetros
        }
    
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Para `SELECT`, devolver el resultado
            if (strpos($sql, 'SELECT') === 0) {
                return $stmt->get_result();
            }
            return true; // Para `UPDATE`, `DELETE` o `INSERT`, solo devolver éxito
        } else {
            echo "Error en la consulta: " . $stmt->error;
            return false;
        }
    }
    function ejecutarConsultaSimpleFila($sql, $params = [])
{
    global $conexion;  // Asegúrate de tener acceso a la conexión a la base de datos

    // Prepara la consulta
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        error_log("Error en la preparación de la consulta: " . $conexion->error);
        return null;
    }

    // Si hay parámetros, los ligamos a la consulta preparada
    if (!empty($params)) {
        // Determinar los tipos de datos
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        if (!$stmt->bind_param($types, ...$params)) {
            error_log("Error en bind_param: " . $stmt->error);
            return null;
        }
    }

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        error_log("Error en la ejecución de la consulta: " . $stmt->error);
        return null;
    }

    // Obtén el resultado
    $resultado = $stmt->get_result();
    if ($resultado === false) {
        error_log("Error al obtener el resultado: " . $stmt->error);
        return null;
    }

    // Verifica si se obtuvo una fila y devuélvela como un array asociativo
    if ($fila = $resultado->fetch_assoc()) {
        return $fila;
    } else {
        return null;  // Devuelve null si no hay resultados
    }
}

    
    function ejecutarConsulta_retornarID($sql)
    {
        global $conexion;
        $query = $conexion->query($sql);
        return $conexion->insert_id;
    }

    function limpiarCadena($str)
    {
        global $conexion;
        $str = mysqli_real_escape_string($conexion, trim($str));
        return htmlspecialchars($str);
    }

    function ejecutarConsultaArray($sql)
    {
        global $conexion;
        $query = $conexion->query($sql);
        
        // Verifica si la consulta fue exitosa
        if (!$query) {
            echo "Error en la consulta: " . $conexion->error;
            return false;
        }
        
        // Crea un arreglo para almacenar los resultados
        $resultArray = array();
        
        // Itera sobre cada fila del resultado y la agrega al arreglo
        while ($row = $query->fetch_assoc()) {
            $resultArray[] = $row;
        }
        
        // Retorna el arreglo con todas las filas
        return $resultArray;
    }

}
?>
