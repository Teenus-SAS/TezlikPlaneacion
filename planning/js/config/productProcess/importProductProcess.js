$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsProcess').hide();

  $('#btnImportNewProductProcess').click(function (e) {
    e.preventDefault();
    $('.cardAddProcess').hide(800);
    $('.cardImportProductsProcess').toggle(800);
  });

  $('#fileProductsProcess').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProductsProcess').click(function (e) {
    e.preventDefault();

    file = $('#fileProductsProcess').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let productProcessToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            process: item.proceso,
            machine: item.maquina,
            enlistmentTime: item.tiempo_enlistamiento,
            operationTime: item.tiempo_operacion,
          };
        });
        checkProductProcess(productProcessToImport);
      })
      .catch(() => {
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProductProcess = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsProcessDataValidation',
      data: { importProductsProcess: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportProductProcess').trigger('reset');
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
            if (result) {
              saveProductProcessTable(data);
            } else $('#fileProductsProcess').val('');
          },
        });
      },
    });
  };

  saveProductProcessTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProductsProcess',
      data: { importProductsProcess: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportProductsProcess').hide(800);
          $('#formImportProductProcess').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblConfigProcess').DataTable().clear();
          $('#tblConfigProcess').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsProcess').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Productos_Procesos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
