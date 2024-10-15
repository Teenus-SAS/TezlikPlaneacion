<div class="modal fade" id="createUserAccess" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Accesos De Usuario</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
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
                                                    <input id="checkbox-2" name="planningCreateMaterial" type="checkbox">
                                                    <label for="checkbox-2">Materiales</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-3" name="planningCreateMachine" type="checkbox">
                                                    <label for="checkbox-3">Procesos y Máquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-4" name="requisition" type="checkbox">
                                                    <label for="checkbox-4">Nomina de Producción</label>
                                                </div>

                                            </div>
                                            <div class="col-sm-4 pb-2">Configuración
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-5" name="planningProductsMaterial" type="checkbox">
                                                    <label for="checkbox-5">Ficha Técnica Productos</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="programsMachine" type="checkbox">
                                                    <label for="checkbox-6">Programación Maquinas</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="ciclesMachine" type="checkbox">
                                                    <label for="checkbox-7">Tiempos Produccion y Recepcion MP</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-8" name="stock" type="checkbox">
                                                    <label for="checkbox-8">Calendario Produccion</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pb-2">General
                                                <div class="mt-1 checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-9" name="client" type="checkbox">
                                                    <label for="checkbox-9">Clientes y Proveedores</label>
                                                </div>
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="sale" type="checkbox">
                                                    <label for="checkbox-10">Vendedores</label>
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
                                            <div class="checkbox checkbox-success checkbox-circle">
                                                <input id="checkbox-4" name="requisition" type="checkbox">
                                                <label for="checkbox-4">Compras</label>
                                            </div>
                                            <!-- <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-13" name="inventoryABC" type="checkbox">
                                                    <label for="checkbox-13">Inventario ABC</label>
                                                </div>
                                            </div> -->
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
                                                    <input id="checkbox-16" name="explosionOfMaterials" type="checkbox">
                                                    <label for="checkbox-16">Explosión de MP</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-17" name="productionOrder" type="checkbox">
                                                    <label for="checkbox-17">Ordenes de Producción</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-19" name="store" type="checkbox">
                                                    <label for="checkbox-19">Almacen</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-18" name="office" type="checkbox">
                                                    <label for="checkbox-18">Despachos</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="row mb-4" style="margin:auto;">
                                            <div class="col-lg-3 mb-3">Básico</div>
                                            <div class="col col-lg-3">
                                                Configuración
                                            </div>
                                            <div class="col col-lg-3">
                                                Configuración General
                                            </div>
                                            <div class="col">
                                                Creación Usuarios
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-1" name="planningCreateProduct" type="checkbox">
                                                    <label for="checkbox-1">Productos</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-5" name="productMaterials" type="checkbox">
                                                    <label for="checkbox-5">Ficha Técnica Productos</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-9" name="clients" type="checkbox">
                                                    <label for="checkbox-9">Clientes</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-11" name="user" type="checkbox">
                                                    <label for="checkbox-11">Usuarios</label>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-2" name="planningCreateMaterials" type="checkbox">
                                                    <label for="checkbox-2">Materiales</label>
                                                </div>
                                            </div>
                                            <div class="col col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-6" name="planningMachine" type="checkbox">
                                                    <label for="checkbox-6">Programación Maquina</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-10" name="sales" type="checkbox">
                                                    <label for="checkbox-10">Ventas</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-12" name="orderTypes" type="checkbox">
                                                    <label for="checkbox-12">Tipos Pedidos</label>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-3" name="planningCreateMachines" type="checkbox">
                                                    <label for="checkbox-3">Máquinas</label>
                                                </div>
                                            </div>
                                            <div class="col col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-7" name="planCiclesMachine" type="checkbox">
                                                    <label for="checkbox-7">Plan Ciclos Maquina</label>
                                                </div>
                                            </div>
                                            <div class="col"></div>
                                            <div class="col"></div>
                                            <div class="w-100"></div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-4" name="planningCreateMachines" type="checkbox">
                                                    <label for="checkbox-4">Requisiciones</label>
                                                </div>
                                            </div>
                                            <div class="col col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-8" name="planCiclesMachine" type="checkbox">
                                                    <label for="checkbox-8">Stock</label>
                                                </div>
                                            </div>
                                            <div class="col"></div>
                                            <div class="col"></div>
                                            <div class="w-100"></div>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <label for=""><b>Menú Navegación:</b></label>
                                        </div>
                                        <div class="row" style="margin:auto;">
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-13" name="inventories" type="checkbox">
                                                    <label for="checkbox-13">Inventarios</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-16" name="loads" type="checkbox">
                                                    <label for="checkbox-16">Cargues</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-19" name="offices" type="checkbox">
                                                    <label for="checkbox-19">Despachos</label>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-14" name="orders" type="checkbox">
                                                    <label for="checkbox-14">Pedidos</label>
                                                </div>
                                            </div>
                                            <div class="col col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-17" name="explosionOfMaterials" type="checkbox">
                                                    <label for="checkbox-17">Explosión de Materiales</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-20" name="store" type="checkbox">
                                                    <label for="checkbox-20">Almacen</label>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col col-lg-4">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-15" name="programming" type="checkbox">
                                                    <label for="checkbox-15">Programa</label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="checkbox checkbox-success checkbox-circle">
                                                    <input id="checkbox-18" name="productionOrder" type="checkbox">
                                                    <label for="checkbox-18">Orden de Producción</label>
                                                </div>
                                            </div>

                                        </div> -->
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