$(document).ready(function () {
  /* Ocultar panel crear molde */

  $(".cardCreateInvMold").hide();

  /* Abrir panel crear molde */

  $("#btnNewInvMold").click(function (e) {
    e.preventDefault();

    $(".cardImportInvMold").hide(800);
    $(".cardCreateInvMold").toggle(800);
    $("#btnCreateInvMold").text("Crear");

    sessionStorage.removeItem("id_mold");

    $(".form-control").css("border-color", "");
    $("#formCreateInvMold").trigger("reset");
  });

  /* Crear nuevo molde */

  $("#btnCreateInvMold").click(function (e) {
    e.preventDefault();

    // Obtener el id
    const idMold = sessionStorage.getItem("id_mold");

    if (!idMold) {
      const reference = $("#referenceMold").val().trim();
      const mold = $("#mold").val().trim();
      const assemblyTime = parseFloat($("#assemblyTime").val());
      const assemblyProduction = parseFloat($("#assemblyProduction").val());
      const cavity = parseFloat($("#cavity").val());
      const cavityAvailable = parseFloat($("#cavityAvailable").val());

      // data = assemblyTime * assemblyProduction * cavity * cavityAvailable;

      // Validación de campos vacíos o inválidos
      if (
        !reference ||
        !mold ||
        isNaN(assemblyTime) ||
        assemblyTime <= 0 ||
        isNaN(assemblyProduction) ||
        assemblyProduction <= 0 ||
        isNaN(cavity) ||
        cavity <= 0 ||
        isNaN(cavityAvailable) ||
        cavityAvailable <= 0
      ) {
        toastr.error("Ingrese todos los campos");
        return false;
      }

      // Validar que las cavidades disponibles no excedan el total de cavidades
      if (cavityAvailable > cavity) {
        toastr.error(
          "Número de cavidades disponibles mayor a Número de cavidades"
        );
        $("#cavityAvailable").css("border-color", "red");
        return false;
      }

      // Enviar datos
      invMold = $("#formCreateInvMold").serialize();
      $.post("/api/addMold", invMold, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateMold();
    }
  });

  /* Actualizar moldes */

  $(document).on("click", ".updateMold", function (e) {
    $(".cardImportInvMold").hide(800);
    $(".cardCreateInvMold").show(800);
    $("#btnCreateInvMold").text("Actualizar");

    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblInvMold.fnGetData(row);

    //cargar formulario
    sessionStorage.setItem("id_mold", data.id_mold);
    $("#referenceMold").val(data.reference);
    $("#mold").val(data.mold);
    $("#assemblyTime").val(data.assembly_time.toLocaleString("es-CO"));
    $("#assemblyProduction").val(
      data.assembly_production.toLocaleString("es-CO")
    );
    $("#cavity").val(data.cavity.toLocaleString("es-CO"));
    $("#cavityAvailable").val(data.cavity_available.toLocaleString("es-CO"));

    //animar desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMold = () => {
    cavity = $("#cavity").val();
    cavityAvailable = $("#cavityAvailable").val();

    if (cavityAvailable > cavity) {
      toastr.error(
        "Número de cavidades disponibles mayor a Número de cavidades"
      );
      $("#cavityAvailable").css("border-color", "red");
      return false;
    }

    let data = $("#formCreateInvMold").serialize();
    idMold = sessionStorage.getItem("id_mold");
    data = data + "&idMold=" + idMold;

    $.post("/api/updateMold", data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar molde */
  deleteFunction = () => {
    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblInvMold.fnGetData(row);

    let id_mold = data.id_mold;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este molde? Esta acción no se puede reversar.",
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
            `/api/deleteMold/${id_mold}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Activar o Desactivar Molde */
  activeMold = (id_mold) => {
    dataMold = {};
    dataMold["idMold"] = id_mold;

    if ($(`#check-${id_mold}`).is(":checked")) {
      bootbox.confirm({
        title: "Activación",
        message: "Está seguro de activar este molde?",
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
            saveMold(dataMold);
          } else {
            $(`#check-${id_mold}`).prop("checked", false);
          }
        },
      });
    } else {
      bootbox.prompt({
        title: "Desactivación",
        message: "<p>Ingrese motivo de desactivación:</p>",
        inputType: "textarea",
        callback: function (result) {
          if (result) {
            if (!result || result == "") {
              toastr.error("Ingrese observación");
              return false;
            }
            dataMold["observationMold"] = result;
            saveMold(dataMold);
          } else {
            $(`#check-${id_mold}`).prop("checked", true);
          }
        },
      });
    }
  };

  saveMold = (data) => {
    $.post(
      "/api/activeOrInactiveMold",
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Mensaje de exito */

  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreateInvMold").hide(800);
      $("#formCreateInvMold").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblInvMold").DataTable().clear();
    $("#tblInvMold").DataTable().ajax.reload();
  }
});
