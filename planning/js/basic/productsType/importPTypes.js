$(document).ready(function () {
  let selectedFile;

  $('.cardImportPType').hide();

  $('#btnNewImportPType').click(function (e) {
    e.preventDefault();
    $('.cardCreatePType').hide(800);
    $('.cardImportPType').toggle(800);
  });

  $('#filePType').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPType').click(function (e) {
    e.preventDefault();

    file = $('#filePType').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formPType');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-grow text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        const expectedHeaders = ['tipo_producto'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePType').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let productTypeToImport = data.map((item) => {
          return {
            productType: item.tipo_producto,
          };
        });
        checkPType(productTypeToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePType').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkPType = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsTypeDataValidation',
      data: { importProducts: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePType').val('');
          $('#formImportPType').trigger('reset');
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
              saveProductsType(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#filePType').val('');
            }
          },
        });
      },
    });
  };

  const saveProductsType = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProductsTypes',
      data: { importProducts: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePType').val('');
        messagePType(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPType').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Tipos_Productos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
