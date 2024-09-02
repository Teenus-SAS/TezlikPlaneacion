$(document).ready(function () {
  let selectedFile;

  $(".cardImportCategories").hide();

  $("#btnImportNewCategories").click(function (e) {
    e.preventDefault();
    $(".cardCreateCategory").hide(800);
    $(".cardImportCategories").toggle(800);
  });

  $("#fileCategory").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportCategories").click(function (e) {
    e.preventDefault();

    file = $("#fileCategory").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let CategoriesToImport = data.map((item) => {
          return {
            category: item.categoria,
            typeCategory: item.tipo_categoria,
          };
        });
        checkCategory(CategoriesToImport);
      })
      .catch(() => {
        toastr.error("Ocurrio un error. Intente Nuevamente");
      });
  });

  /* Mensaje de advertencia */
  checkCategory = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/categoriesDataValidation",
      data: { importCategories: data },
      success: function (resp) {
        if (resp.error == true) {
          $("#formImportCategory").trigger("reset");
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
              saveCategoryTable(data);
            } else $("#fileCategory").val("");
          },
        });
      },
    });
  };

  saveCategoryTable = (data) => {
    $.ajax({
      type: "POST",
      url: "../../api/addCategory",
      data: { importCategories: data },
      success: function (data) {
        /* Mensaje de exito */
        const { success, error, info, message } = data;
        if (success) {
          $(".cardImportCategories").hide(800);
          $("#formImportCategories").trigger("reset");
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);

        /* Actualizar tabla */
        function updateTable() {
          $("#tblCategories").DataTable().clear();
          $("#tblCategories").DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsCategories").click(function (e) {
    e.preventDefault();

    url = "assets/formatsXlsx/Categorias.xlsx";

    link = document.createElement("a");

    link.target = "_blank";

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
