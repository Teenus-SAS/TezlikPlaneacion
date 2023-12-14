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
                if (result == true) {
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

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */
    function updateTable() {
        $('#tblProductionOrders').DataTable().clear();
        $('#tblProductionOrders').DataTable().ajax.reload();
    }
});