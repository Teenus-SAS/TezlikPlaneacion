<div class="modal fade" id="createOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Creación de Pedido</h5>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreateOrder">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Pedido</b></label>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="order" name="order" type="text" class="form-control text-center">
                                                <label for="numberWorkers">Numero de Pedido<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input class="form-control" type="date" name="dateOrder" id="dateOrder"></input>
                                                <label for="dateOrder">Fecha Pedido<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input class="form-control" type="date" name="minDate" id="minDate"></input>
                                                <label for="minDate">Fecha Minima<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input class="form-control" type="date" name="maxDate" id="maxDate"></input>
                                                <label for="maxDate">Fecha Maxima<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select id="refProduct" name="idProduct" class="form-control"></select>
                                                <label for="">Referencia<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-8">
                                            <div class="input-group floating-label enable-floating-label show-label">
                                                <select id="selectNameProduct" name="selectNameProduct" class="form-control"></select>
                                                <label for="">Producto<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-8">
                                            <div class="input-group floating-label enable-floating-label show-label date">
                                                <select id="client" name="idClient" class="form-control"></select>
                                                <label for="">Cliente<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-12 col-lg-4">
                                            <div class="input-group floating-label enable-floating-label show-label date">
                                                <select id="orderType" name="idOrderType" class="form-control"></select>
                                                <label for="">Tipo de Pedido<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div> -->

                                        <!-- <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b></b></label>
                                        </div> -->

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="originalQuantity" name="originalQuantity" type="number" class="form-control text-center" min="1" max="24">
                                                <label for="">Cantidad Original<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="quantity" name="quantity" type="number" class="form-control text-center">
                                                <label for="">Cantidad Pendiente<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseOrder">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreateOrder">Crear</button>
            </div>
        </div>
    </div>
</div>