$(document).ready(function () {
  $("#btnExportStore").hide();

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if ($.fn.dataTable.isDataTable("#tblStore")) {
      $("#tblStore").DataTable().destroy();
      $("#tblStore").empty();
    }
    $(".cardOC, .cardOP").hide();

    if (this.id == "receiveOC") {
      $(".cardOC").show();
      loadTblStoreMaterial(requisitions);
      $("#btnExportStore").hide();
    } else if (this.id == "deliverOC") {
      $(".cardOC").show();
      $("#btnExportStore").show();
      loadTblStoreOrder(store);
    } else if (this.id == "receiveOP") {
      $(".cardOP").show();
    }

    let tables = document.getElementsByClassName("dataTable");

    for (let table of tables) {
      table.style.width = "100%";
      table.firstElementChild.style.width = "100%";
    }
  });

  loadAllData = async (op) => {
    try {
      const [dataRequisitions, dataStore] = await Promise.all([
        searchData("/api/requisitionsMaterials"),
        searchData("/api/store"),
      ]);

      let arr = assignOpToGroups(dataStore, "id_programming");

      if (op == 1) loadTblStoreMaterial(dataRequisitions);
      else loadTblStoreOrder(arr);

      requisitions = dataRequisitions;
      store = arr;
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  // Función para formatear cantidades
  const formatQuantity = (quantity, abbreviation) => {
    quantity = parseFloat(quantity);
    if (abbreviation === "UND") {
      return Math.floor(quantity).toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      });
    }
    return quantity.toLocaleString("es-CO", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };

  // Función para construir las acciones para recibir material
  const buildReceiveAction = (data) => {
    if (!data.admission_date || data.admission_date === "0000-00-00 00:00:00") {
      return `<button class="btn btn-info changeDateMP" id="delivery">Recibir MP</button>`;
    }
    return `Recibido: <br>${data.firstname_deliver} ${data.lastname_deliver}<br>${data.admission_date}`;
  };

  // Función para cargar la tabla de materiales a recibir
  const loadTblStoreMaterial = async (data) => {
    data = data.filter(
      (item) =>
        item.application_date !== "0000-00-00" &&
        item.delivery_date !== "0000-00-00" &&
        item.purchase_order !== ""
    );

    if ($.fn.dataTable.isDataTable("#tblStore")) {
      $("#tblStore").DataTable().clear().rows.add(data).draw();
      return;
    }

    tblStore = $("#tblStore").dataTable({
      destroy: true,
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      pageLength: 50,
      data: data,
      language: { url: "/assets/plugins/i18n/Spanish.json" },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data, type, full, meta) => meta.row + 1,
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Materia Prima",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Proveedor",
          data: "provider",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidad a Recibir",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            `${formatQuantity(data.quantity_requested, data.abbreviation)} ${
              data.abbreviation
            }`,
        },
        {
          title: "Orden de Compra",
          data: "purchase_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Acción",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => buildReceiveAction(data),
        },
      ],
      headerCallback: (thead) => {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
    });
  };

  // Función para construir las acciones para entregar material
  const buildDeliverAction = (data) => {
    if (
      data.delivery_store === 0 ||
      data.delivery_pending >= 0 ||
      data.id_user_delivered === 0
    ) {
      return `<button class="btn btn-info deliver" id="delivery">Entregar MP</button>`;
    }

    let fechaHora = new Date(data.delivery_date);
    let fechaHoraFormateada = `${fechaHora.toLocaleDateString("es-CO", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    })}<br>${fechaHora.toLocaleTimeString("es-CO", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    })}`;

    return `Entregado: ${data.firstname_delivered} ${data.lastname_delivered}<br>${fechaHoraFormateada}
    <a href="javascript:;">
      <i id="${data.id_material}" class="mdi mdi-playlist-check seeDeliverOC" data-toggle="tooltip" title="Ver Usuarios" style="font-size: 30px;color:black"></i>
    </a>`;
  };

  // Función para cargar la tabla de órdenes de almacén
  const loadTblStoreOrder = (data) => {
    tblStore = $("#tblStore").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      language: { url: "/assets/plugins/i18n/Spanish.json" },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data, type, full, meta) => meta.row + 1,
        },
        {
          title: "No Pedido",
          data: "num_order",
          className: "uniqueClassName dt-head-center",
          visible: false,
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Materia Prima",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Existencias",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            `${formatQuantity(data.quantity, data.abbreviation)} ${
              data.abbreviation
            }`,
        },
        {
          title: "Cantidad a Entregar",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            `${formatQuantity(data.reserved, data.abbreviation)} ${
              data.abbreviation
            }`,
        },
        {
          title: "Estado Entregas",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => {
            const store = parseFloat(data.delivery_store).toLocaleString(
              "es-CO",
              { minimumFractionDigits: 0, maximumFractionDigits: 2 }
            );
            const pending = parseFloat(data.delivery_pending).toLocaleString(
              "es-CO",
              { minimumFractionDigits: 0, maximumFractionDigits: 2 }
            );

            return pending > 0
              ? `Entregado: ${store}<br><span class="badge badge-warning">Pendiente: ${pending}</span>`
              : `Entregado: ${store}<br>Pendiente: ${pending}`;
          },
        },
        {
          title: "Acción",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => buildDeliverAction(data),
        },
      ],
      headerCallback: (thead) => {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
      rowGroup: {
        dataSrc: (row) =>
          `<th class="text-center" colspan="7" style="font-weight: bold;"> Orden Producción (${row.num_production}) - No Pedido (${row.num_order}) </th>`,
        startRender: (rows, group) => $("<tr/>").append(group),
        className: "odd",
      },
    });
  };

  loadAllData(1);
});
