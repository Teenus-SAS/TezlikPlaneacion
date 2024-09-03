$(document).ready(function () {
  let selectedFile;

  $('.cardImportEmployees').hide();

  $('#btnImportNewEmployee').click(function (e) {
    e.preventDefault();
    $('.cardCreateEmployee').hide(800);
    $('.cardImportEmployees').toggle(800);
  });

  $('#fileEmployees').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportEmployees').click(function (e) {
    e.preventDefault();

    file = $('#fileEmployees').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formEmployees');
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
        const expectedHeaders = ['nombre', 'apellido', 'area', 'proceso', 'posicion'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileEmployees').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let payrollToImport = data.map((item) => {
          return {
            firstname: item.nombre,
            lastname: item.apellido,
            area: item.area,
            process: item.proceso,
            position: item.posicion,
          };
        });
        checkPayroll(payrollToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileEmployees').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkPayroll = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/payrollDataValidation',
      data: { importPayroll: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileEmployees').val('');
          $('#formImportEmployees').trigger('reset');
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
              saveAreaTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileEmployees').val('');
            }
          },
        });
      },
    });
  };

  const saveAreaTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPayroll',
      data: { importPayroll: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileEmployees').val('');
        messagePayroll(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsEmployees').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Nomina.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
