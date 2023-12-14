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

    $('.cardBottons').hide();
    
    let form = document.getElementById('formRequisitions');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        let requisitionToImport = data.map((item) => {
          return {
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            client: item.proveedor,
            applicationDate: item.fecha_solicitud,
            deliveryDate: item.fecha_entrega,
            quantity: item.cantidad,
            purchaseOrder: item.orden_compra,
          };
        });
        checkRequisition(requisitionToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileRequisitions').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
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
          $('.cardLoading').remove();
        $('.cardBottons').show(400);
          $('#fileRequisitions').val('');
          
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
            } else {
              $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileRequisitions').val('');
            }
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
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileRequisitions').val('');

        message(r);
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
