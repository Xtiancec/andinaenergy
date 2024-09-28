<?php
require_once "../config/Conexion.php"; // Asegúrate de ajustar la ruta según tu estructura de directorios
require_once "../modelos/ApplicantDetails.php";

session_start(); // Asegurarse de que la sesión esté iniciada

class ApplicantDetailsController
{
    public function guardar()
    {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        // Sanitizar y validar entradas
        $applicant_id = $_SESSION['applicant_id'];
        $phone = isset($_POST["phone"]) ? limpiarCadena($_POST["phone"]) : "";
        $emergency_contact_phone = isset($_POST["emergency_contact_phone"]) ? limpiarCadena($_POST["emergency_contact_phone"]) : "";
        $contacto_emergencia = isset($_POST["contacto_emergencia"]) ? limpiarCadena($_POST["contacto_emergencia"]) : "";
        $pais = isset($_POST["pais"]) ? limpiarCadena($_POST["pais"]) : "";
        $departamento = isset($_POST["departamento"]) ? limpiarCadena($_POST["departamento"]) : "";
        $provincia = isset($_POST["provincia"]) ? limpiarCadena($_POST["provincia"]) : "";
        $direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
        $gender = isset($_POST["gender"]) ? limpiarCadena($_POST["gender"]) : "";
        $birth_date = isset($_POST["birth_date"]) ? limpiarCadena($_POST["birth_date"]) : "";
        $marital_status = isset($_POST["marital_status"]) ? limpiarCadena($_POST["marital_status"]) : "";
        $children_count = isset($_POST["children_count"]) ? limpiarCadena($_POST["children_count"]) : "";
        $education_level = isset($_POST["education_level"]) ? limpiarCadena($_POST["education_level"]) : "";
<<<<<<< HEAD
        $photo = isset($_FILES["photo"]) ? $_FILES["photo"] : null;
    
        // Validaciones
        $errors = [];
    
=======

        // Validaciones
        $errors = [];

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($phone)) {
            $errors[] = "El teléfono es obligatorio.";
        } elseif (!preg_match('/^\d{7,15}$/', $phone)) {
            $errors[] = "El teléfono debe contener entre 7 y 15 dígitos.";
        }
<<<<<<< HEAD
    
        if (!empty($emergency_contact_phone) && !preg_match('/^\d{7,15}$/', $emergency_contact_phone)) {
            $errors[] = "El teléfono de emergencia debe contener entre 7 y 15 dígitos.";
        }
    
=======

        if (!empty($emergency_contact_phone) && !preg_match('/^\d{7,15}$/', $emergency_contact_phone)) {
            $errors[] = "El teléfono de emergencia debe contener entre 7 y 15 dígitos.";
        }

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($gender)) {
            $errors[] = "El género es obligatorio.";
        } elseif (!in_array($gender, ['Masculino', 'Femenino', 'Otro'])) {
            $errors[] = "Género inválido.";
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($birth_date)) {
            $errors[] = "La fecha de nacimiento es obligatoria.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
            $errors[] = "La fecha de nacimiento no es válida.";
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($marital_status)) {
            $errors[] = "El estado civil es obligatorio.";
        } elseif (!in_array($marital_status, ['Soltero', 'Casado', 'Divorciado', 'Viudo'])) {
            $errors[] = "Estado civil inválido.";
        }
<<<<<<< HEAD
    
        if (empty($education_level)) {
            $errors[] = "El nivel educativo es obligatorio.";
        }
    
        if (empty($pais)) {
            $errors[] = "El país es obligatorio.";
        }
    
        if (empty($departamento)) {
            $errors[] = "El departamento es obligatorio.";
        }
    
        if (empty($provincia)) {
            $errors[] = "La provincia es obligatoria.";
        }
    
        if (empty($direccion)) {
            $errors[] = "La dirección es obligatoria.";
        }
    
        // Validar la foto si se proporciona
        if ($photo && $photo['error'] != UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if ($photo['error'] != UPLOAD_ERR_OK) {
                $errors[] = "Error al subir la foto.";
            } elseif (!in_array($photo['type'], $allowed_types)) {
                $errors[] = "Tipo de archivo de foto no permitido. Solo se aceptan JPG, PNG y GIF.";
            }
        }
    
