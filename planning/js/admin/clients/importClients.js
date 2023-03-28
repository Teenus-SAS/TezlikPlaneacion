$(document).ready(function () {
  let selectedFile;

  $('.cardImportClients').hide();

  $('#btnImportNewClient').click(function (e) {
    e.preventDefault();
    $('.cardCreateClient').hide(800);
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

    importFile(selectedFile)
      .then((data) => {
        let ClientsToImport = data.map((item) => {
          return {
            ean: item.ean,
            nit: item.nit,
            client: item.cliente.trim(),
          };
        });
        checkClients(ClientsToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
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
            if (result == true) {
              saveClientTable(data);
            } else $('#fileClients').val('');
          },
        });
      },
    });
  };

  saveClientTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addClient',
      data: { importClients: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportClients').hide(800);
          $('#formImportClients').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblClients').DataTable().clear();
          $('#tblClients').DataTable().ajax.reload();
        }
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
