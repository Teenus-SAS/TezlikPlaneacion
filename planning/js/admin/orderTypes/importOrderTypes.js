$(document).ready(function () {
  let selectedFile;

  $(".cardImportOrderTypes").hide();

  $("#btnImportNewOrderTypes").click(function (e) {
    e.preventDefault();
    $(".cardCreateOrderType").hide(800);
    $(".cardImportOrderTypes").toggle(800);
  });

  $("#fileOrderTypes").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportOrderTypes").click(function (e) {
    e.preventDefault();

    file = $("#fileOrderTypes").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let orderTypesToImport = data.map((item) => {
          return {
            orderType: item.tipo_pedido,
          };
        });
        checkOrderTypes(orderTypesToImport);
      })
      .catch(() => {
        toastr.error("Ocurrio un error. Intente Nuevamente");
      });
  });

  /* Mensaje de advertencia */
  checkOrderTypes = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/orderTypesDataValidation",
      data: { importOrderTypes: data },
      success: function (resp) {
        if (resp.error == true) {
          $("#formImportOrderTypes").trigger("reset");
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
              saveClientTable(data);
            } else $("#fileOrderTypes").val("");
          },
        });
      },
    });
  };

  saveClientTable = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/addOrderTypes",
      data: { importOrderTypes: data },
      success: function (data) {
        /* Mensaje de exito */
        const { success, error, info, message } = data;
        if (success) {
          $(".cardImportOrderTypes").hide(800);
          $("#formImportOrderTypes").trigger("reset");
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);

        /* Actualizar tabla */
        function updateTable() {
          $("#tblOrderTypes").DataTable().clear();
          $("#tblOrderTypes").DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsOrderTypes").click(function (e) {
    e.preventDefault();

    url = "assets/formatsXlsx/Tipo_Pedidos.xlsx";

    link = document.createElement("a");

    link.target = "_blank";

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
