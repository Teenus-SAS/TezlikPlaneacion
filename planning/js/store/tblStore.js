$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if ($.fn.dataTable.isDataTable("#tblStore")) {
      $("#tblStore").DataTable().destroy();
      $("#tblStore").empty();
    }

    if (this.id == "receive") loadTblStoreMaterial(requisitions);
    else if (this.id == "deliver") loadTblStoreOrder(store);
  });

  loadAllData = async (op) => {
    try {
      const [dataRequisitions, dataStore] = await Promise.all([
        searchData("/api/requisitions"),
        searchData("/api/store"),
      ]);

      if (op == 1) loadTblStoreMaterial(dataRequisitions);
      else loadTblStoreOrder(dataStore);

      requisitions = dataRequisitions;
      store = dataStore;
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  // Recibir
  loadTblStoreMaterial = async (data) => {
    data = data.filter(
      (item) =>
        item.application_date != "0000-00-00" &&
        item.delivery_date != "0000-00-00" &&
        item.purchase_order != ""
    );

    if ($.fn.dataTable.isDataTable("#tblStore")) {
      $("#tblStore").DataTable().clear();
      $("#tblStore").DataTable().rows.add(data).draw();
      return;
    }

    tblStore = $("#tblStore").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
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
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Materia Prima",
          data: "material",
          className: "uniqueClassName",
        },
        {
          title: "Unidad",
          data: "abbreviation",
          className: "uniqueClassName",
        },
        {
          title: "Proveedor",
          data: "provider",
          className: "classCenter",
        },
        {
          title: "Cantidad",
          data: "quantity",
          className: "uniqueClassName",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Orden de Compra",
          data: "purchase_order",
          className: "classCenter",
        },
        {
          title: "Acción",
          data: null,
          className: "uniqueClassName",
          render: function (data) {
            if (!data.admission_date)
              action = `<button class="btn btn-info changeDate" id="delivery">Recibir MP</button>`;
            else {
              // let fechaHora = new Date(data.admission_date);
              // let fechaHoraFormateada = fechaHora.toLocaleDateString("es-CO", { day: "2-digit", month: "2-digit", year: "numeric" });

              action = `Recibido: <br>${data.admission_date}`;
            }

            return action;
          },
        },
      ],
    });
  };

  // Entregar
  loadTblStoreOrder = (data) => {
    tblStore = $("#tblStore").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
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
          title: "No Pedido",
          data: "num_order",
          className: "uniqueClassName",
          visible: false,
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Materia Prima",
          data: "material",
          className: "uniqueClassName",
        },
        {
          title: "Unidad",
          data: "abbreviation",
          className: "uniqueClassName",
        },
        {
          title: "Existencias",
          data: "quantity",
          className: "uniqueClassName",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Reservado",
          data: "reserved",
          className: "uniqueClassName",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Cantidades",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            const store = full.delivery_store;
            const pending = full.delivery_pending;
            if (pending > 0)
              return `Entregado: ${store}<br><span class="badge badge-warning">Pendiente: ${pending}</span>`;
            else return `Entregado: ${store}<br>Pendiente: ${pending}`;
          },
        },
        {
          title: "Acción",
          data: null,
          className: "uniqueClassName",
          render: function (data) {
            if (!data.delivery_date)
              action = `<button class="btn btn-info deliver" id="delivery">Entregar MP</button>`;
            else {
              let fechaHora = new Date(data.delivery_date);
              let fechaHoraFormateada =
                fechaHora.toLocaleDateString("es-CO", {
                  day: "2-digit",
                  month: "2-digit",
                  year: "numeric",
                }) +
                "<br>" +
                fechaHora.toLocaleTimeString("es-CO", {
                  hour: "2-digit",
                  minute: "2-digit",
                  hour12: true,
                });

              action = `Entregado: <br>${fechaHoraFormateada}`;
            }

            return action;
          },
        },
      ],
      rowGroup: {
        dataSrc: function (row) {
          return `<th class="text-center" colspan="8" style="font-weight: bold;"> No Pedido - ${row.num_order} </th>`;
        },
        startRender: function (rows, group) {
          return $("<tr/>").append(group);
        },
        className: "odd",
      },
    });
  };

  loadAllData(1);
});
