$(document).ready(function () {
  let idProduct;
  /* Ocultar panel crear producto */

  $(".cardAddProcess").hide();

  /* Abrir panel crear producto */

  $("#btnCreateProcess").click(function (e) {
    e.preventDefault();

    $(".cardImportProductsProcess").hide(800);
    $(".cardAddProcess").toggle(800);
    $("#btnAddProcess").html("Asignar");

    sessionStorage.removeItem("id_product_process");
    $("#formAddProcess").trigger("reset");
  });

  /* Seleccionar producto */

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    idProduct = $("#selectNameProduct").val();
  });

  /* calcular el tiempo total proceso */

  $(document).on("click keyup", ".time", function (e) {
    let tOperation = parseFloat($("#operationTime").val());
    let tEnlistment = parseFloat($("#enlistmentTime").val());

    isNaN(tOperation) ? (tOperation = 0) : tOperation;
    isNaN(tEnlistment) ? (tEnlistment = 0) : tEnlistment;

    let val = tEnlistment + tOperation;
    // val = validateNumber(val);
    $("#totalTime").val(val);
  });

  /* Adicionar nuevo proceso */

  $("#btnAddProcess").click(function (e) {
    e.preventDefault();
    let idProductProcess = sessionStorage.getItem("id_product_process");

    if (idProductProcess == "" || idProductProcess == null) {
      idProduct = parseInt($("#selectNameProduct").val());
      refP = parseInt($("#idProcess").val());
      refM = parseInt($("#idMachine").val());

      enlisT = parseInt($("#enlistmentTime").val());
      operT = parseInt($("#operationTime").val());
      totalTime = parseInt($("#totalTime").val());

      data = idProduct * refP;

      if (!data || isNaN(refM) || totalTime == 0 || !totalTime) {
        toastr.error("Ingrese todos los campos");
        return false;
      }

      productProcess = $("#formAddProcess").serialize();

      productProcess = productProcess + "&idProduct=" + idProduct;
      $.post(
        "/api/addProductsProcess",
        productProcess,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateProcess();
    }
  });

  /* Actualizar productos Procesos */

  $(document).on("click", ".updateProcess", function (e) {
    $(".cardImportProductsProcess").hide(800);
    $(".cardAddProcess").show(800);
    $("#btnAddProcess").text("Actualizar");

    //Obtener data
    let row = $(this).closest("tr")[0];
    let data = tblConfigProcess.fnGetData(row);

    sessionStorage.setItem("id_product_process", data.id_product_process);

    $(`#idProcess option[value=${data.id_process}]`).prop("selected", true);

    data.id_machine == null ? (data.id_machine = 0) : data.id_machine;
    $(`#idMachine option[value=${data.id_machine}]`).prop("selected", true);

    enlistment_time = validateNumber(data.enlistment_time);
    $("#enlistmentTime").val(enlistment_time);

    operation_time = validateNumber(data.operation_time);
    $("#operationTime").val(operation_time);

    $("#enlistmentTime").click();

    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateProcess = () => {
    let data = $("#formAddProcess").serialize();
    idProduct = $("#selectNameProduct").val();
    idProductProcess = sessionStorage.getItem("id_product_process");

    data =
      data +
      "&idProductProcess=" +
      idProductProcess +
      "&idProduct=" +
      idProduct;

    $.post(
      "../../api/updatePlanProductsProcess",
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar proceso */

  deleteFunction = () => {
    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblConfigProcess.fnGetData(row);

    let idProductProcess = data.id_product_process;

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
            `/api/deletePlanProductProcess/${idProductProcess}`,
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
      // $('.cardCreateRawProcesss').toggle(800);
      $(".cardAddProcess").hide(800);
      $("#formAddProcess").trigger("reset");
      updateTable();
      toastr.success(message);
      //return false
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblConfigProcess").DataTable().clear();
    $("#tblConfigProcess").DataTable().ajax.reload();
  }

  loadDataMachines(1);
});
