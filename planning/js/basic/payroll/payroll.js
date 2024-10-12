$(document).ready(function () {
  loadDataMachines(2);
  $('#factor').prop('disabled', true);

  /* Ocultar modal Nueva nomina */
  $('#btnCloseCardPayroll').click(function (e) {
    e.preventDefault();
    sessionStorage.removeItem('percentage');
    sessionStorage.removeItem('salary');
    sessionStorage.removeItem('type_salary');

    $('#createPayroll').modal('hide');
  });

  /* Abrir modal crear nomina */
  $('#btnNewEmployee').click(function (e) {
    e.preventDefault();

    $('.cardImportEmployees').hide(800);
    $('#createPayroll').modal('show');
    $('#btnCreatePayroll').html('Crear');

    sessionStorage.removeItem('id_plan_payroll');

    $('#formCreatePayroll').trigger('reset');
  });

  /* Agregar nueva nomina */

  $('#btnCreatePayroll').click(function (e) {
    e.preventDefault();
    let idPayroll = sessionStorage.getItem('id_plan_payroll');
    
    const url = idPayroll ? "/api/updatePayroll" : "/api/addPayroll";
    checkDataPayroll(url, idPayroll);
  });
  
  /* Actualizar nomina */
  $(document).on("click", ".updatePayroll", function (e) {
    $(".cardImportEmployees").hide(800);
    $('#createPayroll').modal('show');
    $("#btnCreateEmployee").text("Actualizar");

    // Obtener el ID del elemento
    const idPayroll = $(this).attr("id").split("-")[1];
    sessionStorage.setItem("id_plan_payroll", idPayroll);

    // Obtener data
    const row = $(this).closest("tr")[0];
    const data = tblEmployees.fnGetData(row);

    // Asignar valores a los campos del form
    $("#firstname").val(data.firstname);
    $("#lastname").val(data.lastname);
    $(`#idArea option[value=${data.id_plan_area}]`).prop("selected", true);
    $(`#idProcess option[value=${data.id_process}]`).prop("selected", true);
    $(`#idMachine option[value=${data.id_machine}]`).prop("selected", true);
    $("#position").val(data.position);

    $('#basicSalary').val(data.salary);
    $('#transport').val(data.transport);
    $('#endowment').val(data.endowment);
    $('#extraTime').val(data.extra_time);
    $('#bonification').val(data.bonification);

    $('#workingHoursDay').val(data.hours_day);
    $('#workingDaysMonth').val(data.working_days_month);

    $(`#risk option[value=${data.id_risk}]`).prop('selected', true);
    $('#valueRisk').val(
      data.percentage.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

    sessionStorage.setItem('percentage', data.percentage);
    sessionStorage.setItem('salary', data.salary);

    if (data.type_contract == 'Nomina')
      $(`#typeFactor option[value=1]`).prop('selected', true);
    else if (data.type_contract == 'Servicios')
      $(`#typeFactor option[value=2]`).prop('selected', true);
    else if (data.type_contract == 'Manual')
      $(`#typeFactor option[value=3]`).prop('selected', true);

    $('#typeFactor').change();

    // Animar el desplazamiento
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Verificar datos */
  const checkDataPayroll = async (url, idPayroll) => {
    const fields = {
      firstname: $("#firstname").val().trim(),
      lastname: $("#lastname").val().trim(),
      position: $("#position").val().trim(),
      idArea: parseFloat($("#idArea").val()),
      idProcess: parseFloat($("#idProcess").val()),
      idMachine: parseFloat($("#idMachine").val()),
    };

    // Verificación de campos vacíos o inválidos
    const hasEmptyField = Object.values(fields).some(
      (field) =>
        field === "" ||
        field === null ||
        (typeof field === "number" && (isNaN(field) || field <= 0))
    );

    if (hasEmptyField) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataPayroll = new FormData(formCreatePayroll);

    let process = parseFloat($('#idProcess').val());
    let machine = parseFloat($('#idMachine').val());
    let salary = parseFloat($('#basicSalary').val());
    let transport = parseFloat($('#transport').val());
    let endowment = parseFloat($('#endowment').val());
    let extraTime = parseFloat($('#extraTime').val());
    let bonification = parseFloat($('#bonification').val());
    let factor = parseFloat($('#factor').val());
    let risk = parseFloat($('#risk').val());
 
    basicSalary = salary;

    isNaN(transport) ? (transport = 0) : transport;
    isNaN(endowment) ? (endowment = 0) : endowment;
    isNaN(extraTime) ? (extraTime = 0) : extraTime;
    isNaN(bonification) ? (bonification = 0) : bonification;
    isNaN(factor) ? (factor = 0) : factor;

    let workingHD = $('#workingHoursDay').val();
    let workingDM = $('#workingDaysMonth').val();
    let valueRisk = parseFloat(sessionStorage.getItem('percentage'));

    let data = process * machine * workingDM * workingHD * salary * risk;

    if (isNaN(data) || data <= 0 || factor == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (workingDM > 31 || workingHD > 24) {
      toastr.error(
        'El campo dias trabajo x mes debe ser menor a 31, y horas trabajo x dia menor a 24'
      );
      return false;
    }

    $('#factor').prop('disabled', false);
    dataPayroll.append('basicSalary', basicSalary);
    dataPayroll.append('transport', transport);
    dataPayroll.append('endowment', endowment);
    dataPayroll.append('extraTime', extraTime);
    dataPayroll.append('bonification', bonification);
    dataPayroll.append('factor', factor);
    dataPayroll.append('valueRisk', valueRisk);

    salary = parseFloat(
      sessionStorage.getItem('salary') || $('#basicSalary').val()
    );
  
    dataPayroll.append('salary', salary);

    // Preparación de datos
    if (idPayroll) dataPayroll.append("idPayroll", idPayroll);

    // Envío de datos y manejo de respuesta
    let resp = await sendDataPOST(url, dataPayroll);
    messagePayroll(resp);
  };

  /* Eliminar Nomina */
  deletePayrollFunction = () => {
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblEmployees.fnGetData(row);

    const { id_plan_payroll } = data;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta nomina? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.get(
            `/api/deletePayroll/${id_plan_payroll}`,
            function (data, textStatus, jqXHR) {
              messagePayroll(data);
            }
          );
        }
      },
    });
  };

  // Estado empleado
  $(document).on("click", ".statusPY", function () {
    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblEmployees.fnGetData(row);

    bootbox.confirm({
      title: "Nomina",
      message: `Está seguro de cambiar el estado ${
        data.status == "1" ? "a <b>No Disponible</b>" : "a <b>Disponible</b>"
      } este empleado?`,
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
          $.get(
            `/api/changeStatusPayroll/${data.id_plan_payroll}/${
              data.status == "0" ? "1" : "0"
            }`,
            function (data, textStatus, jqXHR) {
              messagePayroll(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */

  messagePayroll = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportEmployees").hide(800);
      $('#createPayroll').modal('hide');

      $("#formImportEmployees, #formCreatePayroll").trigger("reset");
      sessionStorage.removeItem('percentage');
      sessionStorage.removeItem('salary');
      sessionStorage.removeItem('type_salary');

      $('#factor').prop('disabled', true);
      toastr.success(message);
      updateTable();
      return false;
    } else if (error == true) toastr.error(message);
    else if (info == true) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblEmployees").DataTable().clear();
    $("#tblEmployees").DataTable().ajax.reload();
  }
});
