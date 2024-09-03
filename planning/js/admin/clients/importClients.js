$(document).ready(function () {
  let selectedFile;

  $('.cardImportClients').hide();

  $('#btnImportNewClient').click(function (e) {
    e.preventDefault();
    $('.cardImportClients').toggle(800);
  });

  $('#fileClients').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportClients').click(function (e) {
    e.preventDefault();

    file = $('#fileClients').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formClients');
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
        const expectedHeaders = ['nit', 'cliente', 'direccion', 'telefono', 'ciudad','tipo'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileClients').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let ClientsToImport = data.map((item) => {
          return {
            nit: item.nit,
            client: item.cliente,
            address: item.direccion,
            phone: item.telefono,
            city: item.ciudad,
            type: item.tipo,
          };
        });
        checkClients(ClientsToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileClients').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkClients = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/clientsDataValidation',
      data: { importClients: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileClients').val('');
          
          $('#formImportClients').trigger('reset');
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
              saveClientTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileClients').val('');
            }
          },
        });
      },
    });
  };

  saveClientTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addClient',
      data: { importClients: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileClients').val('');
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsClients').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Clientes.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
