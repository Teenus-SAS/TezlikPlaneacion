$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardAddRequisitions').hide();

  /* Abrir panel crear producto */

  $('#btnNewRequisition').click(function (e) {
    e.preventDefault();

    $('.cardImportRequisitions').hide(800); 
    $('.cardTableConfigMaterials').show(800);
    $('.cardAddRequisitions').toggle(800);
    $('#btnAddRequisition').html('Asignar');

    sessionStorage.removeItem('id_requisition');

    $('#formAddRequisition').trigger('reset');
  });

    /* Adicionar nueva materia prima */

    $('#btnAddRequisition').click(function (e) {
        e.preventDefault();

        let idRequisition = sessionStorage.getItem('id_requisition');

        if (idRequisition == '' || idRequisition == null) {
            idProduct = $('#selectNameProduct').val();
            applicationDate = $('#applicationDate').val();
            deliveryDate = $('#deliveryDate').val();
            quan = $('#quantity').val();

            data = quan * idProduct;

            if (!data || applicationDate == ''|| deliveryDate == ''|| quan == '') {
                toastr.error('Ingrese todos los campos');
                return false;
            }

            requisition = $('#formAddRequisition').serialize();

            $.post(
                '/api/addRequisition',
                requisition,
                function (data, textStatus, jqXHR) {
                    message(data);
                }
            );
        } else {
            updateRequisition();
        }
    });

  /* Actualizar productos materials */

  $(document).on('click', '.updateRequisition',async function (e) {
    $('.cardImportRequisitions').hide(800);
    $('.cardAddRequisitions').show(800);
    $('#btnAddRequisition').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblRequisitions.fnGetData(row);

    sessionStorage.setItem('id_requisition', data.id_requisition);

    $(`#refProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop('selected', true);
    $('#applicationDate').val(data.application_date);
    $('#deliveryDate').val(data.delivery_date);
    $('#purchaseOrder').val(data.purchase_order);

    quantity = data.quantity;

    if (quantity.isInteger) quantity = quantity.toLocaleString('es-CO');
    else
      quantity = quantity.toLocaleString(undefined, {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
      });
    $('#quantity').val(quantity);
    
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateRequisition = () => {
    let data = $('#formAddRequisition').serialize();
    idRequisition = sessionStorage.getItem('id_requisition');
    data =
      data +
      '&idRequisition=' +
      idRequisition;

    $.post(
      '/api/updateRequisition',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar materia prima */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];

    let data = tblRequisitions.fnGetData(row);

    idRequisition = data.id_requisition;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta requisicion? Esta acción no se puede reversar.',
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
            `/api/deleteRequisition/${idRequisition}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('.cardAddRequisitions').hide(800);
      $('#formAddRequisition').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblRequisitions').DataTable().clear();
    $('#tblRequisitions').DataTable().ajax.reload();
  }
});
