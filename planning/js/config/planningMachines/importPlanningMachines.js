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

    let file = $('#filePlanMachines').val();

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
        const expectedHeaders = ['tipo', 'maquina', 'no_trabajadores', 'total_turno', 'hora_dia', 'hora_inicio', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'disponible'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanMachines').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let machinesToImport = data.map((item) => {
          !item.tipo ? item.tipo = '' : item.tipo;
          !item.maquina ? item.maquina = '' : item.maquina;
          !item.no_trabajadores ? item.no_trabajadores = 0 : item.no_trabajadores;
          !item.total_turno ? item.total_turno = 0 : item.total_turno;
          !item.hora_dia ? item.hora_dia = 0 : item.hora_dia;
          !item.hora_inicio ? item.hora_inicio = 0 : item.hora_inicio;
          !item.hora_fin ? item.hora_fin = 0 : item.hora_fin;
          !item.enero ? item.enero = 0 : item.enero;
          !item.febrero ? item.febrero = 0 : item.febrero;
          !item.marzo ? item.marzo = 0 : item.marzo;
          !item.abril ? item.abril = 0 : item.abril;
          !item.mayo ? item.mayo = 0 : item.mayo;
          !item.junio ? item.junio = 0 : item.junio;
          !item.julio ? item.julio = 0 : item.julio;
          !item.agosto ? item.agosto = 0 : item.agosto;
          !item.septiembre ? item.septiembre = 0 : item.septiembre;
          !item.octubre ? item.octubre = 0 : item.octubre;
          !item.noviembre ? item.noviembre = 0 : item.noviembre;
          !item.diciembre ? item.diciembre = 0 : item.diciembre;
          !item.disponible ? item.disponible = '' : item.disponible;

          return {
            type: item.maquina,
            machine: item.maquina,
            numberWorkers: item.no_trabajadores,
            workShift: item.total_turno,
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
            acive: item.disponible,
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
  const checkMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/planningMachinesDataValidation',
      data: { importPlanMachines: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanMachines').val('');

          $('#formImportPlanMachines').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePlanMachines').val('');

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
                $('#filePlanMachines').val('');
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
      let hourStart = moment((data[i].hour_start).toFixed(2), ['HH:mm']).format('h:mm A');

      planningMachines.push({
        tipo: data[i].type_program_machine == '0' ? 'PROCESO MANUAL' : 'MAQUINA',
        maquina: data[i].machine,
        no_trabajadores: data[i].number_workers,
        total_turno: data[i].work_shift,
        hora_dia: data[i].hours_day,
        hora_inicio: hourStart, 
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
        disponible: data[i].status == '1' ? 'SI' : 'NO',
      });
    }

    ws = XLSX.utils.json_to_sheet(planningMachines);
    XLSX.utils.book_append_sheet(wb, ws, 'Planeacion Maquinas');
    XLSX.writeFile(wb, 'Planeacion_Maquinas.xlsx');
  });
});
