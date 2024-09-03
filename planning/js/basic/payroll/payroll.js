$(document).ready(function () {
  /* Ocultar panel para crear Nomina */
  $(".cardCreateEmployee").hide();

  /* Abrir panel para crear Nominas */

  $("#btnNewEmployee").click(function (e) {
    e.preventDefault();
    $(".cardImportEmployees").hide(800);
    $(".cardCreateEmployee").toggle(800);
    $("#btnCreateEmployee").text("Crear");

    sessionStorage.removeItem("id_plan_payroll");

    $("#formCreateEmployee").trigger("reset");
  });

  /* Crear nomina */
  $("#btnCreateEmployee").click(function (e) {
    e.preventDefault();

    let idPayroll = sessionStorage.getItem("id_plan_payroll") || null;
    const url = idPayroll ? "/api/updatePayroll" : "/api/addPayroll";
    checkDataPayroll(url, idPayroll);
  });

  /* Actualizar nomina */
  $(document).on("click", ".updatePayroll", function (e) {
    $(".cardImportEmployees").hide(800);
    $(".cardCreateEmployee").show(800);
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
    $("#position").val(data.position);

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

    // Preparación de datos
    let dataArea = new FormData(formCreateEmployee);
    if (idPayroll) dataArea.append("idPayroll", idPayroll);

    // Envío de datos y manejo de respuesta
    let resp = await sendDataPOST(url, dataArea);
    messagePayroll(resp);
  };

  /* Eliminar areas */
  deletePayrollFunction = () => {
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblEmployees.fnGetData(row);

    const { id_plan_payroll } = data;
    //let id_plan_payroll = data.id_plan_payroll;

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
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportEmployees, .cardCreateEmployee").hide(800);
      $("#formImportEmployees, #formCreateEmployee").trigger("reset");

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
