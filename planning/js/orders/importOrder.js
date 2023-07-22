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

    importFile(selectedFile)
      .then((data) => {
        let OrderToImport = data.map((item) => {
          return {
            order: item.pedido,
            dateOrder: item.fecha_pedido,
            minDate: item.fecha_minima,
            maxDate: item.fecha_maxima,
            referenceProduct: item.referencia_producto,
            product: item.producto,
            client: item.cliente,
            // orderType: item.tipo_pedido,
            originalQuantity: item.cantidad_original,
            // quantity: item.cantidad_pendiente,
          };
        });
        checkOrder(OrderToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
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
            } else $('#fileOrder').val('');
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
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportOrder').hide(800);
          $('#formImportOrder').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblOrder').DataTable().clear();
          $('#tblOrder').DataTable().ajax.reload();
        }
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
