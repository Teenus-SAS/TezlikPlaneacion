$(document).ready(function () {
    tblPartialsDelivery = $('#tblPartialsDelivery').dataTable({
        destroy: true,
        pageLength: 50,
        ajax: {
            url: `/api/productionOrderPartial`,
            dataSrc: '',
        },
        language: {
            url: '/assets/plugins/i18n/Spanish.json',
        },
        columns: [
            {
                title: 'No.',
                data: null,
                className: 'uniqueClassName dt-head-center',
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                title: "Fechas",
                data: null,
                className: "uniqueClassName dt-head-center",
                width: "200px",
                render: function (data, type, full, meta) {
                    const start_date = full.start_date;
                    const end_date = full.end_date;

                    return `Inicio: ${moment(start_date).format(
                        "DD/MM/YYYY HH:mm A"
                    )}<br>Fin: ${moment(end_date).format("DD/MM/YYYY HH:mm A")}`;
                },
            },
            {
                title: 'Referencia',
                data: 'reference',
                className: 'uniqueClassName dt-head-center',
            },
            {
                title: 'Producto',
                data: 'product',
                className: 'uniqueClassName dt-head-center',
            },
            // {
            //   title: 'Existencia',
            //   data: 'quantity',
            //   className: 'uniqueClassName dt-head-center',
            //   render: (data) => parseFloat(data).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
            // },
            {
                title: 'Operador',
                data: null,
                className: 'uniqueClassName dt-head-center',
                render: function (data) {
                    return `${data.firstname} ${data.lastname}`;
                }
            },
            {
                title: 'Desperdicio',
                data: 'waste',
                className: 'uniqueClassName dt-head-center',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                title: 'Cantidad Entregada',
                data: 'partial_quantity',
                className: 'uniqueClassName dt-head-center',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                title: "Acción",
                data: null,
                className: "uniqueClassName dt-head-center",
                render: function (data) {
                    if (!data.receive_date || data.receive_date == "0000-00-00 00:00:00")
                        action = `<button class="btn btn-info changeDate" id="delivery">Recibir OC</button>`;
                    else {
                        action = `Recibido: <br>${data.firstname_deliver} ${data.lastname_deliver}<br>${data.receive_date}`;
                        // <a href="javascript:;">
                        //   <i id="${data.id_part_deliv}" class="mdi mdi-playlist-check seeReceiveOC" data-toggle='tooltip' title='Ver Usuarios' style="font-size: 30px;color:black"></i>
                        // </a>`;
                    }

                    return action;
                },
            },
        ],
    });

    // Recibir OC
    $(document).on("click", ".changeDate", function (e) {
        e.preventDefault();

        let date = new Date().toISOString().split("T")[0];
        const row = $(this).closest("tr")[0];
        let data = tblPartialsDelivery.fnGetData(row);

        bootbox.confirm({
            title: "Ingrese Fecha De Ingreso!",
            message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date" max="${date}"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
            buttons: {
                confirm: {
                    label: "Agregar",
                    className: "btn-success",
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-danger",
                },
            },
            callback: function (result) {
                if (result) {
                    let date = $("#date").val();

                    if (!date) {
                        toastr.error("Ingrese los campos");
                        return false;
                    }

                    let form = new FormData();
                    form.append("idPartDeliv", data.id_part_deliv);
                    form.append("idProduct", data.id_product);
                    form.append("quantity", parseFloat(data.quantity_product) + parseFloat(data.partial_quantity));
                    form.append("date", date);

                    $.ajax({
                        type: "POST",
                        url: "/api/saveReceiveOCDate",
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (resp) {
                            messageOC(resp);
                        },
                    });
                }
            },
        });
    });

    const messageOC = (data) => {
        const { success, error, info, message } = data;
        if (success) {
            toastr.success(message);
            updateTable();
            return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);
    };

    function updateTable() {
        $("#tblPartialsDelivery").DataTable().clear();
        $("#tblPartialsDelivery").DataTable().ajax.reload();
    }
});