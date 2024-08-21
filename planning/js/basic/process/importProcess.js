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

    $('.cardBottons').hide();

    let form = document.getElementById('formProcess');
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
        const expectedHeaders = ['proceso'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProcess').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let ProcessToImport = data.map((item) => {
          return {
            process: item.proceso,
          };
        });
        checkProcess(ProcessToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProcess').val('');
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkProcess = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/processDataValidation',
      data: { importProcess: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProcess').val('');
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
            if (result) {
              saveProcessTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProcess').val('');
            }
          },
        });
      },
    });
  };

  const saveProcessTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanProcess',
      data: { importProcess: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProcess').val('');
        /* Mensaje de exito */
        messageProcess(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProcess').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Procesos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