=======

        if (empty($education_level)) {
            $errors[] = "El nivel educativo es obligatorio.";
        }

        if (empty($pais)) {
            $errors[] = "El país es obligatorio.";
        }

        if (empty($departamento)) {
            $errors[] = "El departamento es obligatorio.";
        }

        if (empty($provincia)) {
            $errors[] = "La provincia es obligatoria.";
        }

        if (empty($direccion)) {
            $errors[] = "La dirección es obligatoria.";
        }

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (!empty($errors)) {
            echo json_encode(['status' => false, 'message' => implode(" ", $errors)]);
            return;
        }
<<<<<<< HEAD
    
        // Crear instancia y guardar
        $applicantDetails = new ApplicantDetails();
        $result = $applicantDetails->insertar(
            $applicant_id,
            $phone,
            $emergency_contact_phone,
            $contacto_emergencia,
            $pais,
            $departamento,
            $provincia,
            $direccion,
            $gender,
            $birth_date,
            $marital_status,
            $children_count,
            $education_level,
            $photo
        );
    
=======

        // Crear instancia y guardar
        $applicantDetails = new ApplicantDetails();
        $result = $applicantDetails->insertar($applicant_id, $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level);

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        echo json_encode([
            'status' => $result ? true : false,
            'message' => $result ? "Datos guardados correctamente." : "Error al guardar los datos."
        ]);
    }

    public function actualizar()
    {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        // Sanitizar y validar entradas
        $applicant_id = $_SESSION['applicant_id'];
        $phone = isset($_POST["phoneUpdate"]) ? limpiarCadena($_POST["phoneUpdate"]) : "";
        $emergency_contact_phone = isset($_POST["emergency_contact_phoneUpdate"]) ? limpiarCadena($_POST["emergency_contact_phoneUpdate"]) : "";
        $contacto_emergencia = isset($_POST["contacto_emergenciaUpdate"]) ? limpiarCadena($_POST["contacto_emergenciaUpdate"]) : "";
        $pais = isset($_POST["paisUpdate"]) ? limpiarCadena($_POST["paisUpdate"]) : "";
        $departamento = isset($_POST["departamentoUpdate"]) ? limpiarCadena($_POST["departamentoUpdate"]) : "";
        $provincia = isset($_POST["provinciaUpdate"]) ? limpiarCadena($_POST["provinciaUpdate"]) : "";
        $direccion = isset($_POST["direccionUpdate"]) ? limpiarCadena($_POST["direccionUpdate"]) : "";
        $gender = isset($_POST["genderUpdate"]) ? limpiarCadena($_POST["genderUpdate"]) : "";
        $birth_date = isset($_POST["birth_dateUpdate"]) ? limpiarCadena($_POST["birth_dateUpdate"]) : "";
        $marital_status = isset($_POST["marital_statusUpdate"]) ? limpiarCadena($_POST["marital_statusUpdate"]) : "";
        $children_count = isset($_POST["children_countUpdate"]) ? limpiarCadena($_POST["children_countUpdate"]) : "";
        $education_level = isset($_POST["education_levelUpdate"]) ? limpiarCadena($_POST["education_levelUpdate"]) : "";
<<<<<<< HEAD
        $photo = isset($_FILES["photoUpdate"]) ? $_FILES["photoUpdate"] : null;
    
        // Validaciones
        $errors = [];
    
=======

        // Validaciones
        $errors = [];

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($phone)) {
            $errors[] = "El teléfono es obligatorio.";
        } elseif (!preg_match('/^\d{7,15}$/', $phone)) {
            $errors[] = "El teléfono debe contener entre 7 y 15 dígitos.";
        }
<<<<<<< HEAD
    
        if (!empty($emergency_contact_phone) && !preg_match('/^\d{7,15}$/', $emergency_contact_phone)) {
            $errors[] = "El teléfono de emergencia debe contener entre 7 y 15 dígitos.";
        }
    
