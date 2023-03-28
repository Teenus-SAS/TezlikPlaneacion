$(document).ready(function () {
  let selectedFile;

  $('.cardImportOrderTypes').hide();

  $('#btnImportNewOrderTypes').click(function (e) {
    e.preventDefault();
    $('.cardCreateOrderType').hide(800);
    $('.cardImportOrderTypes').toggle(800);
  });

  $('#fileOrderTypes').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportOrderTypes').click(function (e) {
    e.preventDefault();

    file = $('#fileOrderTypes').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let orderTypesToImport = data.map((item) => {
          return {
            orderType: item.tipo_pedido.trim(),
          };
        });
        checkOrderTypes(orderTypesToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkOrderTypes = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/orderTypesDataValidation',
      data: { importOrderTypes: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportOrderTypes').trigger('reset');
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
              saveClientTable(data);
            } else $('#fileOrderTypes').val('');
          },
        });
      },
    });
  };

  saveClientTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addOrderTypes',
      data: { importOrderTypes: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportOrderTypes').hide(800);
          $('#formImportOrderTypes').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblOrderTypes').DataTable().clear();
          $('#tblOrderTypes').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsOrderTypes').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Tipo_Pedidos.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
