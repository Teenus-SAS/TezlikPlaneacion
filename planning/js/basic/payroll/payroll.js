$(document).ready(function () {  
  /* Ocultar panel para crear Nomina */
  $(".cardCreateEmployee").hide();

  /* Abrir panel para crear Nominas */

  $("#btnNewEmployee").click(function (e) {
    e.preventDefault();
    $('.cardImportEmployees').hide(800);
    $(".cardCreateEmployee").toggle(800);
    $("#btnCreateEmployee").html("Crear");

    sessionStorage.removeItem("id_plan_payroll");

    $("#formCreateEmployee").trigger("reset");
  });

  /* Crear nomina */

  $("#btnCreateEmployee").click(function (e) {
    e.preventDefault();
    let idPayroll = sessionStorage.getItem("id_plan_payroll");
    if (idPayroll == "" || idPayroll == null) {
      checkDataPayroll("/api/addPayroll", idPayroll);
    } else {
      checkDataPayroll("/api/updatePayroll", idPayroll);
    }
  });

  /* Actualizar nomina */

  $(document).on("click", ".updatePayroll", function (e) {
    $('.cardImportEmployees').hide(800);
    $(".cardCreateEmployee").show(800);
    $("#btnCreateEmployee").html("Actualizar");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idPayroll = id.split('-')[1]; 

    sessionStorage.setItem("id_plan_payroll", idPayroll);

    let row = $(this).parent().parent()[0];
    let data = tblEmployees.fnGetData(row);

    $("#firstname").val(data.firstname);
    $("#lastname").val(data.lastname);
    $(`#idArea option[value=${data.id_plan_area}]`).prop('selected', true);
    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);
    $("#position").val(data.position);

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  const checkDataPayroll = async (url, idPayroll) => {
    let firstname = $("#firstname").val();
    let lastname = $("#lastname").val();
    let idArea = parseFloat($("#idArea").val());
    let idProcess = parseFloat($("#idProcess").val());
    let position = $("#position").val();

    if (
      firstname.trim() == "" || firstname.trim() == null ||
      lastname.trim() == "" || lastname.trim() == null ||
      position.trim() == "" || position.trim() == null ||
      isNaN(idArea) || idArea <= 0 ||
      isNaN(idProcess) || idProcess <= 0
    ) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataArea = new FormData(formCreateEmployee);

    if (idPayroll != "" || idPayroll != null)
      dataArea.append("idPayroll", idPayroll);

    let resp = await sendDataPOST(url, dataArea);

    messagePayroll(resp);
  };

  /* Eliminar areas */
  deletePayrollFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblEmployees.fnGetData(row);

    let id_plan_payroll = data.id_plan_payroll;

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

  /* Mensaje de exito */

  messagePayroll = (data) => {
    if (data.success == true) {
      $(".cardImportEmployees").hide(800);
      $("#formImportEmployees").trigger("reset");
      $(".cardCreateEmployee").hide(800);
      $("#formCreateEmployee").trigger("reset");
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblEmployees").DataTable().clear();
    $("#tblEmployees").DataTable().ajax.reload();
  }
});
