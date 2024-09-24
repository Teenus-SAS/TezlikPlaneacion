$(document).ready(function () {
  sessionStorage.removeItem("businessDays");

  let date = new Date();
  let arr = [];

  //Abrir modal crear plan de maquinas
  $("#btnNewPlanMachine").click(function (e) {
    e.preventDefault();

    $("#createPlanMachine").modal("show");
    $("#btnCreatePlanMachine").text("Crear");

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

    checkDataPlanningMachines(apiUrl, id_planning_machine);
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
    $(`#typePM`).val(data.type_program_machine).prop("selected", true);
    $("#numberWorkers").val(data.number_workers);
    $("#workShift").val(data.work_shift);
    $("#hoursDay").val(data.hours_day); 

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

    // Desplazamiento parte superior
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataPlanningMachines = async (url, idProgramMachine) => {
    const typePM = parseInt($("#typePM").val());
    const idMachine = parseInt($("#idMachine").val());
    const numberWorkers = parseInt($("#numberWorkers").val());
    const workShift = parseInt($("#workShift").val());
    const hoursDay = parseInt($("#hoursDay").val());

    const data = idMachine * numberWorkers * workShift * hoursDay;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataPlanningMachines = new FormData(formCreatePlanMachine);

    if (idProgramMachine){
      dataPlanningMachines.append("idProgramMachine", idProgramMachine); 
    }

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

  // Estado empleado
  $(document).on("click", ".statusPM", function () {
    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblPlanMachines.fnGetData(row);

    bootbox.confirm({
      title: "Programacion Maquina",
      message: `Está seguro de cambiar el estado ${
        data.status == "1" ? "a <b>No Disponible</b>" : "a <b>Disponible</b>"
      } de esta maquina?`,
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
            `/api/changeStatusPMachine/${data.id_program_machine}/${
              data.status == "0" ? "1" : "0"
            }`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $("#createPlanMachine").modal("hide");
      $(".cardImportPlanMachines").hide(800);
      $("#formImportPlanMachines, #formCreatePlanMachine").trigger("reset");
      $('.cardWarningPM').show();

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
