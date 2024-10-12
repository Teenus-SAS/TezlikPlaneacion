$(document).ready(function () {
  $('.cardAddRisk').hide();

  $(document).on('click', '.updateRisk', function () {
    let row = $(this).parent().parent()[0];
    let data = tblRisks.fnGetData(row);
    $('#formAddRisk').trigger('reset');

    sessionStorage.setItem('id_risk', data.id_risk);

    $(`#risk option[value=${data.id_risk}]`).prop('selected', true);
    $('#risk').prop('disabled', true);
    $('#percentage').val(data.percentage);

    $('.cardAddRisk').show(800);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $('#btnAddRisk').click(function (e) {
    e.preventDefault();

    let risk = $('#risk').val();
    let percentage = $('#percentage').val();

    if (risk == '' || percentage == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }
    $('#risk').prop('disabled', false);

    let data = $('#formAddRisk').serialize();

    $.post('/api/updateRisk', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardAddRisk').hide(800);
      $('#formAddRisk').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblRisks').DataTable().clear();
    $('#tblRisks').DataTable().ajax.reload();
  }
});
