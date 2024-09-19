$(document).ready(function () {
  /* Ocultar panel crear producto */

  $(".cardCreateProcess").hide();

  /* Abrir panel crear producto */

  $("#btnNewProcess").click(function (e) {
    e.preventDefault();

    $(".cardImportProcess").hide(800);
    $(".cardCreateProcess").toggle(800);
    $("#btnCreateProcess").text("Crear");

    sessionStorage.removeItem("id_process");

    $("#process").val("");
  });

  /* Crear nuevo proceso */
  $("#btnCreateProcess").click(function (e) {
    e.preventDefault();

    const idProcess = sessionStorage.getItem("id_process") || null;
    const apiUrl = idProcess ? "/api/updatePlanProcess" : "/api/addPlanProcess";

    checkDataProcess(apiUrl, idProcess);
  });

  /* Actualizar procesos */

  $(document).on("click", ".updateProcess", function (e) {
    $(".cardImportProcess").hide(800);
    $(".cardCreateProcess").show(800);
    $("#btnCreateProcess").text("Actualizar");

    // Obtener data
    let row = $(this).closest("tr")[0];
    let data = tblProcess.fnGetData(row);
    
    sessionStorage.setItem("id_process", data.id_process);
    // Asignar valores a los campos del formulario y animar
    $("#process").val(data.process);
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data procesos */
  const checkDataProcess = async (url, idProcess) => {
    let process = $("#process").val();

    if (!process.trim()) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataProcess = new FormData(formCreateProcess);

    if (idProcess) dataProcess.append("idProcess", idProcess);
    let resp = await sendDataPOST(url, dataProcess);
    messageProcess(resp);
  };

  /* Eliminar proceso */

  deleteProcessFunction = () => {
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblProcess.fnGetData(row);

    const { id_process } = data;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este proceso? Esta acción no se puede reversar.",
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
            `/api/deletePlanProcess/${id_process}`,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageProcess = (data) => {
    const { success, error, info, message } = data;
    if (success == true) {
      $(".cardImportProcess, .cardCreateProcess").hide(800);
      $("#formImportProcess, #formCreateProcess").trigger("reset");

      updateTable();
      toastr.success(message);
      return false;
    } else if (error == true) toastr.error(message);
    else if (info == true) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblProcess").DataTable().clear();
    $("#tblProcess").DataTable().ajax.reload();
  }
});
