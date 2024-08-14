$(document).ready(function () { 
  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    let option = this.id;
    $('.cardMachines').hide();
    $('.cardCreateMachines').hide();
    $('.cardImportMachines').hide();
    $('.cardProcess').hide();
    $('.cardCreateProcess').hide();
    $('.cardImportProcess').hide();
    $('.cardAreas').hide();
    $('.cardCreateArea').hide();
    $('.cardImportAreas').hide();

    switch (option) {
      case 'link-process':
        $('.cardProcess').show();
        break;
      case 'link-machines':
        $('.cardMachines').show();
        break;
      case 'link-areas':
        $('.cardAreas').show();
        break;
    }

    let tables = document.getElementsByClassName('dataTable');

    for (let table of tables) {
      table.style.width = '100%';
      table.firstElementChild.style.width = '100%';
    }
  });

  /* Ocultar panel para crear Machinees */
  $(".cardCreateMachines").hide();

  /* Abrir panel para crear Machinees */

  $("#btnNewMachine").click(function (e) {
    e.preventDefault();
    $('.cardImportMachines').hide(800);
    $(".cardCreateMachines").toggle(800);
    $("#btnCreateMachine").html("Crear");

    sessionStorage.removeItem("id_machine");

    $("#formCreateMachine").trigger("reset");
  });

  /* Crear producto */

  $("#btnCreateMachine").click(function (e) {
    e.preventDefault();
    let idMachine = sessionStorage.getItem("id_machine");
    if (idMachine == "" || idMachine == null) {
      checkDataMachines("/api/addPlanMachines", idMachine);
    } else {
      checkDataMachines("/api/updatePlanMachines", idMachine);
    }
  });

  /* Actualizar maquinas */

  $(document).on("click", ".updateMachines", function (e) {
    $('.cardImportMachines').hide(800);
    $(".cardCreateMachines").show(800);
    $("#btnCreateMachine").html("Actualizar");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idMachine = id.split('-')[1]; 

    sessionStorage.setItem("id_machine", idMachine);

    let row = $(this).parent().parent()[0];
    let data = tblMachines.fnGetData(row);

    $("#machine").val(data.machine);

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  const checkDataMachines = async (url, idMachine) => {
    let Machine = $("#machine").val();

    if (Machine.trim() == "" || Machine.trim() == null) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataMachine = new FormData(formCreateMachine);

    if (idMachine != "" || idMachine != null)
      dataMachine.append("idMachine", idMachine);

    let resp = await sendDataPOST(url, dataMachine);

    messageMachine(resp);
  };

  /* Eliminar productos */
  deleteMachineFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblMachines.fnGetData(row);

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
    if (data.success == true) {
      $(".cardImportMachines").hide(800);
      $("#formImportMachines").trigger("reset");
      $(".cardCreateMachines").hide(800);
      $("#formCreateMachine").trigger("reset");
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblMachines").DataTable().clear();
    $("#tblMachines").DataTable().ajax.reload();
  }
});
