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
        <div class="spinner-grow text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        const expectedHeaders = ['referencia_material', 'material', 'proveedor', 'fecha_solicitud', 'fecha_entrega','cantidad_solicitada','orden_compra'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileRequisitions').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let requisitionToImport = data.map((item) => {
          !item.referencia_material ? item.referencia_material = '' : item.referencia_material;
          !item.material ? item.material = '' : item.material;
          !item.proveedor ? item.proveedor = '' : item.proveedor;
          !item.fecha_solicitud ? item.fecha_solicitud = '' : item.fecha_solicitud;
          !item.fecha_entrega ? item.fecha_entrega = '' : item.fecha_entrega;
          !item.cantidad_solicitada ? item.cantidad_solicitada = 0 : item.cantidad_solicitada;
          !item.orden_compra ? item.orden_compra = '' : item.orden_compra;

          return {
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            client: item.proveedor,
            applicationDate: item.fecha_solicitud,
            deliveryDate: item.fecha_entrega,
            requestedQuantity: item.cantidad_solicitada,
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
  const checkRequisition = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/requisitionDataValidation',
      data: { importRequisition: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileRequisitions').val('');
          
          $('#formImportRequisitions').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileRequisitions').val('');

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
                $('#fileRequisitions').val('');
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

    let url = 'assets/formatsXlsx/Requisiciones.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
