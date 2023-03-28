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

    file = $('#filePlanCiclesMachine').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let planCiclesMachineToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto.trim(),
            product: item.producto.trim(),
            machine: item.maquina.trim(),
            ciclesHour: item.ciclo_hora,
          };
        });
        checkCiclesMachine(planCiclesMachineToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkCiclesMachine = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/planCiclesMachineDataValidation',
      data: { importPlanCiclesMachine: data },
      success: function (resp) {
        if (resp.error == true) {
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
            if (result == true) {
              saveCiclesMachineTable(data);
            } else $('#filePlanCiclesMachine').val('');
          },
        });
      },
    });
  };

  saveCiclesMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPlanCiclesMachine',
      data: { importPlanCiclesMachine: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportPlanCiclesMachine').hide(800);
          $('#formImportPlanCiclesMachine').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblPlanCiclesMachine').DataTable().clear();
          $('#tblPlanCiclesMachine').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPlanCiclesMachine').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Ciclos_Maquina.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
