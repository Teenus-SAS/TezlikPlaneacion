$(document).ready(function () {
  let selectedFile;

  $('.cardImportSellers').hide();

  $('#btnImportNewSeller').click(function (e) {
    e.preventDefault();
    $('.cardImportSellers').toggle(800);
    $('.cardCreateSeller').hide(800);
  });

  $('#fileSellers').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportSellers').click(function (e) {
    e.preventDefault();

    file = $('#fileSellers').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formSellers');
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
        const expectedHeaders = ['nombre', 'apellido', 'email'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileSellers').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let sellersToImport = data.map((item) => {
          return {
            firstname: item.nombre,
            lastname: item.apellido,
            email: item.email, 
          };
        });
        checkSellers(sellersToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileSellers').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkSellers = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/sellersDataValidation',
      data: { importSellers: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileSellers').val('');
          
          $('#formImportSellers').trigger('reset');
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
              saveSellerTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileSellers').val('');
            }
          },
        });
      },
    });
  };

  const saveSellerTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addSeller',
      data: { importSellers: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileSellers').val('');
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsSellers').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Vendedores.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
