$(document).ready(function () {
  let selectedFile;

  $(".cardImportInvMold").hide();

  $("#btnImportNewInvMold").click(function (e) {
    e.preventDefault();
    $(".cardCreateInvMold").hide(800);
    $(".cardImportInvMold").toggle(800);
  });

  $("#fileInvMold").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportInvMold").click(function (e) {
    e.preventDefault();

    file = $("#fileInvMold").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let MoldsToImport = data.map((item) => {
          return {
            referenceMold: item.referencia,
            mold: item.molde,
            assemblyTime: item.tiempo_montaje,
            assemblyProduction: item.tiempo_montaje_produccion,
            cavity: item.numero_cavidades,
            cavity_available: item.cavidades_disponibles,
          };
        });
        checkMolds(MoldsToImport);
      })
      .catch(() => {
        toastr.error("Ocurrio un error. Intente Nuevamente");
      });
  });

  /* Mensaje de advertencia */
  checkMolds = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/invMoldDataValidation",
      data: { importInvMold: data },
      success: function (resp) {
        if (resp.error == true) {
          $("#formImportInvMold").trigger("reset");
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: "¿Desea continuar con la importación?",
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
              saveMoldTable(data);
            } else $("#fileInvMold").val("");
          },
        });
      },
    });
  };

  saveMoldTable = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/addMold",
      data: { importInvMold: data },
      success: function (data) {
        /* Mensaje de exito */
        const { success, error, info, message } = data;
        if (success) {
          $(".cardImportInvMold").hide(800);
          $("#formImportInvMold").trigger("reset");
          updateTable();
          toastr.success(message);
          return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);

        /* Actualizar tabla */
        function updateTable() {
          $("#tblInvMold").DataTable().clear();
          $("#tblInvMold").DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsInvMold").click(function (e) {
    e.preventDefault();

    url = "assets/formatsXlsx/Moldes.xlsx";

    link = document.createElement("a");

    link.target = "_blank";

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
