$(document).ready(function () {
  let selectedFile;
  $('.cardImportPlanMachines').hide();

  $('#btnImportNewPlanMachines').click(function (e) {
    e.preventDefault();
    $('.cardCreatePlanMachines').hide(800);
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

    importFile(selectedFile)
      .then((data) => {
        let machinesToImport = data.map((item) => {
          return {
            machine: item.maquina.trim(),
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
        console.log('Ocurrio un error. Intente Nuevamente');
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
            if (result == true) {
              saveMachineTable(data);
            } else $('#filePlanMachines').val('');
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
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportPlanMachines').hide(800);
          $('#formImportPlanMachines').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblPlanMachines').DataTable().clear();
          $('#tblPlanMachines').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPlanMachines').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Planeacion_Maquinas.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
