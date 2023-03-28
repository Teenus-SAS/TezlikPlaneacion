<div class="modal fade" id="createSale" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Ventas</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreateSale">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="referenceProduct" id="refProduct"></select>
                                                <label for="refProduct">Referencia<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-8">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idProduct" id="selectNameProduct"></select>
                                                <label for="selectNameProduct">Producto<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Unidades Vendidas x Mes</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="january" name="january" type="number" class="form-control text-center month">
                                                <label for="january">Enero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="february" name="february" type="number" class="form-control text-center month">
                                                <label for="february">Febrero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="march" name="march" type="number" class="form-control text-center month">
                                                <label for="march">Marzo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="april" name="april" type="number" class="form-control text-center month">
                                                <label for="april">Abril<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="may" name="may" type="number" class="form-control text-center month">
                                                <label for="may">Mayo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="june" name="june" type="number" class="form-control text-center month">
                                                <label for="june">Junio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="july" name="july" type="number" class="form-control text-center month">
                                                <label for="july">Julio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="august" name="august" type="number" class="form-control text-center month">
                                                <label for="august">Agosto<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="september" name="september" type="number" class="form-control text-center month">
                                                <label for="september">Septiembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="october" name="october" type="number" class="form-control text-center month">
                                                <label for="october">Octubre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="november" name="november" type="number" class="form-control text-center month">
                                                <label for="november">Noviembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="december" name="december" type="number" class="form-control text-center month">
                                                <label for="december">Diciembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <!-- This button link with id-sw-default-step-1 if you change it change in serial number like below 
                                    <div class="d-none">
                                        <button class="btn btn-primary" id="btn">submit</button>
                                    </div> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseSale">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreateSale">Crear</button>
            </div>
        </div>
    </div>
</div>