$(document).ready(function () {
  $('#btnCalcConsolidated').click(function (e) {
    e.preventDefault();
    week = $('#numWeek').val();

    if (!week || week == '') {
      toastr.error('Ingrese numero de semana');
      return false;
    }

    $.ajax({
      url: `/api/calcConsolidated/${week}`,
      success: function (r) {
        message(r);
        loadTblConsolidated(r.dataConsolidated);
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
