$(document).ready(function () {
  $(document).on("click", ".changeStatus", function () {
    const row = $(this).closest("tr")[0];
    let data = tblProductionOrders.fnGetData(row);

    let dataOP = {};
    dataOP["idProgramming"] = data.id_programming;
    dataOP["idOrder"] = data.id_order;
    dataOP["idProduct"] = data.id_product;
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

  /* Mensaje de exito */
  const message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      loadData(2);
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
