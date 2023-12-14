$(document).ready(function () {
  /* Cargar pedidos */
  loadTblOffices = (min_date, max_date) => {
    if (min_date == null && max_date == null) url = "/api/actualOffices";
    else url = `/api/offices/${min_date}/${max_date}`;

    if ($.fn.dataTable.isDataTable("#tblOffices")) {
      $("#tblOffices").DataTable().clear();
      $("#tblOffices").DataTable().ajax.url(url).load();
      return;
    }

    tblOffices = $("#tblOffices").dataTable({
      // destroy: true,
      pageLength: 50,
      ajax: {
        url: url,
        dataSrc: "",
      },
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Fecha",
          data: "date_order",
          className: "uniqueClassName",
        },
        {
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName",
        },

        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName",
        },
        {
          title: "Fechas",
          data: null,
          className: "classCenter",
          render: function (data, type, full, meta) {
            const minDate = full.min_date;
            const maxDate = full.max_date;
            return `Mínima: ${moment(minDate).format(
              "DD/MM/YYYY"
            )}<br>Máxima: ${moment(maxDate).format("DD/MM/YYYY")}`;
          },
        },
        {
          title: "Ref",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName",
        },
        {
          title: "Cantidad",
          data: "original_quantity",
          className: "classCenter",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        {
          title: "Existencias",
          data: "quantity",
          className: "classCenter",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Estado",
          data: "status",
          className: "classCenter",
          render: function (data) {
            if (data == "Entregado") badge = "badge-success";
            else if (data == "Sin Ficha Tecnica" || data == "Sin Materia Prima")
              badge = "badge-danger";
            else if (data == "Despacho") badge = "badge-info";
            else badge = "badge-light";

            return `<span class="badge ${badge}">${data}</span>`;
          },
        },
        {
          title: "F.Entrega",
          data: null,
          className: "classCenter",
          render: function (data) {
            data.status == "Despacho"
              ? (action = `<button class="btn btn-warning changeDate" id="${data.id_order}" name="${data.id_order}">Entregar</button>`)
              : (action = data.delivery_date);

            return action;
          },
        },
        {
          title: "Cancelar",
          data: null,
          className: "classCenter",
          render: function (data) {
            return data.status == "Despacho"
              ? `<a href="javascript:;" <i class="bi bi-x-octagon-fill cancelOrder" id="${data.id_order}" data-toggle='tooltip' title='Cancelar Despacho' style="font-size: 30px;color:red;"></i></a>`
              : "";
          },
        },
      ],
    });
  };

  loadTblOffices(null, null);
});
