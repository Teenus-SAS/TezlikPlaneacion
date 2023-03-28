$(document).ready(function () {
  let selectedFile;

  $('.cardImportCategories').hide();

  $('#btnImportNewCategories').click(function (e) {
    e.preventDefault();
    $('.cardCreateCategory').hide(800);
    $('.cardImportCategories').toggle(800);
  });

  $('#fileCategory').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportCategories').click(function (e) {
    e.preventDefault();

    file = $('#fileCategory').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
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
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkCategory = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/categoriesDataValidation',
      data: { importCategories: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportCategory').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
          buttons: {
            confirm: {
              label: 'Si',
              className: 'btn-success',
            },
            cancel: {
              label: 'No',
              className: 'btn-danger',
            },
          },
          callback: function (result) {
            if (result == true) {
              saveCategoryTable(data);
            } else $('#fileCategory').val('');
          },
        });
      },
    });
  };

  saveCategoryTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addCategory',
      data: { importCategories: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportCategories').hide(800);
          $('#formImportCategories').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblCategories').DataTable().clear();
          $('#tblCategories').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsCategories').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Categorias.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
