$(document).ready(function () {
  let selectedFile;

  $('.cardImportProducts').hide();

  $('#btnImportNewProducts').click(function (e) {
    e.preventDefault();
    $('.cardCreateProduct').hide(800);
    $('.cardImportProducts').toggle(800);
  });

  $('#fileProducts').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProducts').click(function (e) {
    e.preventDefault();

    file = $('#fileProducts').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let productsToImport = data.map((item) => {          
          return {
            referenceProduct: item.reference,
            product: item.producto, 
            quantity: item.existencias, 
          };
        });
        checkProduct(productsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProduct = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsDataValidation',
      data: { importProducts: data },
      success: function (resp) {
        if (resp.error == true) {
          toastr.error(resp.message);
          $('#formImportProduct').trigger('reset');
          return false;
        }
        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se encontraron los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
              saveProductTable(data);
            } else $('#fileProducts').val('');
          },
        });
      },
    });
  };

  /* Guardar Importacion */
  saveProductTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProduct',
      //data: data,
      data: { importProducts: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportProducts').hide(800);
          $('#formImportProduct').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblProducts').DataTable().clear();
          $('#tblProducts').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProducts').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Productos.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      toastr.success(data.message);
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
