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
        searchData("/api/requisitions"),
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
        url: "/assets/plugins/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
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
          render: function (data, type, row) {
            let quantity = parseFloat(data.quantity_requested);

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              });

            return `${quantity} ${data.abbreviation}`;
          },
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
          render: function (data) {
            if (
              !data.admission_date ||
              data.admission_date == "0000-00-00 00:00:00"
            )
              action = `<button class="btn btn-info changeDateMP" id="delivery">Recibir MP</button>`;
            else {
              action = `Recibido: <br>${data.firstname_deliver} ${data.lastname_deliver}<br>${data.admission_date}
                        <a href="javascript:;">
                          <i id="${data.id_requisition}" class="mdi mdi-playlist-check seeReceiveOC" data-toggle='tooltip' title='Ver Usuarios' style="font-size: 30px;color:black"></i>
                        </a>`;
            }

            return action;
          },
        },
      ],
      headerCallback: function (thead, data, start, end, display) {
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

  // Entregar
  loadTblStoreOrder = (data) => {
    tblStore = $("#tblStore").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      language: {
        url: "/assets/plugins/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
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
        // {
        //   title: "Unidad",
        //   data: "abbreviation",
        //   className: "uniqueClassName dt-head-center",
        // },
        {
          title: "Existencias",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let quantity = parseFloat(data.quantity);

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              });

            return `${quantity} ${data.abbreviation}`;
          },
        },
        {
          title: "Cantidad a Entregar",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let reserved = parseFloat(data.reserved);

            if (data.abbreviation === "UND")
              reserved = Math.floor(reserved).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              reserved = reserved.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              });

            return reserved;
          },
        },
        {
          title: "Estado Entregas",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            const store = full.delivery_store;
            const pending = full.delivery_pending;
            // const deliver = full.deliver;
            if (pending > 0) {
              return `Entregado: ${store.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}<br><span class="badge badge-warning">Pendiente: ${pending.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}</span>`;
            }
            else {
              return `Entregado: ${store.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}<br>Pendiente: ${pending.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`;
            }
          },
        },
        {
          title: "Acción",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            if (
              !data.delivery_date ||
              data.delivery_date == "0000-00-00 00:00:00"
            )
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

              action = `Entregado: ${data.firstname_delivered} ${data.lastname_delivered}<br>${fechaHoraFormateada}
              <a href="javascript:;">
                          <i id="${data.id_material}" class="mdi mdi-playlist-check seeDeliverOC" data-toggle='tooltip' title='Ver Usuarios' style="font-size: 30px;color:black"></i>
              </a>`;
            }

            return action;
          },
        },
      ],
      headerCallback: function (thead, data, start, end, display) {
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
        dataSrc: function (row) {
          return `<th class="text-center" colspan="7" style="font-weight: bold;"> No Pedido - ${row.num_order} Orden Produccion - ${row.op} </th>`;
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