=======

        if (!empty($emergency_contact_phone) && !preg_match('/^\d{7,15}$/', $emergency_contact_phone)) {
            $errors[] = "El teléfono de emergencia debe contener entre 7 y 15 dígitos.";
        }

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($gender)) {
            $errors[] = "El género es obligatorio.";
        } elseif (!in_array($gender, ['Masculino', 'Femenino', 'Otro'])) {
            $errors[] = "Género inválido.";
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($birth_date)) {
            $errors[] = "La fecha de nacimiento es obligatoria.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
            $errors[] = "La fecha de nacimiento no es válida.";
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (empty($marital_status)) {
            $errors[] = "El estado civil es obligatorio.";
        } elseif (!in_array($marital_status, ['Soltero', 'Casado', 'Divorciado', 'Viudo'])) {
            $errors[] = "Estado civil inválido.";
        }
<<<<<<< HEAD
    
        if (empty($education_level)) {
            $errors[] = "El nivel educativo es obligatorio.";
        }
    
        if (empty($pais)) {
            $errors[] = "El país es obligatorio.";
        }
    
        if (empty($departamento)) {
            $errors[] = "El departamento es obligatorio.";
        }
    
        if (empty($provincia)) {
            $errors[] = "La provincia es obligatoria.";
        }
    
        if (empty($direccion)) {
            $errors[] = "La dirección es obligatoria.";
        }
    
        // Validar la foto si se proporciona
        if ($photo && $photo['error'] != UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if ($photo['error'] != UPLOAD_ERR_OK) {
                $errors[] = "Error al subir la foto.";
            } elseif (!in_array($photo['type'], $allowed_types)) {
                $errors[] = "Tipo de archivo de foto no permitido. Solo se aceptan JPG, PNG y GIF.";
            }
        }
    
=======

        if (empty($education_level)) {
            $errors[] = "El nivel educativo es obligatorio.";
        }

        if (empty($pais)) {
            $errors[] = "El país es obligatorio.";
        }

        if (empty($departamento)) {
            $errors[] = "El departamento es obligatorio.";
        }

        if (empty($provincia)) {
            $errors[] = "La provincia es obligatoria.";
        }

        if (empty($direccion)) {
            $errors[] = "La dirección es obligatoria.";
        }

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        if (!empty($errors)) {
            echo json_encode(['status' => false, 'message' => implode(" ", $errors)]);
            return;
        }
<<<<<<< HEAD
    
=======

>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
        // Crear instancia y verificar existencia
        $applicantDetails = new ApplicantDetails();
        $existingDetails = $applicantDetails->mostrar($applicant_id);
    
        if ($existingDetails) {
<<<<<<< HEAD
            $result = $applicantDetails->actualizar(
                $existingDetails['id'],
                $phone,
                $emergency_contact_phone,
                $contacto_emergencia,
                $pais,
                $departamento,
                $provincia,
                $direccion,
                $gender,
                $birth_date,
                $marital_status,
                $children_count,
                $education_level,
                $photo
            );
=======
            $result = $applicantDetails->actualizar($existingDetails['id'], $phone, $emergency_contact_phone, $contacto_emergencia, $pais, $departamento, $provincia, $direccion, $gender, $birth_date, $marital_status, $children_count, $education_level);
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733
            echo json_encode([
                'status' => $result ? true : false,
                'message' => $result ? "Datos actualizados correctamente." : "Error al actualizar los datos."
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => "No se encontraron datos para actualizar."
            ]);
        }
    }

    public function mostrar()
    {
        if (!isset($_SESSION['applicant_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $applicant_id = $_SESSION['applicant_id'];
        $applicantDetails = new ApplicantDetails();
        $result = $applicantDetails->mostrar($applicant_id);

        if ($result) {
            echo json_encode([
                'status' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => "No se encontraron datos registrados."
            ]);
        }
    }
<<<<<<< HEAD

    public function obtenerDepartamentos()
    {
        // Lista de departamentos de Perú
        $departamentos = [
            "Amazonas",
            "Áncash",
            "Apurímac",
            "Arequipa",
            "Ayacucho",
            "Cajamarca",
            "Callao",
            "Cusco",
            "Huancavelica",
            "Huánuco",
            "Ica",
            "Junín",
            "La Libertad",
            "Lambayeque",
            "Lima",
            "Loreto",
            "Madre de Dios",
            "Moquegua",
            "Pasco",
            "Piura",
            "Puno",
            "San Martín",
            "Tacna",
            "Tumbes",
            "Ucayali"
        ];

        echo json_encode(['departamentos' => $departamentos]);
    }

    // Método para obtener provincias basadas en el departamento
    public function obtenerProvincias()
    {
        if (!isset($_GET['departamento'])) {
            echo json_encode(['provincias' => []]);
            return;
        }
    
        $departamento = $_GET['departamento'];
    
        // Lista completa de provincias por departamento (ejemplo parcial)
        $provinciasPorDepartamento = [
            "Amazonas" => ["Chachapoyas", "Bagua", "Bongará", "Condorcanqui", "Luya", "Rodríguez de Mendoza", "Utcubamba"],
            "Áncash" => ["Huaraz", "Aija", "Antonio Raymondi", "Asunción", "Bolognesi", "Carhuaz", "Carlos Fermín Fitzcarrald", "Casma", "Corongo", "Huari", "Huarmey", "Huaylas", "Mariscal Luzuriaga", "Ocros", "Pallasca", "Pomabamba", "Recuay", "Santa", "Sihuas", "Yungay"],
            "Apurímac" => ["Abancay", "Andahuaylas", "Antabamba", "Aymaraes", "Cotabambas", "Chincheros"],
            "Arequipa" => ["Arequipa", "Camaná", "Caravelí", "Castilla", "Caylloma", "Condesuyos", "Islay", "La Unión"],
            "Ayacucho" => ["Huamanga", "Cangallo", "Huanca Sancos", "Huanta", "La Mar", "Lucanas", "Parinacochas", "Páucar del Sara Sara", "Sucre", "Víctor Fajardo", "Vilcas Huamán"],
            "Cajamarca" => ["Cajamarca", "Cajabamba", "Celendín", "Chota", "Contumazá", "Cutervo", "Hualgayoc", "Jaén", "San Ignacio", "San Marcos", "San Miguel", "San Pablo", "Santa Cruz"],
            "Callao" => ["Callao"],
            "Cusco" => ["Cusco", "Acomayo", "Anta", "Calca", "Canas", "Canchis", "Chumbivilcas", "Espinar", "La Convención", "Paruro", "Paucartambo", "Quispicanchi", "Urubamba"],
            "Huancavelica" => ["Huancavelica", "Acobamba", "Angaraes", "Castrovirreyna", "Churcampa", "Huaytará", "Tayacaja"],
            "Huánuco" => ["Huánuco", "Ambo", "Dos de Mayo", "Huacaybamba", "Huamalíes", "Leoncio Prado", "Marañón", "Pachitea", "Puerto Inca", "Lauricocha", "Yarowilca"],
            "Ica" => ["Ica", "Chincha", "Nazca", "Palpa", "Pisco"],
            "Junín" => ["Huancayo", "Concepción", "Chanchamayo", "Jauja", "Junín", "Satipo", "Tarma", "Yauli"],
            "La Libertad" => ["Trujillo", "Ascope", "Bolívar", "Chepén", "Gran Chimú", "Julcán", "Otuzco", "Pacasmayo", "Pataz", "Sánchez Carrión", "Santiago de Chuco", "Virú"],
            "Lambayeque" => ["Chiclayo", "Ferreñafe", "Lambayeque"],
            "Lima" => ["Lima", "Barranca", "Cajatambo", "Canta", "Cañete", "Huaral", "Huarochirí", "Huaura", "Oyón", "Yauyos"],
            "Loreto" => ["Maynas", "Alto Amazonas", "Loreto", "Mariscal Ramón Castilla", "Putumayo", "Requena", "Ucayali"],
            "Madre de Dios" => ["Tambopata", "Manu", "Tahuamanu"],
            "Moquegua" => ["Mariscal Nieto", "General Sánchez Cerro", "Ilo"],
            "Pasco" => ["Pasco", "Daniel Alcides Carrión", "Oxapampa"],
            "Piura" => ["Piura", "Ayabaca", "Huancabamba", "Morropón", "Paita", "Sullana", "Talara", "Sechura"],
            "Puno" => ["Puno", "Azángaro", "Carabaya", "Chucuito", "El Collao", "Huancané", "Lampa", "Melgar", "Moho", "San Antonio de Putina", "San Román", "Sandia", "Yunguyo"],
            "San Martín" => ["Moyobamba", "Bellavista", "El Dorado", "Huallaga", "Lamas", "Mariscal Cáceres", "Picota", "Rioja", "San Martín", "Tocache"],
            "Tacna" => ["Tacna", "Candarave", "Jorge Basadre", "Tarata"],
            "Tumbes" => ["Tumbes", "Contralmirante Villar", "Tumbes"],
            "Ucayali" => ["Coronel Portillo", "Atalaya", "Padre Abad", "Purús"]
        ];
    
        $provincias = isset($provinciasPorDepartamento[$departamento]) ? $provinciasPorDepartamento[$departamento] : [];
    
        echo json_encode(['provincias' => $provincias]);
    }
    
    // Método para obtener distritos basados en la provincia
    // Se elimina este método ya que ahora utilizaremos 'direccion' en lugar de 'distrito'
    // Por lo tanto, no es necesario cargar distritos
}
=======
>>>>>>> 19cd7fe849e27466995491cd45aa87e6231fe733

    public function obtenerDepartamentos()
    {
        // Lista de departamentos de Perú
        $departamentos = [
            "Amazonas",
            "Áncash",
            "Apurímac",
            "Arequipa",
            "Ayacucho",
            "Cajamarca",
            "Callao",
            "Cusco",
            "Huancavelica",
            "Huánuco",
            "Ica",
            "Junín",
            "La Libertad",
            "Lambayeque",
            "Lima",
            "Loreto",
            "Madre de Dios",
            "Moquegua",
            "Pasco",
            "Piura",
            "Puno",
            "San Martín",
            "Tacna",
            "Tumbes",
            "Ucayali"
        ];

        echo json_encode(['departamentos' => $departamentos]);
    }

    // Método para obtener provincias basadas en el departamento
    public function obtenerProvincias()
    {
        if (!isset($_GET['departamento'])) {
            echo json_encode(['provincias' => []]);
            return;
        }
    
        $departamento = $_GET['departamento'];
    
        // Lista completa de provincias por departamento (ejemplo parcial)
        $provinciasPorDepartamento = [
            "Amazonas" => ["Chachapoyas", "Bagua", "Bongará", "Condorcanqui", "Luya", "Rodríguez de Mendoza", "Utcubamba"],
            "Áncash" => ["Huaraz", "Aija", "Antonio Raymondi", "Asunción", "Bolognesi", "Carhuaz", "Carlos Fermín Fitzcarrald", "Casma", "Corongo", "Huari", "Huarmey", "Huaylas", "Mariscal Luzuriaga", "Ocros", "Pallasca", "Pomabamba", "Recuay", "Santa", "Sihuas", "Yungay"],
            "Apurímac" => ["Abancay", "Andahuaylas", "Antabamba", "Aymaraes", "Cotabambas", "Chincheros"],
            "Arequipa" => ["Arequipa", "Camaná", "Caravelí", "Castilla", "Caylloma", "Condesuyos", "Islay", "La Unión"],
            "Ayacucho" => ["Huamanga", "Cangallo", "Huanca Sancos", "Huanta", "La Mar", "Lucanas", "Parinacochas", "Páucar del Sara Sara", "Sucre", "Víctor Fajardo", "Vilcas Huamán"],
            "Cajamarca" => ["Cajamarca", "Cajabamba", "Celendín", "Chota", "Contumazá", "Cutervo", "Hualgayoc", "Jaén", "San Ignacio", "San Marcos", "San Miguel", "San Pablo", "Santa Cruz"],
            "Callao" => ["Callao"],
            "Cusco" => ["Cusco", "Acomayo", "Anta", "Calca", "Canas", "Canchis", "Chumbivilcas", "Espinar", "La Convención", "Paruro", "Paucartambo", "Quispicanchi", "Urubamba"],
            "Huancavelica" => ["Huancavelica", "Acobamba", "Angaraes", "Castrovirreyna", "Churcampa", "Huaytará", "Tayacaja"],
            "Huánuco" => ["Huánuco", "Ambo", "Dos de Mayo", "Huacaybamba", "Huamalíes", "Leoncio Prado", "Marañón", "Pachitea", "Puerto Inca", "Lauricocha", "Yarowilca"],
            "Ica" => ["Ica", "Chincha", "Nazca", "Palpa", "Pisco"],
            "Junín" => ["Huancayo", "Concepción", "Chanchamayo", "Jauja", "Junín", "Satipo", "Tarma", "Yauli"],
            "La Libertad" => ["Trujillo", "Ascope", "Bolívar", "Chepén", "Gran Chimú", "Julcán", "Otuzco", "Pacasmayo", "Pataz", "Sánchez Carrión", "Santiago de Chuco", "Virú"],
            "Lambayeque" => ["Chiclayo", "Ferreñafe", "Lambayeque"],
            "Lima" => ["Lima", "Barranca", "Cajatambo", "Canta", "Cañete", "Huaral", "Huarochirí", "Huaura", "Oyón", "Yauyos"],
            "Loreto" => ["Maynas", "Alto Amazonas", "Loreto", "Mariscal Ramón Castilla", "Putumayo", "Requena", "Ucayali"],
            "Madre de Dios" => ["Tambopata", "Manu", "Tahuamanu"],
            "Moquegua" => ["Mariscal Nieto", "General Sánchez Cerro", "Ilo"],
            "Pasco" => ["Pasco", "Daniel Alcides Carrión", "Oxapampa"],
            "Piura" => ["Piura", "Ayabaca", "Huancabamba", "Morropón", "Paita", "Sullana", "Talara", "Sechura"],
            "Puno" => ["Puno", "Azángaro", "Carabaya", "Chucuito", "El Collao", "Huancané", "Lampa", "Melgar", "Moho", "San Antonio de Putina", "San Román", "Sandia", "Yunguyo"],
            "San Martín" => ["Moyobamba", "Bellavista", "El Dorado", "Huallaga", "Lamas", "Mariscal Cáceres", "Picota", "Rioja", "San Martín", "Tocache"],
            "Tacna" => ["Tacna", "Candarave", "Jorge Basadre", "Tarata"],
            "Tumbes" => ["Tumbes", "Contralmirante Villar", "Tumbes"],
            "Ucayali" => ["Coronel Portillo", "Atalaya", "Padre Abad", "Purús"]
        ];
    
        $provincias = isset($provinciasPorDepartamento[$departamento]) ? $provinciasPorDepartamento[$departamento] : [];
    
        echo json_encode(['provincias' => $provincias]);
    }
    
    // Método para obtener distritos basados en la provincia
    // Se elimina este método ya que ahora utilizaremos 'direccion' en lugar de 'distrito'
    // Por lo tanto, no es necesario cargar distritos
}
    
if (isset($_GET["op"])) {
    $controller = new ApplicantDetailsController();
    switch ($_GET["op"]) {
        case 'guardar':
            $controller->guardar();
            break;
        case 'actualizar':
            $controller->actualizar();
            break;
        case 'mostrar':
            $controller->mostrar();
            break;
        case 'obtenerDepartamentos':
            $controller->obtenerDepartamentos();
            break;
        case 'obtenerProvincias':
            $controller->obtenerProvincias();
            break;
        // Se elimina el caso 'obtenerDistritos' ya que ya no es necesario
        default:
            echo json_encode(['status' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
