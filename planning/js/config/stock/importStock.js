$(document).ready(function () {
  let selectedFile;

  $('.cardImportStock').hide();

  $('#btnImportNewStock').click(function (e) {
    e.preventDefault();
    $('.cardCreateStock').hide(800);
    $('.cardImportStock').toggle(800);
  });

  $('#fileStock').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportStock').click(function (e) {
    e.preventDefault();

    file = $('#fileStock').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let StockToImport = data.map((item) => {
          return {
            Stock: item.proceso,
          };
        });
        checkStock(StockToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkStock = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/StockDataValidation',
      data: { importStock: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportStock').trigger('reset');
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
              saveStockTable(data);
            } else $('#fileStock').val('');
          },
        });
      },
    });
  };

  saveStockTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addPlanStock',
      data: { importStock: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportStock').hide(800);
          $('#formImportStock').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblStock').DataTable().clear();
          $('#tblStock').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsStock').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Stock.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
