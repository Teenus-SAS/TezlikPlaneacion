$(document).ready(function () {
  let selectedFile;

  $('.cardImportProcess').hide();

  $('#btnImportNewProcess').click(function (e) {
    e.preventDefault();
    $('.cardCreateProcess').hide(800);
    $('.cardImportProcess').toggle(800);
  });

  $('#fileProcess').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProcess').click(function (e) {
    e.preventDefault();

    file = $('#fileProcess').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let ProcessToImport = data.map((item) => {
          return {
            process: item.proceso,
          };
        });
        checkProcess(ProcessToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProcess = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/processDataValidation',
      data: { importProcess: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportProcess').trigger('reset');
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
              saveProcessTable(data);
            } else $('#fileProcess').val('');
          },
        });
      },
    });
  };

  saveProcessTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addPlanProcess',
      data: { importProcess: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportProcess').hide(800);
          $('#formImportProcess').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblProcess').DataTable().clear();
          $('#tblProcess').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProcess').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Procesos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
