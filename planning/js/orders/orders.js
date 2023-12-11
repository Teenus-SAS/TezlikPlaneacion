$(document).ready(function () {
  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
  });

  // Abrir modal crear pedidos
  $('#btnNewOrder').click(function (e) {
    e.preventDefault();

    $('.cardImportOrder').hide(800);
    $('#createOrder').modal('show');
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_order');

    $('#formCreateOrder').trigger('reset');
    $('#btnCreateOrder').html('Crear');
  });

  // Ocultar modal Pedidos
  $('#btnCloseOrder').click(function (e) {
    e.preventDefault();

    $('#createOrder').modal('hide');
  });

  $('#btnCreateOrder').click(function (e) {
    e.preventDefault();

    let idOrder = sessionStorage.getItem('id_order');

    if (!idOrder || idOrder == null) {
      order = $('#order').val();
      dateOrder = $('#dateOrder').val();
      minDate = $('#minDate').val();
      maxDate = $('#maxDate').val();
      idProduct = $('#refProduct').val();
      idClient = $('#client').val();
      // orderType = $('#orderType').val();
      originalQuantity = $('#originalQuantity').val();
      // quantity = $('#quantity').val();

      data =
        idProduct * idClient * originalQuantity;

      if (
        !data ||
        data == '' ||
        !order ||
        order == '' ||
        !dateOrder ||
        dateOrder == '' ||
        !minDate ||
        minDate == '' ||
        !maxDate ||
        maxDate == ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      dataOrder = $('#formCreateOrder').serialize();

      $.post('../api/addOrder', dataOrder, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else updateOrder();
  });

  $(document).on('click', '.updateOrder', function () {
    $('.cardImportOrder').hide(800);
    $('#createOrder').modal('show');
    $('#btnCreateOrder').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblOrder.fnGetData(row);

    sessionStorage.setItem('id_order', data.id_order);

    $('#order').val(data.num_order);
    $('#dateOrder').val(data.date_order);
    $('#minDate').val(data.min_date);
    $('#maxDate').val(data.max_date);
    $(`#refProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop(
      'selected',
      true
    );
    $(`#client option[value=${data.id_client}]`).prop('selected', true);
    // $(`#orderType option[value=${data.id_order_type}]`).prop('selected', true);
    $('#originalQuantity').val(data.original_quantity);
    // $('#quantity').val(data.quantity);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateOrder = () => {
    let data = $('#formCreateOrder').serialize();
    idOrder = sessionStorage.getItem('id_order');
    data = data + '&idOrder=' + idOrder;

    $.post('/api/updateOrder', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblOrder.fnGetData(row);

    let id_order = data.id_order;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este pedido? Esta acción no se puede reversar.',
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
          $.get(
            `../../api/deleteOrder/${id_order}`,
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
    if (data.success == true) {
      $('#createOrder').modal('hide');
      $('#formCreateOrder').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblOrder').DataTable().clear();
    $('#tblOrder').DataTable().ajax.reload();
  }
});
