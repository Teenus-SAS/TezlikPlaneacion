$(document).ready(function () {
  let selectedFile;
  $('.cardImportPlanCiclesMachine').hide();

  $('#btnImportNewPlanCiclesMachine').click(function (e) {
    e.preventDefault();
    $('.cardCreatePlanCiclesMachine').hide(800);
    $('.cardImportPlanCiclesMachine').toggle(800);
  });

  $('#filePlanCiclesMachine').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPlanCiclesMachine').click(function (e) {
    e.preventDefault();

    let file = $('#filePlanCiclesMachine').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();
    
    let form = document.getElementById('formPlanCiclesMachine');
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
        const expectedHeaders = ['referencia_producto', 'producto', 'proceso', 'maquina', 'ciclo_hora'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanCiclesMachine').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let planCiclesMachineToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            process: item.proceso,
            machine: item.maquina,
            ciclesHour: item.ciclo_hora,
          };
        });
        checkCiclesMachine(planCiclesMachineToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePlanCiclesMachine').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkCiclesMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/planCiclesMachineDataValidation',
      data: { importPlanCiclesMachine: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanCiclesMachine').val('');

          $('#formImportPlanCiclesMachine').trigger('reset');
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
              saveCiclesMachineTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#filePlanCiclesMachine').val('');
            }
          },
        });
      },
    });
  };

  const saveCiclesMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanCiclesMachine',
      data: { importPlanCiclesMachine: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePlanCiclesMachine').val('');

        messageMachine(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPlanCiclesMachine').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Ciclos_Maquina.xlsx';

    let link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
