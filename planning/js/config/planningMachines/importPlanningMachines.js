$(document).ready(function () {
  let selectedFile;
  $('.cardImportPlanMachines').hide();

  $('#btnImportNewPlanMachines').click(function (e) {
    e.preventDefault();
    $('.cardImportPlanMachines').toggle(800);
  });

  $('#filePlanMachines').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPlanMachines').click(function (e) {
    e.preventDefault();

    file = $('#filePlanMachines').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formPlanMachines');
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
        const expectedHeaders = ['maquina', 'no_trabajadores', 'hora_dia', 'hora_inicio', 'hora_fin', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanMachines').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let machinesToImport = data.map((item) => {
          return {
            machine: item.maquina,
            numberWorkers: item.no_trabajadores,
            hoursDay: item.hora_dia,
            hourStart: item.hora_inicio,
            hourEnd: item.hora_fin,
            january: item.enero,
            february: item.febrero,
            march: item.marzo,
            april: item.abril,
            may: item.mayo,
            june: item.junio,
            july: item.julio,
            august: item.agosto,
            september: item.septiembre,
            october: item.octubre,
            november: item.noviembre,
            december: item.diciembre,
          };
        });
        checkMachine(machinesToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePlanMachines').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/planningMachinesDataValidation',
      data: { importPlanMachines: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanMachines').val('');

          $('#formImportPlanMachines').trigger('reset');
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
              saveMachineTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#filePlanMachines').val('');
            }
          },
        });
      },
    });
  };

  saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanningMachines',
      data: { importPlanMachines: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePlanMachines').val('');
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPlanMachines').click(function (e) {
    e.preventDefault();
    let wb = XLSX.utils.book_new();
    let planningMachines = [];
    let data = tblPlanMachines.fnGetData();

    for (let i = 0; i < data.length; i++) {
      planningMachines.push({
        maquina: data[i].machine,
        no_trabajadores: data[i].number_workers,
        hora_dia: data[i].hours_day,
        hora_inicio: data[i].hour_start,
        hora_fin: data[i].hour_end,
        enero: data[i].january,
        febrero: data[i].february,
        marzo: data[i].march,
        abril: data[i].april,
        mayo: data[i].may,
        junio: data[i].june,
        julio: data[i].july,
        agosto: data[i].august,
        septiembre: data[i].september,
        octubre: data[i].october,
        noviembre: data[i].november,
        diciembre: data[i].december,
      });
    }

    ws = XLSX.utils.json_to_sheet(planningMachines);
    XLSX.utils.book_append_sheet(wb, ws, 'Planeacion Maquinas');
    XLSX.writeFile(wb, 'Planeacion_Maquinas.xlsx');
  });
});
