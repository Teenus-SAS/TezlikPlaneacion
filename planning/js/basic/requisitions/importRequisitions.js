$(document).ready(function () {
  let selectedFile;

  $('.cardImportRequisitions').hide();

  $('#btnImportNewRequisitions').click(function (e) {
    e.preventDefault();
    $('.cardAddRequisitions').hide(800);
    $('.cardImportRequisitions').toggle(800);
  });

  $('#fileRequisitions').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportRequisitions').click(function (e) {
    e.preventDefault();

    file = $('#fileRequisitions').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let requisitionToImport = data.map((item) => {
          return {
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            applicationDate: item.fecha_solicitud,
            deliveryDate: item.fecha_entrega,
            quantity: item.cantidad,
            purchaseOrder: item.orden_compra,
          };
        });
        checkRequisition(requisitionToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkRequisition = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/requisitionDataValidation',
      data: { importRequisition: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportRequisitions').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert}`,
          // message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
            } else $('#fileRequisitions').val('');
          },
        });
      },
    });
  };

  saveMachineTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addRequisition',
      data: { importRequisition: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportRequisitions').hide(800);
          $('#formImportRequisitions').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblRequisitions').DataTable().clear();
          $('#tblRequisitions').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsRequisitions').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Requisiciones.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
