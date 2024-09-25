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
                        action = `<button class="btn btn-info changeDateOP" id="delivery">Recibir OP</button>`;
                    else {
                        action = `Recibido: <br>${data.firstname_deliver} ${data.lastname_deliver}<br>${data.receive_date}`;
                        `<a href="javascript:;">
                          <i id="${data.id_part_deliv}" class="mdi mdi-playlist-check seeReceiveOP" data-toggle='tooltip' title='Ver Usuarios' style="font-size: 30px;color:black"></i>
                        </a>`;
                    }

                    return action;
                },
            },
        ],
    });

    // Recibir OP
    $(document).on("click", ".changeDateOP", function (e) {
        e.preventDefault();

        let date = new Date().toISOString().split("T")[0];
        const row = $(this).closest("tr")[0];
        let data = tblPartialsDelivery.fnGetData(row);

        bootbox.confirm({
            title: "Ingrese Fecha De Ingreso!",
            message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="dateOP" max="${date}"></input>
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
                    let date = $("#dateOP").val();

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
                        url: "/api/saveReceiveOPDate",
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (resp) {
                            messageOP(resp);
                        },
                    });
                }
            },
        });
    });

    $(document).on('click', '.seeReceiveOP', async function (e) {
    e.preventDefault();

    const row = $(this).closest("tr")[0];
    let data = tblPartialsDelivery.fnGetData(row);

    let users = await searchData(`/api/usersOPPartial/${data.id_part_deliv}`);
    let rows = '';

    for (let i = 0; i < users.length; i++) {
      rows +=
        `<tr>
          <td>${i + 1}</td>
          <td>${users[i].firstname}</td>
          <td>${users[i].lastname}</td>
          <td>${users[i].email}</td>
        </tr>`;
    }

    // Mostramos el mensaje con Bootbox
    bootbox.alert({
      title: 'Usuarios',
      message: `
            <div class="container">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="fixed-table-loading table table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${rows}
                    </tbody>
                  </table>
                </div>
              </div> 
            </div>`,
      size: 'large',
      backdrop: true
    });
    return false;
  });

    const messageOP = (data) => {
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