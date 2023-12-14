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

    $('.cardBottons').hide();

    let form = document.getElementById('formProducts');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {

        const expectedHeaders = ['referencia_producto', 'producto', 'existencias'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let productsToImport = data.map((item) => {          
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto, 
            quantity: item.existencias, 
          };
        });
        checkProduct(productsToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProducts').val('');
        toastr.error('Ocurrio un error. Intente Nuevamente');
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
          $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProducts').val('');
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
            } else {
              $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProducts').val(''); 
            }
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
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProducts').val('');
        message(r);
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
 
});
