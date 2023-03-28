<div class="modal fade" id="createCompany" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Empresa</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreateCompany">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll pt-2">
                                            <label for=""><b>Empresa</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="company" name="company" type="text" class="form-control">
                                                <label for="company">Nombre Empresa<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyNIT" name="companyNIT" type="text" class="form-control">
                                                <label for="companyNIT">NIT<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-12">
                                            <div class="bg-secondary-soft mb-4 rounded">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <label for="Image" class="form-label">Ingrese su logo</label>
                                                        <input class="form-control" type="file" id="formFile">
                                                    </div>
                                                    <div class="col-4">
                                                        <img id="logo" src="" class="img-fluid" style="width: 100px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll pt-2">
                                            <label for=""><b>Ubicación y Contacto</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyCity" name="companyCity" type="text" class="form-control">
                                                <label for="companyCity">Ciudad<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyState" name="companyState" type="text" class="form-control">
                                                <label for="companyState">Departamento<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyCountry" name="companyCountry" type="text" class="form-control">
                                                <label for="companyCountry">País<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyAddress" name="companyAddress" type="text" class="form-control">
                                                <label for="companyAddress">Dirección<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="companyTel" name="companyTel" type="tel" class="form-control">
                                                <label for="companyTel">Teléfono<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <!-- <div id="license" class="row">
                                            <div class="col-12 col-lg-12 titlePayroll pt-2 bg-info text-white">
                                                <label for=""><b>Licencia</b></label>
                                            </div>

                                            <div class="col-12 col-lg-4">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input id="companyLic_start" name="companyLic_start" type="date" class="form-control">
                                                    <label for="companyLic_start">Inicio Licencia<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-4">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input id="companyLic_end" name="companyLic_end" type="date" class="form-control">
                                                    <label for="companyLic_end">Final Licencia<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-2">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <input id="companyUsers" name="companyUsers" type="number" class="form-control">
                                                    <label for="companyUsers">Usuarios<span class="text-danger">*</span></label>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-2">
                                                <div class="form-group floating-label enable-floating-label show-label">
                                                    <label for="companyStatus">Estado<span class="text-danger">*</span></label>
                                                    <select class="form-control" id="companyStatus">
                                                        <option value="1" selected>Activa</option>
                                                        <option value="0">Inactiva</option>
                                                    </select>
                                                    <div class="validation-error d-none font-size-13">Requerido</div>
                                                </div>
                                            </div>
                                        </div> -->



                                    </div>
                                    <div class="row justify-content-md-end">
                                        <button type="button" class="col-6 col-sm-2 btn btn-secondary m-2" data-bs-dismiss="modal" id="btnCloseCompany">Cerrar</button>
                                        <button type="submit" class="col-6 col-sm-2 btn btn-primary m-2" id="btnCreateCompany">Crear</button>
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
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCardPayroll">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePayroll">Crear</button>
            </div> -->
        </div>
    </div>
</div>