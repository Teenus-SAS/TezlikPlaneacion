$(document).ready(function () {
  loadClients(1);

  loadAllData = async () => {
    try {
      const dataOrders = await searchData("/api/orders");

      orders = dataOrders;

      ordersIndicators(orders);
      loadTblOrders(orders);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const ordersIndicators = (data) => {
    let totalQuantity = 0;
    let completed = 0;
    let late = 0;
    let today = formatDate(new Date());

    if (data.length > 0) {
      let arrCompleted = data.filter(
        (item) => item.status == "DESPACHO" && item.min_date < item.office_date
      );
      let arrLate = data.filter((item) => item.min_date < today);

      totalQuantity = data.length;
      completed = (arrCompleted.length / totalQuantity) * 100;
      late = (arrLate.length / totalQuantity) * 100;
    }

    $("#lblTotal").html(
      ` Total: ${totalQuantity.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
    $("#lblCompleted").html(
      ` Completados: ${completed.toLocaleString("es-CO", {
        maximumFractionDigits: 2,
      })} %`
    );
    $("#lblLate").html(
      ` Atrasados: ${late.toLocaleString("es-CO", {
        maximumFractionDigits: 2,
      })} %`
    );
  };

  /* Cargar pedidos 
  const loadTblOrders = (data) => {
    tblOrder = $("#tblOrder").dataTable({
      destroy: true,
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
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            !data.delivery_date &&
            (data.status == "PROGRAMAR" ||
              data.status == "POR PROCESAR" ||
              data.status == "SIN FICHA TECNICA" ||
              data.status == "MAQUINA NO DISPONIBLE" ||
              data.status == "PERSONAL NO DISPONIBLE" ||
              data.status == "SIN MATERIA PRIMA")
              ? (action = `<a href="javascript:;" <i class="bx bx-edit-alt updateOrder" id="${data.id_order}" data-toggle='tooltip' title='Actualizar Pedido' style="font-size: 30px;"></i></a><a href="javascript:;" <i class="mdi mdi-delete-forever" id="${data.id_order}" data-toggle='tooltip' title='Eliminar Pedido' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`)
              : (action = "");

            return action;
          },
        },
        {
          title: "Fecha",
          data: "date_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Vendedor",
          data: null,
          className: "uniqueuniqueClassName dt-head-center",
          render: function (data) {
            return `${data.firstname} ${data.lastname}`;
          },
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueuniqueClassName dt-head-center",
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidad Requerida",
          data: "original_quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        {
          title: "F.Maxima",
          data: "max_date",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Estado",
          data: "status",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            if (data == "ENTREGADO") badge = "badge-success";
            else if (data == "SIN FICHA TECNICA" || data == "SIN MATERIA PRIMA" || data == "MAQUINA NO DISPONIBLE" || data == "PERSONAL NO DISPONIBLE")
              badge = "badge-danger";
            else if (data == "DESPACHO") badge = "badge-info";
            else badge = "badge-light";

            return `<span class="badge ${badge}">${data}</span>`;
          },
        },
        {
          title: "Inventario ABC",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: data["visible"],
          render: function (data) {
            if (data.classification == "A") badge = "badge-success";
            else if (data.classification == "B") badge = "badge-info";
            else badge = "badge-danger";
            return `<span class="badge ${badge}" style="font-size: large;">${data.classification}</span>`;
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
  }; */
  // Función para mapear los estados a clases de badges
  const getStatusBadge = (status) => {
    const statusBadgeMap = {
      "ENTREGADO": "badge-success",
      "SIN FICHA TECNICA": "badge-danger",
      "SIN MATERIA PRIMA": "badge-danger",
      "MAQUINA NO DISPONIBLE": "badge-danger",
      "PERSONAL NO DISPONIBLE": "badge-danger",
      "DESPACHO": "badge-info",
      "EN PRODUCCION": "badge-warning",
    };
    return statusBadgeMap[status] || "badge-light";
  };

  // Función para mapear las clasificaciones a badges
  const getClassificationBadge = (classification) => {
    const classificationBadgeMap = {
      "A": "badge-success",
      "B": "badge-info",
      "C": "badge-danger",
    };
    return classificationBadgeMap[classification] || "";
  };

  // Función para construir las acciones
  const buildActions = (data) => {
    if (!data.delivery_date && [
      "PROGRAMAR", "POR PROCESAR", "SIN FICHA TECNICA",
      "MAQUINA NO DISPONIBLE", "PERSONAL NO DISPONIBLE",
      "SIN MATERIA PRIMA"
    ].includes(data.status)) {
      return `
      <a href="javascript:;" class="updateOrder" id="${data.id_order}" data-toggle="tooltip" title="Actualizar Pedido" style="font-size: 30px;">
        <i class="bx bx-edit-alt"></i>
      </a>
      <a href="javascript:;" id="${data.id_order}" data-toggle="tooltip" title="Eliminar Pedido" style="font-size: 30px;color:red" onclick="deleteFunction()">
        <i class="mdi mdi-delete-forever"></i>
      </a>`;
    }
    return "";
  };

  // Función para cargar pedidos
  const loadTblOrders = (data) => {
    tblOrder = $("#tblOrder").dataTable({
      destroy: true,
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
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => buildActions(data),
        },
        { title: "Fecha", data: "date_order", className: "uniqueClassName dt-head-center" },
        { title: "Pedido", data: "num_order", className: "uniqueClassName dt-head-center" },
        {
          title: "Vendedor",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => `${data.firstname} ${data.lastname}`,
        },
        { title: "Cliente", data: "client", className: "uniqueClassName dt-head-center" },
        { title: "Referencia", data: "reference", className: "uniqueClassName dt-head-center" },
        { title: "Producto", data: "product", className: "uniqueClassName dt-head-center" },
        {
          title: "Cantidad Requerida",
          data: "original_quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        { title: "F.Maxima", data: "max_date", className: "uniqueClassName dt-head-center" },
        {
          title: "Estado",
          data: "status",
          className: "uniqueClassName dt-head-center",
          render: (data) => `<span class="badge ${getStatusBadge(data)}">${data}</span>`,
        },
        {
          title: "Inventario ABC",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: data["visible"],
          render: (data) => `<span class="badge ${getClassificationBadge(data.classification)}" style="font-size: large;">${data.classification}</span>`,
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

  loadAllData();
});
