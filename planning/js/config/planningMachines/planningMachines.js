$(document).ready(function () {
  sessionStorage.removeItem("businessDays");

  let date = new Date();
  let arr = [];

  //Abrir modal crear plan de maquinas
  $("#btnNewPlanMachine").click(function (e) {
    e.preventDefault();

    $("#createPlanMachine").modal("show");
    $("#btnCreatePlanMachine").html("Crear");

    sessionStorage.removeItem("id_planning_machine");

    $(".month").css("border-color", "");
    $("#formCreatePlanMachine").trigger("reset");

    // Mostrar dias habiles x mes
    for (i = 1; i <= 12; i++) {
      month = new Date(date.getFullYear(), i, 0);
      lastDay = month.getDate();

      businessDays = getBusinessDays(lastDay, i - 1);
      arr[i] = businessDays;

      $(`#month-${i}`).val(businessDays);
    }
    businessDays = JSON.stringify(arr);
    sessionStorage.setItem("businessDays", businessDays);
  });

  //Ocultar modal Plan maquinas
  $("#btnClosePlanMachine").click(function (e) {
    e.preventDefault();
    $("#createPlanMachine").modal("hide");
  });

  //Crear Plan maquinas
  $("#btnCreatePlanMachine").click(function (e) {
    e.preventDefault();
    const id_planning_machine =
      sessionStorage.getItem("id_planning_machine") || null;

    const apiUrl = id_planning_machine
      ? "/api/updatePlanningMachines"
      : "/api/addPlanningMachines";

    checkDataRequisition(apiUrl, id_planning_machine);
  });

  //Actualizar Plan maquina
  $(document).on("click", ".updatePMachines", function (e) {
    // Mostrar modal y actualizar botón
    $("#createPlanMachine").modal("show");
    $("#btnCreatePlanMachine").text("Actualizar");

    // Obtener el ID del elemento
    let id_planning_machine = $(this).attr("id").split("-")[1];
    sessionStorage.setItem("id_planning_machine", id_planning_machine);

    // Obtener data
    let row = $(this).closest("tr")[0];
    let data = tblPlanMachines.fnGetData(row);

    // Asignar valores
    $(`#idMachine`).val(data.id_machine).prop("selected", true);
    $("#numberWorkers").val(data.number_workers);
    $("#hoursDay").val(data.hours_day);

    // Formatear horas
    /* hourStart = moment(data.hour_start.toFixed(2), ["HH:mm"]).format("h:mm A");
    hourEnd = moment(data.hour_end.toFixed(2), ["HH:mm"]).format("h:mm A");
    $("#hourStart").val(hourStart);
    $("#hourEnd").val(hourEnd); */

    const hourFormat = ["HH:mm"];
    $("#hourStart").val(
      moment(data.hour_start.toFixed(2), hourFormat).format("h:mm A")
    );
    $("#hourEnd").val(
      moment(data.hour_end.toFixed(2), hourFormat).format("h:mm A")
    );

    // Asignar año y meses
    $("#year").val(data.year);

    // Asignar los meses
    for (let i = 1; i <= 12; i++)
      $(`#month-${i}`).val(
        data[
          `${moment()
            .month(i - 1)
            .format("MMMM")
            .toLowerCase()}`
        ]
      );

    /* $("#month-1").val(data.january);
    $("#month-2").val(data.february);
    $("#month-3").val(data.march);
    $("#month-4").val(data.april);
    $("#month-5").val(data.may);
    $("#month-6").val(data.june);
    $("#month-7").val(data.july);
    $("#month-8").val(data.august);
    $("#month-9").val(data.september);
    $("#month-10").val(data.october);
    $("#month-11").val(data.november);
    $("#month-12").val(data.december); */

    // Desplazamiento parte superior
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataPlanningMachines = async (url, idProgramMachine) => {
    const idMachine = parseInt($("#idMachine").val());
    const numberWorkers = parseInt($("#numberWorkers").val());
    const hoursDay = parseInt($("#hoursDay").val());

    const data = idMachine * numberWorkers * hoursDay;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataPlanningMachines = new FormData(formCreatePlanMachine);

    if (idProgramMachine)
      dataPlanningMachines.append("idProgramMachine", idProgramMachine);

    let resp = await sendDataPOST(url, dataPlanningMachines);

    message(resp);
  };

  // Eliminar Plan maquina

  deleteFunction = () => {
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblPlanMachines.fnGetData(row);

    let id_program_machine = data.id_program_machine;
    let id_machine = data.id_machine;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta maquina? Esta acción no se puede reversar.",
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
            `/api/deletePlanningMachines/${id_program_machine}/${id_machine}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $("#createPlanMachine").modal("hide");
      $(".cardImportPlanMachines").hide(800);
      $("#formImportPlanMachines, #formCreatePlanMachine").trigger("reset");

      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblPlanMachines").DataTable().clear();
    $("#tblPlanMachines").DataTable().ajax.reload();
  }

  loadDataMachines(2);
});
