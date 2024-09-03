$(document).ready(function () {
  let selectedFile;

  $('.cardImportAreas').hide();

  $('#btnNewImportAreas').click(function (e) {
    e.preventDefault();
    $('.cardCreateArea').hide(800);
    $('.cardImportAreas').toggle(800);
  });

  $('#fileAreas').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportAreas').click(function (e) {
    e.preventDefault();

    file = $('#fileAreas').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formAreas');
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
        const expectedHeaders = ['area'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileAreas').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let areaToImport = data.map((item) => {
          return {
            area: item.area,
          };
        });
        checkArea(areaToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileAreas').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkArea = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/areasDataValidation',
      data: { importAreas: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileAreas').val('');
          $('#formImportAreas').trigger('reset');
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
              $('#fileAreas').val('');
            }
          },
        });
      },
    });
  };

  const saveAreaTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanArea',
      data: { importAreas: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileAreas').val('');
        messageArea(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsAreas').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Areas.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
