$(document).ready(function () {
  $(document).on("click", ".changeStatus", function () {
    const row = $(this).closest("tr")[0];
    let data = tblProductionOrders.fnGetData(row);

    let dataOP = {};
    dataOP["idProgramming"] = data.id_programming;
    dataOP["idOrder"] = data.id_order;
    dataOP["idProduct"] = data.id_product;
    dataOP["origin"] = data.origin;
    dataOP["quantity"] = data.quantity_programming;

    bootbox.confirm({
      title: "Orden de Producción",
      message: "Desea cambiar a fabricado? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.post(
            `/api/changeStatusOP`,
            dataOP,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  seePO = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblProductionOrders.fnGetData(row);

    let idProgramming = data.id_programming;

    sessionStorage.setItem("id_programming", idProgramming);
  };

  $(document).on('click', '.changeFlagOP', function () {
    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblProductionOrders.fnGetData(row);
    let id_programming = data.id_programming;
    let flag_cancel = data.flag_cancel;

    bootbox.confirm({
      title: "Orden de Producción",
      message: `¿ Está seguro de ${flag_cancel == 0 ? 'anular' : 'aprobar'} esta orden. ?`,
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
          $.get(`/api/changeFlagCancelOP/${id_programming}/${flag_cancel == 0 ? '1' : '0'}`,
            function (resp, textStatus, jqXHR) {
              message(resp);
            },
          );
        }
      },
    });
  });

  /* Mensaje de exito */
  const message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      loadData();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
