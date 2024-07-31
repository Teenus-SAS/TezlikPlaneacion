<div class="modal fade" id="createPlanMachine" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Plan Maquina</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePlanMachine">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control idMachine" name="idMachine" id="idMachine"></select>
                                                <label for="idMachine">Maquina<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <!-- <select class="form-control" name="idProcess" id="idProcess"></select> -->
                                                <input id="numberWorkers" name="numberWorkers" type="number" class="form-control">
                                                <label for="numberWorkers">No Trabajadores<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Horario</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="hoursDay" name="hoursDay" type="number" class="form-control text-center hours" min="1" max="24">
                                                <label for="hoursDay">Horas x Dia<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="input-group floating-label enable-floating-label show-label date" id="hourStartPicker" data-target-input="nearest">
                                                <input id="hourStart" name="hourStart" type="text" class="form-control text-center hours datetimepicker-input" data-target="#hourStartPicker">
                                                <label for="hourStart">Horas Inicio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                                <div class="input-group-append" data-target="#hourStartPicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="input-group floating-label enable-floating-label show-label date" id="hourEndPicker" data-target-input="nearest">
                                                <input id="hourEnd" name="hourEnd" type="text" class="form-control text-center datetimepicker-input" data-target="#hourEndPicker" readonly>
                                                <label for="hourEnd">Horas Fin<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                                <div class="input-group-append" data-target="#hourEndPicker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label for=""><b>Meses</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-1" name="january" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-1">Enero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-2" name="february" type="number" class="form-control text-center month" min="1" max="28">
                                                <label for="month-2">Febrero<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-3" name="march" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-3">Marzo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-4" name="april" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="month-4">Abril<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-5" name="may" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-5">Mayo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-6" name="june" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="month-6">Junio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-7" name="july" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-7">Julio<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-8" name="august" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-8">Agosto<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-9" name="september" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="month-9">Septiembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-10" name="october" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-10">Octubre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-11" name="november" type="number" class="form-control text-center month" min="1" max="30">
                                                <label for="month-11">Noviembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="month-12" name="december" type="number" class="form-control text-center month" min="1" max="31">
                                                <label for="month-12">Diciembre<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <!-- This button link with id-sw-default-step-1 if you change it change in serial number like below -->
                                    <div class="d-none">
                                        <button class="btn btn-primary" id="btn">submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnClosePlanMachine">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePlanMachine">Crear</button>
            </div>
        </div>
    </div>
</div>