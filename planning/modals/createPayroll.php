<div class="modal fade" id="createPayroll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear Nomina</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="vertical-app-tabs" id="rootwizard">
                            <div class="col-md-12 col-lg-12 InputGroup">
                                <form id="formCreatePayroll">
                                    <div class="row mt-5">
                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Descripción</b></label>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="firstname" name="firstname" type="text" class="form-control">
                                                <label for="firstname">Nombres <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="lastname" name="lastname" type="text" class="form-control">
                                                <label for="lastname">Apellidos <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="position" name="position" type="text" class="form-control">
                                                <label for="position">Posición <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idArea" id="idArea"></select>
                                                <label for="idArea">Área <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control" name="idProcess" id="idProcess"></select>
                                                <label for="idProcess">Proceso <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select class="form-control idMachine" name="idMachine" id="idMachine"></select>
                                                <label for="idMachine">Maquina <span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>
                                        <!-- < ?php if ($_SESSION['type_payroll'] == '1') { ?> -->

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Devengado</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="basicSalary" name="basicSalary" type="number" class="form-control basicSalary text-center">
                                                <label for="basicSalary">Salario Básico<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="transport" name="transport" type="number" class="form-control transport text-center">
                                                <label for="transport">Transporte<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="endowment" name="endowment" type="number" class="form-control endowment text-center">
                                                <label for="endowment">Dotaciones<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="extraTime" name="extraTime" type="number" class="form-control extraTime text-center">
                                                <label for="extraTime">Horas Extras<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="bonification" name="bonification" type="number" class="form-control bonification text-center">
                                                <label for="otherIncome">Otros Ingresos<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Jornada</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="workingHoursDay" name="workingHoursDay" type="number" class="form-control workingHoursDay text-center">
                                                <label for="workingHoursDay">Horas Trabajo x Día<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="workingDaysMonth" name="workingDaysMonth" type="number" class="form-control workingDaysMonth text-center">
                                                <label for="workingDaysMonth">Dias Trabajo x Mes<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Nivel de Riesgo</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select id="risk" name="risk" class="form-control risk"> </select>
                                                <label for="risk">Riesgo<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="valueRisk" name="valueRisk" type="text" class="form-control text-center valueRisk" readonly>
                                                <label for="valueRisk">Valor<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-12 titlePayroll">
                                            <label><b>Factor Prestacional</b></label>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <select id="typeFactor" name="typeFactor" type="number" class="form-control typeFactor" data-toggle="tooltip" title="Seleccione el tipo de contrato para su colaborador, la plataforma calculara automaticamente las prestaciones sociales">
                                                    <option selected disabled value="0">Seleccionar</option>
                                                    <option value="1">Nómina</option>
                                                    <option value="2">Servicios</option>
                                                    <option value="3">Calculo Manual</option>
                                                </select>
                                                <label for="typeFactor">Tipo Nómina<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <div class="form-group floating-label enable-floating-label show-label">
                                                <input id="factor" name="factor" type="number" class="form-control text-center factor">
                                                <label for="factor">Factor<span class="text-danger">*</span></label>
                                                <div class="validation-error d-none font-size-13">Requerido</div>
                                            </div>
                                        </div>

                                        <hr>
                                        <!-- < ?php } ?> -->
                                    </div>
                                    <!-- This button link with id-sw-default-step-1 if you change it change in serial number like below -->
                                    <!-- <div class="d-none">
                                        <button class="btn btn-primary" id="btn">submit</button>
                                    </div> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCloseCardPayroll">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCreatePayroll">Crear</button>
            </div>
        </div>
    </div>
</div>