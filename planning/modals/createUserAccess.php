<div class="modal fade" id="createUserAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Usuario</h5>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreateUser">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Usuario</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="nameUser" name="names">
                                                <label for="nameUser">Nombres<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="lastnameUser" name="lastnames">
                                                <label for="lastnameUser">Apellidos<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input type="text" class="form-control" id="emailUser" name="email">
                                                <label for="emailUser">Email<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Accesos De Usuario</b></label>
                                        </div>
                                        <div class="row ml-2">
                                            <div class="col-12 col-lg-12">
                                                <label for=""><b>Menú Configuración:</b></label>
                                            </div>
                                            <div class="col-sm-3 pb-2">Básico
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="planningCreateProduct" type="checkbox">
                                                    <label for="checkbox-1">Productos y MP</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-2" name="planningCreateMachine" type="checkbox">
                                                    <label for="checkbox-2">Procesos y Máquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-3" name="payroll" type="checkbox">
                                                    <label for="checkbox-3">Nomina de Producción</label>
                                                </div>

                                            </div>
                                            <div class="col-sm-4 pb-2">Configuración
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-4" name="planningProductsMaterial" type="checkbox">
                                                    <label for="checkbox-4">Ficha Técnica Productos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-5" name="programsMachine" type="checkbox">
                                                    <label for="checkbox-5">Programación Maquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="stock" type="checkbox">
                                                    <label for="checkbox-6">Tiempos Produccion y Recepcion MP</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="calendar" type="checkbox">
                                                    <label for="checkbox-7">Calendario Produccion</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-8" name="client" type="checkbox">
                                                    <label for="checkbox-8">Clientes y Proveedores</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-9" name="seller" type="checkbox">
                                                    <label for="checkbox-9">Vendedores</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="sale" type="checkbox">
                                                    <label for="checkbox-10">Ventas</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pb-2">Administrador
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-11" name="plannigUser" type="checkbox">
                                                    <label for="checkbox-11">Usuarios y Accesos</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-12 mt-2">
                                                <label for=""><b>Menú Navegación:</b></label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-12" name="inventory" type="checkbox">
                                                    <label for="checkbox-12">Inventarios</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-13" name="requisition" type="checkbox">
                                                    <label for="checkbox-13">Compras</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-14" name="order" type="checkbox">
                                                    <label for="checkbox-14">Pedidos</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-15" name="program" type="checkbox">
                                                    <label for="checkbox-15">Programar Producción</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-16" name="typeProgram" type="checkbox">
                                                    <label for="checkbox-16">Tipo Programar</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-17" name="explosionOfMaterials" type="checkbox">
                                                    <label for="checkbox-17">Explosión de MP</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input class="typeCheckbox" id="checkbox-18" name="productionOrder" type="checkbox">
                                                    <label for="checkbox-18">Ordenes de Producción</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-19" name="opToStore" type="checkbox">
                                                    <label for="checkbox-19">Entregas OP a Almacen</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-20" name="store" type="checkbox">
                                                    <label for="checkbox-20">Almacen</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group floating-label enable-floating-label show-label my-2 cardTypeMachineOP" style="width: 150px">
                                                    <select class="form-control idMachine" name="typeMachineOP" id="typeMachineOP"> </select>
                                                    <label>Maquinas<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-21" name="office" type="checkbox">
                                                    <label for="checkbox-21">Despachos</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseUser">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreateUserAndAccess">Crear</button>
            </div>
        </div>
    </div>
</div>