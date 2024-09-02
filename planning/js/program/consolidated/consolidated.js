$(document).ready(function () {
  $("#btnCalcConsolidated").click(function (e) {
    e.preventDefault();
    week = $("#numWeek").val();

    if (!week || week == "") {
      toastr.error("Ingrese numero de semana");
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
    const { success, error, info, message } = data;
    if (success) {
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
