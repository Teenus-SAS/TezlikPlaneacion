$(document).ready(function () {
    $(document).on("click", ".changeStatus", function () {
        let row = $(this).parent().parent()[0];
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
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblQuotes.fnGetData(row);

        let idProgramming = data.id_programming;

        sessionStorage.setItem('id_programming', idProgramming);
    };

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            loadData();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };
});