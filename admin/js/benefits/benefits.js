$(document).ready(function () { 
  $('.cardAddBenefit').hide();

  $(document).on('click', '.updateBenefit', function () {
    let row = $(this).parent().parent()[0];
    let data = tblBenefits.fnGetData(row);
    $('#formAddBenefit').trigger('reset');

    sessionStorage.setItem('id_benefit', data.id_benefit);

    $(`#benefit option[value=${data.id_benefit}]`).prop('selected', true);
    $('#benefit').prop('disabled', true);
    $('#percentage').val(data.percentage);

    $('.cardAddBenefit').show(800);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $('#btnAddBenefit').click(function (e) {
    e.preventDefault();

    let benefit = $('#benefit').val();
    let percentage = $('#percentage').val();

    if (benefit == '' || percentage == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }
    $('#benefit').prop('disabled', false);

    let data = $('#formAddBenefit').serialize();

    $.post('/api/updateBenefit', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardAddBenefit').hide(800);
      $('#formAddBenefit').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblBenefits').DataTable().clear();
    $('#tblBenefits').DataTable().ajax.reload();
  }
});
