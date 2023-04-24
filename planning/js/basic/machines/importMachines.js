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

    importFile(selectedFile)
      .then((data) => {
        let machinesToImport = data.map((item) => {
          return {
            machine: item.maquina.trim(),
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
      url: '/api/machinesDataValidation',
      data: { importMachines: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportMachines').trigger('reset');
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
            } else $('#fileMachines').val('');
          },
        });
      },
    });
  };

  saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanMachines',
      data: { importMachines: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportMachines').hide(800);
          $('#formImportMachines').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblMachines').DataTable().clear();
          $('#tblMachines').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMachines').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Maquinas.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
