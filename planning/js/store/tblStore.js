$(document).ready(function () {
  $("#btnExportStore").hide();

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    let tables = document.getElementsByClassName("dataTable");

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = "100%";
      attr = tables[i].firstElementChild;
      attr.style.width = "100%";
    }
  });

  loadAllData = async () => {
    try {
      const [dataRequisitions, dataStore] = await Promise.all([
        searchData("/api/requisitionsMaterials"),
        searchData("/api/store"),
      ]);

      let arr = assignOpToGroups(dataStore, "id_programming");

      loadTblReceiveOC(dataRequisitions);
      loadTblDeliverOP(arr);

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
  const loadTblReceiveOC = async (data) => {
    data = data.filter(
      (item) =>
        item.application_date !== "0000-00-00" &&
        item.delivery_date !== "0000-00-00" &&
        item.purchase_order !== ""
    );

    if ($.fn.dataTable.isDataTable("#tblReceiveOC")) {
      $("#tblReceiveOC").DataTable().clear().rows.add(data).draw();
      return;
    }

    tblReceiveOC = $("#tblReceiveOC").dataTable({
      destroy: true,
      autoWidth: false,
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
    
    if (data.id_user_delivered == 0) {
      if (data.flag_op == 1)
        return '';
      else
        return `<button class="btn btn-info deliver" id="delivery">Entregar MP</button>`;
    }

    let check_quantity = parseFloat(data.reserved) - parseFloat(data.delivery_pending);

    if (check_quantity > 0 && data.delivery_pending > 0) {
      if (data.id_materials_component_user != 0) {
        if (data.flag_op == 1)
          return '';
        else
          return `<button class="btn btn-info deliver" id="delivery">Entregar MP</button>`;
      } else
        return `
          <button class="btn btn-info deliver" id="delivery">Entregar MP</button>
          <a href="javascript:;">
            <i id="${data.id_materials_component_user}" class="bi bi-info-circle seeUPDTDeliverOC" data-toggle="tooltip" title="Ver Detalle" style="font-size: 30px;color:black"></i>
          </a>
        `;
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


    if (data.id_materials_component_user != 0)
      return `Entregado: ${data.firstname_delivered} ${data.lastname_delivered}<br>${fechaHoraFormateada}
        <a href="javascript:;">
          <i id="${data.id_material}" class="mdi mdi-playlist-check seeDeliverOC" data-toggle="tooltip" title="Ver Detalle" style="font-size: 30px;"></i>
        </a>`;
    else
      return `
          Entregado: ${data.firstname_delivered} ${data.lastname_delivered}<br>${fechaHoraFormateada}
          <a href="javascript:;">
            <i id="${data.id_materials_component_user}" class="bi bi-info-circle seeUPDTDeliverOC" data-toggle="tooltip" title="Ver Detalle" style="font-size: 30px; color:black"></i>
          </a>
        `;
  };
  // const buildDeliverAction = (data) => {
  //   const formatDateTime = (date) => {
  //     const fechaHora = new Date(date);
  //     return `${fechaHora.toLocaleDateString("es-CO", {
  //       day: "2-digit",
  //       month: "2-digit",
  //       year: "numeric",
  //     })}<br>${fechaHora.toLocaleTimeString("es-CO", {
  //       hour: "2-digit",
  //       minute: "2-digit",
  //       hour12: true,
  //     })}`;
  //   };

  //   const deliverButton = `<button class="btn btn-info deliver" id="delivery">Entregar MP</button>`;
  //   const detailIcon = (id, style = "font-size: 30px; color:black") =>
  //     `<a href="javascript:;">
  //     <i id="${id}" class="bi bi-info-circle seeUPDTDeliverOC" data-toggle="tooltip" title="Ver Detalle" style="${style}"></i>
  //   </a>`;

  //   if (data.id_user_delivered === 0) {
  //     return data.flag_op === 1 ? "" : deliverButton;
  //   }

  //   const checkQuantity = parseFloat(data.reserved) - parseFloat(data.delivery_pending);

  //   if (checkQuantity > 0 && data.delivery_pending > 0) {
  //     if (data.id_materials_component_user !== 0) {
  //       return data.flag_op === 1 ? "" : deliverButton;
  //     }
  //     return `${deliverButton} ${detailIcon(data.id_materials_component_user)}`;
  //   }

  //   const formattedDateTime = formatDateTime(data.delivery_date);
  //   const deliveredInfo = `Entregado: ${data.firstname_delivered} ${data.lastname_delivered}<br>${formattedDateTime}`;

  //   if (data.id_materials_component_user !== 0) {
  //     return `${deliveredInfo}
  //     <a href="javascript:;">
  //       <i id="${data.id_material}" class="mdi mdi-playlist-check seeDeliverOC" data-toggle="tooltip" title="Ver Detalle" style="font-size: 30px;"></i>
  //     </a>`;
  //   }

  //   return `${deliveredInfo} ${detailIcon(data.id_materials_component_user)}`;
  // };

  // Función para cargar la tabla de órdenes de almacén
  const loadTblDeliverOP = (data) => {
    tblDeliverOP = $("#tblDeliverOP").dataTable({
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

  loadAllData();
});
