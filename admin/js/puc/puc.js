$(document).ready(function () {
  /* Abrir modal crear cuenta */

  let id;
  $('.createPUC').hide();

  $('#btnNewPUC').click(function (e) {
    e.preventDefault();
    $('.createPUC').toggle(800);
    $('#btnCreatePuc').removeClass('updPUC');
    $('#btnCreatePuc').addClass('crtPUC');
    $('#btnCreatePuc').html('Crear');
    $('#staticBackdropLabel').html('Crear cuenta');
    $('#formCreatePuc').trigger('reset');
  });

  /* Crear Cuenta */

  $(document).on('click', '.crtPUC', function (e) {
    e.preventDefault();
    accountNumber = $('#accountNumber').val();
    account = $('#account').val();

    dataPuc = new FormData(document.getElementById('formCreatePuc'));

    if (accountNumber === '' || account === '') {
      toastr.error('Ingrese todos los campos');
      return false;
    } else {
      $.ajax({
        type: 'POST',
        url: '/api/createPUC',
        data: dataPuc,
        contentType: false,
        cache: false,
        processData: false,

        success: function (resp) {
          message(resp);
        },
      });
    }
  });

  /* Cargar datos en el modal*/

  $(document).on('click', '.updatePuc', function (e) {
    e.preventDefault();
    $('.createPUC').toggle(800);
    $('#btnCreatePuc').removeClass('crtPUC');
    $('#btnCreatePuc').addClass('updPUC');
    $('#staticBackdropLabel').html('Actualizar cuenta');
    $('#btnCreatePuc').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblPUC.fnGetData(row);

    id = data.id_puc;

    $('#accountNumber').val(data.number_count);
    $('#account').val(data.count);
  });

  /* Actualizar Cuentas*/

  $(document).on('click', '.updPUC', function (e) {
    e.preventDefault();

    accountNumber = $('#accountNumber').val();
    account = $('#account').val();

    dataProduct = new FormData(document.getElementById('formCreatePuc'));
    dataProduct.append('id_puc', id);

    $.ajax({
      type: 'POST',
      url: `/api/updatePUC`,
      data: dataProduct,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('.createPUC').toggle(800);
      $('#formCreatePuc').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPUC').DataTable().clear();
    $('#tblPUC').DataTable().ajax.reload();
  }
});
