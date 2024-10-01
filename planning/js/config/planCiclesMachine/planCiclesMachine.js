$(document).ready(function () {
  $(".cardCreatePlanCiclesMachine").hide();

  //Abrir Card crear plan de ciclos maquina
  $("#btnNewPlanCiclesMachine").click(function (e) {
    e.preventDefault();

    $(".cardImportPlanCiclesMachine").hide(800);
    $(".cardCreatePlanCiclesMachine").toggle(800);
    $("#btnCreatePlanCiclesMachine").text("Crear");

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

  const checkPlanCiclesMachine = async (url, idCiclesMachine) => {
    const idProcess = parseInt($("#idProcess").val());
    const idMachine = parseInt($("#idMachine").val());
    const idProduct = parseInt($("#selectNameProduct").val());
    const ciclesHour = $("#ciclesHour").val();

    let data = idProcess * idProduct * ciclesHour;

    if (!data || isNaN(idMachine)) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataPlanCiclesMachine = new FormData(formCreatePlanCiclesMachine);
    dataPlanCiclesMachine.append("idProduct", idProduct);

    if (idCiclesMachine)
      dataPlanCiclesMachine.append("idCiclesMachine", idCiclesMachine);

    let resp = await sendDataPOST(url, dataPlanCiclesMachine);

    messageMachine(resp);
  };

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

  /* Mensaje de exito */
  messageMachine = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreatePlanCiclesMachine, .cardImportPlanCiclesMachine").hide(800);
      $("#formCreatePlanCiclesMachine, #formImportPlanCiclesMachine").trigger(
        "reset"
      );
      if ($("#selectNameProduct").val()) updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  $(document).on('click', '.alternateMachine', function () {
    $('#btnCreatePlanCiclesMachine').html('');
    // Obtener el ID del elemento
    const id_cicles_machine = $(this).attr("id").split("-")[1]; 
    // sessionStorage.setItem("id_cicles_machine", id_cicles_machine);

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblPlanCiclesMachine.fnGetData(row);


    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });
  

  /* Actualizar tabla */
  function updateTable() {
    $("#tblPlanCiclesMachine").DataTable().clear();
    $("#tblPlanCiclesMachine").DataTable().ajax.reload();
    $("#tblRoutes").DataTable().clear();
    $("#tblRoutes").DataTable().ajax.reload();
  }

  loadDataMachines(1);
});
