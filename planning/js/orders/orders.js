$(document).ready(function () { 
  $('.cardAddOrders').hide();

  // Abrir card crear pedidos
  $('#btnNewOrder').click(function (e) {
    e.preventDefault();

    $('.cardImportOrder').hide(800);
    $('.cardAddOrders').toggle(800);
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_order');

    $('#formCreateOrder').trigger('reset');
    $('#btnCreateOrder').html('Crear');
  }); 

  $('#btnCreateOrder').click(function (e) {
    e.preventDefault();

    let idOrder = sessionStorage.getItem('id_order');

    if (!idOrder || idOrder == null) {
      checkDataOrder('/api/addOrder', idOrder);
    } else
      checkDataOrder('/api/updateOrder', idOrder);
  });

  $(document).on('click', '.updateOrder', function () {
    $('.cardImportOrder').hide(800);
    $('.cardAddOrders').show(800);
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
    $(`#seller option[value=${data.id_seller}]`).prop('selected', true);
    $(`#client option[value=${data.id_client}]`).prop('selected', true);

    const dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
    const arr = dataProducts.find((item) => item.id_product == data.id_product);
    $('#inptQuantity').val(arr.quantity);

    $('#originalQuantity').val(data.original_quantity); 

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataOrder = async(url, idOrder) => {
    let order = $('#order').val();
    let dateOrder = $('#dateOrder').val();
    let minDate = $('#minDate').val();
    let maxDate = $('#maxDate').val();
    let idProduct = parseFloat($('#refProduct').val());
    let idClient = parseFloat($('#client').val());
    let seller = parseFloat($('#seller').val());
    let originalQuantity = parseFloat($('#originalQuantity').val());

    let data = idProduct * idClient * originalQuantity * seller;

    if (
      isNaN(data)
      || data <= 0 ||
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
      
    if (dateOrder > minDate) {
      toastr.error('Fecha de pedido mayor a la fecha minima');
      return false;
    }
      
    if (minDate > maxDate) {
      toastr.error('Fecha minima mayor a la fecha maxima');
      return false;
    }
      
    let date = new Date().toISOString().split('T')[0];
      
    if (minDate < date || maxDate < date || dateOrder < date) {
      toastr.error('Fecha por debajo de la fecha actual');
      return false;
    }

    let dataOrder = new FormData(formCreateOrder);

    if (idOrder) {
      dataOrder.append('idOrder', idOrder);
    }

    let resp = await sendDataPOST(url, dataOrder);

    message(resp);
  }; 

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblOrder.fnGetData(row);

    let dataOrder = {}; 

    dataOrder['idOrder'] = data.id_order;
    dataOrder['idProduct'] = data.id_product;

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
        if (result) {
          $.post('/api/deleteOrder', dataOrder,
            function (data, textStatus, jqXHR) {
              message(data);              
            }, 
          ); 
        }
      },
    });
  };

  /* Mensaje de exito */
  message = (data) => {
    if (data.success == true) {
      $('.cardImportOrder').hide(800);
      $('#formImportOrder').trigger('reset');
      $('.cardAddOrders').hide(800);
      $('#formCreateOrder').trigger('reset');
      loadAllData();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
