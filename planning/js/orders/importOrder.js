$(document).ready(function () {
  let selectedFile;

  $('.cardImportOrder').hide();

  $('#btnImportNewOrder').click(function (e) {
    e.preventDefault();

    $('.cardImportOrder').toggle(800);
  });

  $('#fileOrder').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportOrder').click(function (e) {
    e.preventDefault();

    file = $('#fileOrder').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formClients');
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
        const expectedHeaders = ['pedido','fecha_pedido','fecha_minima','fecha_maxima','referencia_producto','producto','cliente','cantidad_original'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileOrder').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let OrderToImport = data.map((item) => {
          return {
            order: item.pedido,
            dateOrder: item.fecha_pedido,
            minDate: item.fecha_minima,
            maxDate: item.fecha_maxima,
            referenceProduct: item.referencia_producto,
            product: item.producto,
            client: item.cliente,
            originalQuantity: item.cantidad_original,
          };
        });
        checkOrder(OrderToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileOrder').val('');
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkOrder = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/orderDataValidation',
      data: { importOrder: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileOrder').val('');
          $('#formImportOrder').trigger('reset');
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
              saveOrderTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileOrder').val('');
            }
          },
        });
      },
    });
  };

  saveOrderTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addOrder',
      data: { importOrder: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileOrder').val('');

        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsOrder').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Pedidos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
