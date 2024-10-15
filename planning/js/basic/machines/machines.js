$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    let option = this.id;

    const sections = {
      "link-process": ".cardProcess",
      "link-machines": ".cardMachines",
      "link-areas": ".cardAreas",
    };

    // Ocultar todas las secciones
    $(
      ".cardMachines, .cardCreateMachines, .cardImportMachines, .cardProcess, .cardCreateProcess, .cardImportProcess, .cardAreas, .cardCreateArea, .cardImportAreas"
    ).hide();

    // Mostrar la sección correspondiente según la opción seleccionada
    $(sections[option] || "").show();

    let tables = document.getElementsByClassName("dataTable");

    for (let table of tables) { 
      table.style.width = "1217.55px"; 
      table.style.width = "1217.55px";
    }
  });

  /* Ocultar panel para crear Machines */
  $(".cardCreateMachines").hide();

  /* Abrir panel para crear Machines */
  $("#btnNewMachine").click(function (e) {
    e.preventDefault();
    $(".cardImportMachines").hide(800);
    $(".cardCreateMachines").toggle(800);
    $("#btnCreateMachine").text("Crear");

    sessionStorage.removeItem("id_machine");

    $("#formCreateMachine").trigger("reset");
  });

  /* Crear producto */
  $("#btnCreateMachine").click(function (e) {
    e.preventDefault();

    const idMachine = sessionStorage.getItem("id_machine") || null;
    const apiUrl = idMachine
      ? "/api/updatePlanMachines"
      : "/api/addPlanMachines";

    checkDataMachines(apiUrl, idMachine);
  });

  /* Actualizar maquinas */

  $(document).on("click", ".updateMachines", function (e) {
    $(".cardImportMachines").hide(800);
    $(".cardCreateMachines").show(800);
    $("#btnCreateMachine").text("Actualizar");

    // Obtener el ID del elemento
    const idMachine = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_machine", idMachine);

    // Obtener data
    const row = $(this).closest("tr")[0];
    const data = tblMachines.fnGetData(row);

    // Asignar valores a los campos del formulario y animar
    $("#machine").val(data.machine);
    $('#costMachine').val(data.cost); 
    $('#depreciationYears').val(data.years_depreciation);
    $('#hoursMachine').val(data.hours_machine);
    $('#daysMachine').val(data.days_machine);
    
    $('#ciclesMachine').val(data.cicles_machine);
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  const checkDataMachines = async (url, idMachine) => {
    let machine = $("#machine").val();
    let costMachine = parseFloat($('#costMachine').val());
    let depreciationYears = parseFloat($('#depreciationYears').val());
    let hoursMachine = parseFloat($('#hoursMachine').val());
    let daysMachine = parseFloat($('#daysMachine').val());

    let data = costMachine * depreciationYears * hoursMachine * daysMachine;

    if (!machine.trim() || isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataMachine = new FormData(formCreateMachine);

    if (idMachine) dataMachine.append("idMachine", idMachine);
    let resp = await sendDataPOST(url, dataMachine);
    messageMachine(resp);
  };

  /* Eliminar productos */
  deleteMachineFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    const data = tblMachines.fnGetData(row);

    const { id_machine } = data;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta máquina? Esta acción no se puede reversar.",
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
            `/api/deletePlanMachine/${id_machine}`,
            function (data, textStatus, jqXHR) {
              messageMachine(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMachine = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportMachines, .cardCreateMachines").hide(800);
      $("#formImportMachines, #formCreateMachine").trigger("reset");

      toastr.success(message);
      updateTable();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblMachines").DataTable().clear();
    $("#tblMachines").DataTable().ajax.reload();
  }
});
