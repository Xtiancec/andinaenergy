<?php
// superadmin_dashboard.php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'applicant' || $_SESSION['user_role'] !== 'postulante') {
    header("Location: ../login.php");
    exit();
}

require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Registrar/Actualizar Datos Personales</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Postulantes</li>
            <li class="breadcrumb-item active">Registrar/Actualizar Datos</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Formulario para registrar datos -->
                <form class="needs-validation" id="formApplicantDetailsRegister" method="POST" novalidate>
                    <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                        <div class="card-body p-4">
                            <h4 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-user-plus"></i> Registrar Datos Personales</h4>

                            <!-- Fila 1: Nivel Educativo y Género -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <!-- Cambiar el nombre del campo de 'nivel_estudio' a 'education_level' -->
                                    <label for="educationLevel" class="font-weight-bold"><i class="fa fa-venus-mars"></i> Nivel de Educacion</label>

                                    <select class="form-control rounded-lg" id="education_level" name="education_level" required>
                                        <option value="" disabled selected>Selecciona tu nivel de estudio</option>
                                        <option value="Secundaria Completa">Secundaria Completa</option>
                                        <option value="Secundaria Incompleta">Secundaria Incompleta</option>
                                        <option value="Superior Completo">Superior Completo</option>
                                        <option value="Superior Incompleto">Superior Incompleto</option>
                                        <option value="Maestría">Maestría</option>
                                        <option value="Doctorado">Doctorado</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu nivel de estudio.
                                    </div>

                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu nivel de estudio.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="gender" class="font-weight-bold"><i class="fa fa-venus-mars"></i> Género</label>
                                    <select class="form-control rounded-lg" id="gender" name="gender" required>
                                        <option value="" disabled selected>Selecciona tu género</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu género.
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 2: Teléfono, Teléfono de Emergencia y Contacto de Emergencia -->
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="phone" class="font-weight-bold"><i class="fa fa-phone"></i> Teléfono</label>
                                    <input type="text" class="form-control rounded-lg" id="phone" name="phone" maxlength="15" placeholder="Ingresa tu teléfono" required pattern="^\d{7,15}$">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un teléfono válido de 7 a 15 dígitos.
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="emergency_contact_phone" class="font-weight-bold"><i class="fa fa-phone-square"></i> Teléfono de Emergencia</label>
                                    <input type="text" class="form-control rounded-lg" id="emergency_contact_phone" name="emergency_contact_phone" maxlength="15" placeholder="Teléfono de emergencia" pattern="^\d{7,15}$">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un teléfono válido de 7 a 15 dígitos.
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="contacto_emergencia" class="font-weight-bold"><i class="fa fa-user-circle"></i> Contacto de Emergencia</label>
                                    <input type="text" class="form-control rounded-lg" id="contacto_emergencia" name="contacto_emergencia" maxlength="255" placeholder="Nombre del contacto de emergencia" required pattern=".{2,255}">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un nombre válido para el contacto de emergencia (mínimo 2 caracteres).
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 3: País, Departamento, Provincia y Dirección (Reemplazo de Distrito) -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="pais" class="font-weight-bold"><i class="fa fa-globe"></i> País</label>
                                    <select class="form-control rounded-lg" id="pais" name="pais" required>
                                        <option value="" disabled>Selecciona tu país</option>
                                        <!-- Países de América con Perú seleccionado por defecto -->
                                        <option value="Argentina">Argentina</option>
                                        <option value="Belice">Belice</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Brasil">Brasil</option>
                                        <option value="Canadá">Canadá</option>
                                        <option value="Chile">Chile</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="República Dominicana">República Dominicana</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Granada">Granada</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guyana">Guyana</option>
                                        <option value="Haití">Haití</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="México">México</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                        <option value="Panamá">Panamá</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Perú" selected>Perú</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="República Centroafricana">República Centroafricana</option>
                                        <option value="San Cristóbal y Nieves">San Cristóbal y Nieves</option>
                                        <option value="San Vicente y las Granadinas">San Vicente y las Granadinas</option>
                                        <option value="Santa Lucía">Santa Lucía</option>
                                        <option value="Surinam">Surinam</option>
                                        <option value="Trinidad y Tobago">Trinidad y Tobago</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Venezuela">Venezuela</option>
                                        <!-- Otros países de América pueden añadirse aquí -->
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu país.
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="departamento" class="font-weight-bold"><i class="fa fa-map"></i> Departamento</label>
                                    <select class="form-control rounded-lg" id="departamento" name="departamento" required>
                                        <option value="" disabled selected>Selecciona tu departamento</option>
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu departamento.
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="provincia" class="font-weight-bold"><i class="fa fa-map-marker-alt"></i> Provincia</label>
                                    <select class="form-control rounded-lg" id="provincia" name="provincia" required>
                                        <option value="" disabled selected>Selecciona tu provincia</option>
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu provincia.
                                    </div>
                                </div>

                                <!-- Reemplazo de Distrito por Dirección -->
                                <div class="form-group col-md-3">
                                    <label for="direccion" class="font-weight-bold"><i class="fa fa-map-signs"></i> Dirección</label>
                                    <input type="text" class="form-control rounded-lg" id="direccion" name="direccion" maxlength="255" placeholder="Ingresa tu dirección" required pattern=".{2,255}">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa una dirección válida (mínimo 2 caracteres).
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 4: Estado Civil y Cantidad de Hijos -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="marital_status" class="font-weight-bold"><i class="fa fa-life-ring"></i> Estado Civil</label>
                                    <select class="form-control rounded-lg" id="marital_status" name="marital_status" required>
                                        <option value="" disabled selected>Selecciona tu estado civil</option>
                                        <option value="Soltero">Soltero</option>
                                        <option value="Casado">Casado</option>
                                        <option value="Divorciado">Divorciado</option>
                                        <option value="Viudo">Viudo</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecciona tu estado civil.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="children_count" class="font-weight-bold"><i class="fa fa-child"></i> Cantidad de Hijos</label>
                                    <input type="number" class="form-control rounded-lg" id="children_count" name="children_count" min="0" placeholder="Cantidad de hijos" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa una cantidad válida de hijos.
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 5: Fecha de Nacimiento y Nivel Educativo -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="birth_date" class="font-weight-bold"><i class="fa fa-birthday-cake"></i> Fecha de Nacimiento</label>
                                    <input type="date" class="form-control rounded-lg" id="birth_date" name="birth_date" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa una fecha de nacimiento válida.
                                    </div>
                                </div>

                            </div>

                            <!-- Botón de Envío -->
                            <button type="submit" class="btn btn-primary btn-block rounded-lg font-weight-bold">
                                <i class="fa fa-save"></i> Guardar Datos
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Vista de Datos Registrados -->
                <div class="row justify-content-center mt-4">
                    <div class="col-6">
                        <div id="datosRegistrados" style="display:none;">
                            <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h2 class="text-info mb-4 font-weight-bold text-center">
                                        <i class="fa fa-check "></i> Mis Datos Personales Registrados
                                    </h2>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-phone fa-lg text-info mr-3"></i>
                                                <span><strong>Teléfono:</strong></span>
                                            </div>
                                            <span id="verPhone" class="text-muted">999999999</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-phone-square fa-lg text-info mr-3"></i>
                                                <span><strong>Teléfono de Emergencia:</strong></span>
                                            </div>
                                            <span id="verEmergencyPhone" class="text-muted">3333</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-user-circle fa-lg text-info mr-3"></i>
                                                <span><strong>Contacto de Emergencia:</strong></span>
                                            </div>
                                            <span id="verContactoEmergencia" class="text-muted">Nombre Contacto</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-globe fa-lg text-info mr-3"></i>
                                                <span><strong>País:</strong></span>
                                            </div>
                                            <span id="verPais" class="text-muted">Perú</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-map fa-lg text-info mr-3"></i>
                                                <span><strong>Departamento:</strong></span>
                                            </div>
                                            <span id="verDepartamento" class="text-muted">Lima</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-map-marker fa-lg text-info mr-3"></i>
                                                <span><strong>Provincia:</strong></span>
                                            </div>
                                            <span id="verProvincia" class="text-muted">Lima</span>
                                        </li>
                                        <!-- Reemplazo de Distrito por Dirección -->
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-map-signs fa-lg text-info mr-3"></i>
                                                <span><strong>Dirección:</strong></span>
                                            </div>
                                            <span id="verDireccion" class="text-muted">Dirección Ejemplo</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-venus-mars fa-lg text-info mr-3"></i>
                                                <span><strong>Género:</strong></span>
                                            </div>
                                            <span id="verGender" class="text-muted">Masculino</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-birthday-cake fa-lg text-info mr-3"></i>
                                                <span><strong>Fecha de Nacimiento:</strong></span>
                                            </div>
                                            <span id="verBirthDate" class="text-muted">1992-07-03</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-life-ring fa-lg text-info mr-3"></i>
                                                <span><strong>Estado Civil:</strong></span>
                                            </div>
                                            <span id="verMaritalStatus" class="text-muted">Soltero</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-child fa-lg text-info mr-3"></i>
                                                <span><strong>Cantidad de Hijos:</strong></span>
                                            </div>
                                            <span id="verChildrenCount" class="text-muted">1</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-graduation-cap fa-lg text-info mr-3"></i>
                                                <span><strong>Nivel de Estudio:</strong></span>
                                            </div>
                                            <span id="verNivelEstudio" class="text-muted">Superior</span>
                                        </li>
                                    </ul>
                                    <button id="btnEditarPerfil" class="btn btn-info btn-lg btn-block rounded-lg font-weight-bold mt-3">
                                        <i class="fa fa-edit"></i> Editar Perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario para actualizar datos -->
                    <form class="needs-validation" id="formApplicantDetailsUpdate" method="POST" novalidate style="display:none;">
                        <div class="card shadow-sm border rounded-lg" style="border-color: #17a2b8; border-radius: 15px;">
                            <div class="card-body p-4">
                                <h2 class="text-info mb-4 font-weight-bold text-center"><i class="fa fa-edit"></i> Actualizar Datos Personales</h2>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <!-- Cambiar el nombre del campo de 'nivel_estudioUpdate' a 'education_levelUpdate' -->
                                        <label for="educationLevelUpdate" class="font-weight-bold"><i class="fa fa-venus-mars"></i> Nivel de Educacion</label>
                                        <select class="form-control rounded-lg" id="education_levelUpdate" name="education_levelUpdate" required>
                                            <option value="" disabled>Selecciona tu nivel de estudio</option>
                                            <option value="Secundaria Completa">Secundaria Completa</option>
                                            <option value="Secundaria Incompleta">Secundaria Incompleta</option>
                                            <option value="Superior Completo">Superior Completo</option>
                                            <option value="Superior Incompleto">Superior Incompleto</option>
                                            <option value="Maestría">Maestría</option>
                                            <option value="Doctorado">Doctorado</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu nivel de estudio.
                                        </div>

                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu nivel de estudio.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="genderUpdate" class="font-weight-bold"><i class="fa fa-venus-mars"></i> Género</label>
                                        <select class="form-control rounded-lg" id="genderUpdate" name="genderUpdate" required>
                                            <option value="" disabled>Selecciona tu género</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu género.
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="phoneUpdate" class="font-weight-bold"><i class="fa fa-phone"></i> Teléfono</label>
                                        <input type="text" class="form-control rounded-lg" id="phoneUpdate" name="phoneUpdate" maxlength="15" placeholder="Ingresa tu teléfono" required pattern="^\d{7,15}$">
                                        <div class="invalid-feedback">
                                            Por favor, ingresa un teléfono válido de 7 a 15 dígitos.
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="emergency_contact_phoneUpdate" class="font-weight-bold"><i class="fa fa-phone-square"></i> Teléfono de Emergencia</label>
                                        <input type="text" class="form-control rounded-lg" id="emergency_contact_phoneUpdate" name="emergency_contact_phoneUpdate" maxlength="15" placeholder="Teléfono de emergencia" pattern="^\d{7,15}$">
                                        <div class="invalid-feedback">
                                            Por favor, ingresa un teléfono válido de 7 a 15 dígitos.
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="contacto_emergenciaUpdate" class="font-weight-bold"><i class="fa fa-user-circle"></i> Contacto de Emergencia</label>
                                        <input type="text" class="form-control rounded-lg" id="contacto_emergenciaUpdate" name="contacto_emergenciaUpdate" maxlength="255" placeholder="Nombre del contacto de emergencia" required pattern=".{2,255}">
                                        <div class="invalid-feedback">
                                            Por favor, ingresa un nombre válido para el contacto de emergencia (mínimo 2 caracteres).
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 3: País, Departamento, Provincia y Dirección (Reemplazo de Distrito) -->
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="paisUpdate" class="font-weight-bold"><i class="fa fa-globe"></i> País</label>
                                        <select class="form-control rounded-lg" id="paisUpdate" name="paisUpdate" required>
                                            <option value="" disabled>Selecciona tu país</option>
                                            <!-- Países de América con Perú seleccionado por defecto -->
                                            <option value="Argentina">Argentina</option>
                                            <option value="Belice">Belice</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Brasil">Brasil</option>
                                            <option value="Canadá">Canadá</option>
                                            <option value="Chile">Chile</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Dominica">Dominica</option>
                                            <option value="República Dominicana">República Dominicana</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="Granada">Granada</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Guyana">Guyana</option>
                                            <option value="Haití">Haití</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="México">México</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Panamá">Panamá</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Perú" selected>Perú</option>
                                            <option value="Puerto Rico">Puerto Rico</option>
                                            <option value="San Cristóbal y Nieves">San Cristóbal y Nieves</option>
                                            <option value="San Vicente y las Granadinas">San Vicente y las Granadinas</option>
                                            <option value="Santa Lucía">Santa Lucía</option>
                                            <option value="Surinam">Surinam</option>
                                            <option value="Trinidad y Tobago">Trinidad y Tobago</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <!-- Otros países de América pueden añadirse aquí -->
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu país.
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="departamentoUpdate" class="font-weight-bold"><i class="fa fa-map"></i> Departamento</label>
                                        <select class="form-control rounded-lg" id="departamentoUpdate" name="departamentoUpdate" required>
                                            <option value="" disabled selected>Selecciona tu departamento</option>
                                            <!-- Opciones dinámicas -->
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu departamento.
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="provinciaUpdate" class="font-weight-bold"><i class="fa fa-map-marker-alt"></i> Provincia</label>
                                        <select class="form-control rounded-lg" id="provinciaUpdate" name="provinciaUpdate" required>
                                            <option value="" disabled selected>Selecciona tu provincia</option>
                                            <!-- Opciones dinámicas -->
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu provincia.
                                        </div>
                                    </div>

                                    <!-- Reemplazo de Distrito por Dirección -->
                                    <div class="form-group col-md-3">
                                        <label for="direccionUpdate" class="font-weight-bold"><i class="fa fa-map-signs"></i> Dirección</label>
                                        <input type="text" class="form-control rounded-lg" id="direccionUpdate" name="direccionUpdate" maxlength="255" placeholder="Ingresa tu dirección" required pattern=".{2,255}">
                                        <div class="invalid-feedback">
                                            Por favor, ingresa una dirección válida (mínimo 2 caracteres).
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 4: Estado Civil y Cantidad de Hijos -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="marital_statusUpdate" class="font-weight-bold"><i class="fa fa-life-ring"></i> Estado Civil</label>
                                        <select class="form-control rounded-lg" id="marital_statusUpdate" name="marital_statusUpdate" required>
                                            <option value="" disabled>Selecciona tu estado civil</option>
                                            <option value="Soltero">Soltero</option>
                                            <option value="Casado">Casado</option>
                                            <option value="Divorciado">Divorciado</option>
                                            <option value="Viudo">Viudo</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu estado civil.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="children_countUpdate" class="font-weight-bold"><i class="fa fa-child"></i> Cantidad de Hijos</label>
                                        <input type="number" class="form-control rounded-lg" id="children_countUpdate" name="children_countUpdate" min="0" placeholder="Cantidad de hijos" required>
                                        <div class="invalid-feedback">
                                            Por favor, ingresa una cantidad válida de hijos.
                                        </div>
                                    </div>
                                </div>

                                <!-- Fila 5: Fecha de Nacimiento y Nivel Educativo -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="birth_dateUpdate" class="font-weight-bold"><i class="fa fa-birthday-cake"></i> Fecha de Nacimiento</label>
                                        <input type="date" class="form-control rounded-lg" id="birth_dateUpdate" name="birth_dateUpdate" required>
                                        <div class="invalid-feedback">
                                            Por favor, ingresa una fecha de nacimiento válida.
                                        </div>
                                    </div>



                                </div>

                                <div class="form-row d-flex justify-content-center flex-wrap">
                                    <!-- Botón de actualización -->
                                    <div class="col-auto mb-2 mx-2">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-lg font-weight-bold">
                                            <i class="fa fa-sync"></i> Actualizar Datos
                                        </button>
                                    </div>

                                    <!-- Botón de regresar al perfil -->
                                    <div class="col-auto mb-2 mx-2">
                                        <button type="button" id="btnBackToProfile" class="btn btn-secondary btn-lg rounded-lg font-weight-bold">
                                            <i class="fa fa-arrow-left"></i> Regresar a Mi Perfil
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                </div>
                </form>

                <!-- Vista de Datos Registrados y Formularios -->
                <!-- ... (como ya lo tienes) -->
            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>

<!-- Incluir el archivo JavaScript externo -->
<script src="scripts/applicant_details.js"></script>