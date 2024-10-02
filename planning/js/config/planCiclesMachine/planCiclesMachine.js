$(document).ready(function () {
  $(".cardCreatePlanCiclesMachine").hide();
  $(".cardSaveAlternalMachine").hide();

  //Abrir Card crear plan de ciclos maquina
  $("#btnNewPlanCiclesMachine").click(function (e) {
    e.preventDefault();

    $(".cardImportPlanCiclesMachine").hide(800);
    $(".cardSaveAlternalMachine").hide(800);
    $(".cardCreatePlanCiclesMachine").toggle(800);
    $("#btnCreatePlanCiclesMachine").text("Crear");
    $('.selectMachines').show();

    sessionStorage.removeItem("id_cicles_machine");

    $("#formCreatePlanCiclesMachine").trigger("reset");
  });

  //Crear plan ciclos maquina
  $("#btnCreatePlanCiclesMachine").click(function (e) {
    e.preventDefault();

    const id_cicles_machine =
      sessionStorage.getItem("id_cicles_machine") || null;
    const apiUrl = id_cicles_machine
      ? "/api/updatePlanCiclesMachine"
      : "/api/addPlanCiclesMachine";

    checkPlanCiclesMachine(apiUrl, id_cicles_machine);
  });

  //Actualizar plan ciclo maquina
  $(document).on("click", ".updatePCMachine", function (e) {
    $(".cardCreatePlanCiclesMachine").show(800);
    $(".cardSaveAlternalMachine").hide();
    $("#btnCreatePlanCiclesMachine").text("Actualizar");

    // Obtener el ID del elemento
    const id_cicles_machine = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_cicles_machine", id_cicles_machine);

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblPlanCiclesMachine.fnGetData(row);

    $(`#idProcess option[value=${data.id_process}]`).prop("selected", true);
    $(`#idMachine option[value=${data.id_machine}]`).prop("selected", true);
    $("#ciclesHour").val(data.cicles_hour.toLocaleString("es-CO"));

    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  }); 

  // Eliminar plan ciclo maquina

  deleteMachine = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblPlanCiclesMachine.fnGetData(row);

    let dataPlanCiclesMachine = {};
    dataPlanCiclesMachine['idCiclesMachine']= data.id_cicles_machine;
    dataPlanCiclesMachine['idProduct']= data.id_product;

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
          $.post('/api/deletePlanCiclesMachine', dataPlanCiclesMachine,
            function (data, textStatus, jqXHR) {
              messageMachine(data);
            },
          );
        }
      },
    });
  }; 

  // Maquina alterna
  $(document).on('click', '.alternateMachine', function () {
    $('#formSaveAlternalMachine').trigger('reset');
    $(".cardCreatePlanCiclesMachine").hide(800);
    $('.cardSaveAlternalMachine').show(800);
    $('.inputsAlternalUnds').hide();

    // Obtener el ID del elemento
    const id_cicles_machine = $(this).attr("id").split("-")[1];
    sessionStorage.setItem("id_cicles_machine", id_cicles_machine); 

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblPlanCiclesMachine.fnGetData(row);

    if (data.id_alternate_machine != 0) {
      $('.inputsAlternalUnds').show();

      $(`#idMachine1 option[value=${data.id_alternate_machine}]`).prop("selected", true);
      $('#ciclesHour1').val(data.alternate_cicles_hour);
      $('#unitsTurn').val(parseFloat(data.alternate_units_turn).toLocaleString('es-CO', {
        minimumFractionDigits: 0, maximumFractionDigits: 2
      }));
      $('#unitsMonth').val(parseFloat(data.alternate_units_month).toLocaleString('es-CO', {
        minimumFractionDigits: 0, maximumFractionDigits: 2
      }));
    }  


    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $("#btnSaveAlternalMachine").click(async function (e) {
    e.preventDefault();
 
    const idMachine = parseInt($("#idMachine").val());
    const idMachine1 = parseInt($("#idMachine1").val());
    const ciclesHour = $("#ciclesHour1").val();

    let data = idMachine * ciclesHour;

    if (!data || isNaN(idMachine)) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (idMachine == idMachine1) {
      toastr.error("Seleccione una maquina diferente");
      return false;
    }

    let idCiclesMachine = sessionStorage.getItem('id_cicles_machine');

    let dataPlanCiclesMachine = new FormData(formSaveAlternalMachine);
 
    dataPlanCiclesMachine.append("idCiclesMachine", idCiclesMachine);

    let resp = await sendDataPOST('/api/saveAlternalMachine', dataPlanCiclesMachine);

    messageMachine(resp);
  }); 

  /* Mensaje de exito */
  messageMachine = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreatePlanCiclesMachine, .cardImportPlanCiclesMachine, .cardSaveAlternalMachine").hide(800);
      $("#formCreatePlanCiclesMachine, #formSaveAlternalMachine, #formImportPlanCiclesMachine").trigger(
        "reset"
      );
      if ($("#selectNameProduct").val()) updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblPlanCiclesMachine").DataTable().clear();
    $("#tblPlanCiclesMachine").DataTable().ajax.reload();
    $("#tblRoutes").DataTable().clear();
    $("#tblRoutes").DataTable().ajax.reload();
  }

  loadDataMachines(1);
});
