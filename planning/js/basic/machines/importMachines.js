$(document).ready(function () {
  let selectedFile;

  $('.cardImportMachines').hide();

  $('#btnImportNewMachines').click(function (e) {
    e.preventDefault();
    $('.cardCreateMachines').hide(800);
    $('.cardImportMachines').toggle(800);
  });

  $('#fileMachines').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportMachines').click(function (e) {
    e.preventDefault();

    file = $('#fileMachines').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formMachines');
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
        const expectedHeaders = ['maquina', 'precio', 'años_depreciacion', 'horas_trabajo_dia', 'dias_trabajo_mes'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMachines').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let machinesToImport = data.map((item) => {
          !item.maquina ? item.maquina = '' : item.maquina;
          !item.precio ? item.precio = '' : item.precio;
          !item.años_depreciacion ? item.años_depreciacion = '' : item.años_depreciacion;
          !item.horas_trabajo_dia ? item.horas_trabajo_dia = '' : item.horas_trabajo_dia;
          !item.dias_trabajo_mes ? item.dias_trabajo_mes = '' : item.dias_trabajo_mes;

          return {
            machine: item.maquina,
            cost: item.precio,
            depreciationYears: item.años_depreciacion,
            hoursMachine: item.horas_trabajo_dia,
            daysMachine: item.dias_trabajo_mes,
          };
        });
        checkMachine(machinesToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMachines').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/machinesDataValidation',
      data: { importMachines: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMachines').val('');
          $('#formImportMachines').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#formImportMachines').val('');

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg.map(item =>
            `<li>
              <span class="badge text-danger" style="font-size: 16px;">${item.message}</span>
            </li>`
          ).join('');

          // Mostramos el mensaje con Bootbox
          bootbox.alert({
            title: 'Estado Importación Data',
            message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
            size: 'large',
            backdrop: true
          });
          return false;
        }
        
        if (typeof arr === 'object' && !Array.isArray(arr) && arr !== null && resp.debugg.length == 0) {
          bootbox.confirm({
            title: '¿Desea continuar con la importación?',
            message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveMachineTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileMachines').val('');
              }
            },
          });
        }
      },
    });
  };

  const saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanMachines',
      data: { importMachines: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMachines').val('');
        messageMachine(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMachines').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Maquinas.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
