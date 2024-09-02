$(document).ready(function () {
  /* Ocultar panel crear Tipo Pedido */

  $('.cardCreateOrderType').hide();

  /* Abrir panel crear Tipo Pedido */

  $('#btnNewOrderType').click(function (e) {
    e.preventDefault();
    $('.cardImportOrderTypes').hide(800);
    $('.cardCreateOrderType').toggle(800);
    $('#btnCreateOrderType').html('Crear');

    sessionStorage.removeItem('id_order_type');

    $('#formCreateOrderType').trigger('reset');
  });

  /* Crear nuevo Tipo Pedido */

  $('#btnCreateOrderType').click(function (e) {
    e.preventDefault();
    let idOrderType = sessionStorage.getItem('id_order_type');

    if (idOrderType == '' || idOrderType == null) {
      orderType = $('#orderType').val();

      if (orderType == '' || orderType == null) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      orderType = $('#formCreateOrderType').serialize();

      $.post(
        '../../api/addOrderTypes',
        orderType,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateOrderType();
    }
  });

  /* Actualizar Tipo Pedidos */

  $(document).on('click', '.updateOrderType', function (e) {
    $('.cardImportOrderType').hide(800);
    $('.cardCreateOrderType').show(800);
    $('#btnCreateOrderType').html('Actualizar');

    const row = $(this).closest("tr")[0];
    let data = tblOrderTypes.fnGetData(row);

    sessionStorage.setItem('id_order_type', data.id_order_type);
    $('#orderType').val(data.order_type);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateOrderType = () => {
    let data = $('#formCreateOrderType').serialize();
    idOrderType = sessionStorage.getItem('id_order_type');
    data = data + '&idOrderType=' + idOrderType;

    $.post(
      '../../api/updateOrderType',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar Tipo Pedido */
  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblOrderTypes.fnGetData(row);

    let id_order_type = data.id_order_type;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este tipo de pedido? Esta acción no se puede reversar.',
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
          $.get(
            `../../api/deleteOrderType/${id_order_type}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (success) {
      $('.cardCreateOrderType').hide(800);
      $('#formCreateOrderType').trigger('reset');
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblOrderTypes').DataTable().clear();
    $('#tblOrderTypes').DataTable().ajax.reload();
  }
});
