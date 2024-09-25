<?php


require 'layout/header.php';
require 'layout/navbar.php';
require 'layout/sidebar.php';
?>
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Gestionar Proveedores</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item">Configuración</li>
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="accordionBasic" class="widget-header">
                    <div class="text-left">
                        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#formularioregistros">
                            <i class="fa fa-plus"></i> Agregar Proveedor
                        </button>
                    </div>
                </div>

                <!-- Tabla para listar proveedores -->
                <div class="table-responsive mb-4 mt-4">
                    <table id="tbllistado" class="table table-striped table-bordered" style="width:100%">
                        <thead style="background-color: #2A3E52; color: white;">
                            <tr>
                                <th>ID</th>
                                <th>RUC</th>
                                <th>Nombre de Empresa</th>
                                <th>Estado SUNAT</th>
                                <th>Condicion SUNAT</th>
                                <th>Nombre Contacto</th>
                                <th>Email Contacto</th>
                                <th>Teléfono Contacto</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Modal para agregar proveedor -->
                <!-- Modal para agregar proveedor -->
                <!-- Modal para agregar proveedor -->
                <div class="modal fade" id="formularioregistros" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Agregar Proveedor</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form  id="formulario" method="POST">
                                    <div class="row">
                                        <!-- Información de la Empresa -->
                                        <div class="col-12 mb-3">
                                            <h6>Información de la Empresa</h6>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="RUC">RUC *</label>
                                            <input class="form-control" type="text" id="RUC" name="RUC" maxlength="11" placeholder="RUC" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="companyName">Nombre de la Empresa</label>
                                            <input class="form-control" type="text" id="companyName" name="companyName" placeholder="Nombre de la Empresa" required readonly>
                                        </div>

                                        <!-- Dirección -->
                                        <div class="form-group col-lg-4">
                                            <label for="department">Departamento</label>
                                            <input class="form-control" type="text" id="department" name="department" placeholder="Departamento" required readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="province">Provincia</label>
                                            <input class="form-control" type="text" id="province" name="province" placeholder="Provincia" required readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="district">Distrito</label>
                                            <input class="form-control" type="text" id="district" name="district" placeholder="Distrito" required readonly>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label for="address">Dirección</label>
                                            <input class="form-control" type="text" id="address" name="address" placeholder="Dirección" required readonly>
                                        </div>

                                        <!-- Estado SUNAT -->
                                        <div class="form-group col-lg-6">
                                            <label for="stateSunat">Estado de Contribuyente</label>
                                            <input class="form-control" type="text" id="stateSunat" name="stateSunat" placeholder="Estado de Contribuyente" required readonly>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="conditionSunat">Condición de Contribuyente</label>
                                            <input class="form-control" type="text" id="conditionSunat" name="conditionSunat" placeholder="Condición de Contribuyente" required readonly>
                                        </div>

                                        <!-- Información de Contacto -->
                                        <div class="col-12 mb-3 mt-4">
                                            <h6>Información de Contacto</h6>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactNameBusiness">Nombre del Contacto</label>
                                            <input class="form-control" type="text" id="contactNameBusiness" name="contactNameBusiness" placeholder="Nombre del Contacto" required>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactEmailBusiness">Email Contacto</label>
                                            <input class="form-control" type="email" id="contactEmailBusiness" name="contactEmailBusiness" placeholder="Email del Contacto" required>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactPhoneBusiness">Teléfono Contacto</label>
                                            <input class="form-control" type="text" id="contactPhoneBusiness" name="contactPhoneBusiness" placeholder="Teléfono del Contacto" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="submit" class="btn btn-success">Guardar</button>
                                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para actualizar proveedor -->
                <div class="modal fade" id="formularioActualizar" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Actualizar Proveedor</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="formActualizar" method="POST">
                                    <input type="hidden" id="idUpdate" name="idUpdate"> <!-- Campo oculto para el ID del proveedor -->
                                    <div class="row">
                                        <!-- Información de la Empresa -->
                                        <div class="col-12 mb-3">
                                            <h6>Información de la Empresa</h6>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="RUCUpdate">RUC</label>
                                            <input class="form-control" type="text" id="RUCUpdate" name="RUCUpdate" maxlength="11" placeholder="RUC" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="companyNameUpdate">Nombre de la Empresa</label>
                                            <input class="form-control" type="text" id="companyNameUpdate" name="companyNameUpdate" required readonly>
                                        </div>

                                        <!-- Dirección -->
                                        <div class="form-group col-lg-4">
                                            <label for="departmentUpdate">Departamento</label>
                                            <input class="form-control" type="text" id="departmentUpdate" name="departmentUpdate" required readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="provinceUpdate">Provincia</label>
                                            <input class="form-control" type="text" id="provinceUpdate" name="provinceUpdate" required readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="districtUpdate">Distrito</label>
                                            <input class="form-control" type="text" id="districtUpdate" name="districtUpdate" required readonly>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label for="addressUpdate">Dirección</label>
                                            <input class="form-control" type="text" id="addressUpdate" name="addressUpdate" required readonly>
                                        </div>

                                        <!-- Estado SUNAT -->
                                        <div class="form-group col-lg-6">
                                            <label for="stateSunatUpdate">Estado de Contribuyente</label>
                                            <input class="form-control" type="text" id="stateSunatUpdate" name="stateSunatUpdate" required readonly>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="conditionSunatUpdate">Condición de Contribuyente</label>
                                            <input class="form-control" type="text" id="conditionSunatUpdate" name="conditionSunatUpdate" required readonly>
                                        </div>

                                        <!-- Información de Contacto -->
                                        <div class="col-12 mb-3 mt-4">
                                            <h6>Información de Contacto</h6>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactNameBusinessUpdate">Nombre del Contacto</label>
                                            <input class="form-control" type="text" id="contactNameBusinessUpdate" name="contactNameBusinessUpdate" required>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactEmailBusinessUpdate">Email Contacto</label>
                                            <input class="form-control" type="email" id="contactEmailBusinessUpdate" name="contactEmailBusinessUpdate" required>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="contactPhoneBusinessUpdate">Teléfono Contacto</label>
                                            <input class="form-control" type="text" id="contactPhoneBusinessUpdate" name="contactPhoneBusinessUpdate" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>


<script src="scripts/supplier.js"></script>