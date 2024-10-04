$(document).ready(function () {
  // $('.selectNavigation').hide();

  $(".selectNavigation").click(function (e) {
    e.preventDefault();
    let data = [];

    if (this.id == "processOP")
      data = JSON.parse(sessionStorage.getItem("dataPOP"));
    else if (this.id == "completeOP")
      data = JSON.parse(sessionStorage.getItem("dataPON"));

    loadTblProductionOrders(data);
  });

  loadData = async (op) => {
    try {
      const [dataPO, dataFTMaterials, dataStore] = await Promise.all([
        searchData("/api/productionOrder"),
        op == 1 ? searchData("/api/allProductsMaterials") : "",
        op == 1 ? searchData("/api/allStore") : "",
      ]);

      let dataPOP = dataPO.filter((item) => item.flag_op == 0);
      let dataPON = dataPO.filter((item) => item.flag_op == 1);

      sessionStorage.setItem("dataPOP", JSON.stringify(dataPOP));
      sessionStorage.setItem("dataPON", JSON.stringify(dataPON));
      sessionStorage.setItem("dataOP", JSON.stringify(dataPO));

      if (op == 1) {
        sessionStorage.setItem(
          "dataFTMaterials",
          JSON.stringify(dataFTMaterials)
        );
        sessionStorage.setItem("dataAllStore", JSON.stringify(dataStore));
      }

      let element = document.getElementsByClassName("selectNavigation");

      if (element[1].className.includes("active"))
        loadTblProductionOrders(dataPON);
      else loadTblProductionOrders(dataPOP);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  // const loadTblProductionOrders = (data) => { 
  //   tblProductionOrders = $("#tblProductionOrders").dataTable({
  //     destroy: true,
  //     pageLength: 50,
  //     data: data,
  //     language: {
  //       url: "/assets/plugins/i18n/Spanish.json",
  //     },
  //     columns: [
  //       {
  //         title: "No.",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data, type, full, meta) {
  //           return meta.row + 1;
  //         },
  //       },
  //       {
  //         title: "OP",
  //         data: "num_production",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       {
  //         title: "Referencia",
  //         data: "reference",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       {
  //         title: "Producto",
  //         data: "product",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       {
  //         title: "Maquina",
  //         data: "machine",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       {
  //         title: "Cantidades",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data, type, full, meta) {
  //           const quantityOrder = full.quantity_order;
  //           const quantityProgramming = full.quantity_programming;

  //           return `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}`;
  //         },
  //       },
  //       {
  //         title: "Cliente",
  //         data: "client",
  //         className: "uniqueClassName dt-head-center",
  //       },

  //       {
  //         title: "Fechas",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         width: "200px",
  //         render: function (data, type, full, meta) {
  //           const minDate = full.min_date_programming;
  //           const maxDate = full.max_date_programming;

  //           return `Inicio: ${moment(minDate).format(
  //             "DD/MM/YYYY HH:mm A"
  //           )}<br>Fin: ${moment(maxDate).format("DD/MM/YYYY HH:mm A")}`;
  //         },
  //       },
  //       {
  //         title: "Acciones",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data) { 
  //           let action = '';
  //           if (data.flag_op == 0) {
  //             action = `<a href="javascript:;">
  //                         <i id="${data.id_programming}" 
  //                             class="
  //                               ${data.flag_cancel == 0 ? "bi bi-x-circle-fill" : "bi bi-check-circle-fill"} changeFlagOP
  //                               " 
  //                             data-toggle='tooltip'
  //                             title='${data.flag_cancel == 0 ? "Anular" : "Aprobar"} Orden de Produccion' 
  //                             style="font-size:25px; color: ${data.flag_cancel == 0 ? "#ff0000" : "#7bb520"};"
  //                         ></i>
  //                       </a>
  //                       <a href="/planning/details-production-order">
  //                         <i id="${data.id_programming}" class="fas fa-eye" data-toggle='tooltip' title='Ver Orden de Producción' style="font-size: 30px;color:black" onclick="seePO()"></i>
  //                       </a>`;
  //           } else {
  //             action = `<a href="/planning/details-production-order">
  //                         <i id="${data.id_programming}" class="fas fa-eye" data-toggle='tooltip' title='Ver Orden de Producción' style="font-size: 30px;color:black" onclick="seePO()"></i>
  //                       </a>`;
  //           }
            
  //           return action;
  //         },
  //       },
  //     ],
  //     headerCallback: function (thead, data, start, end, display) {
  //       $(thead).find("th").css({
  //         "background-color": "#386297",
  //         color: "white",
  //         "text-align": "center",
  //         "font-weight": "bold",
  //         padding: "10px",
  //         border: "1px solid #ddd",
  //       });
  //     },
  //   });
  // };

  // Función para construir las acciones
  const buildActions = (data) => {
    if (data.flag_op == 0) {
      return `
      <a href="javascript:;">
        <i id="${data.id_programming}" 
           class="${data.flag_cancel == 0 ? "bi bi-x-circle-fill" : "bi bi-check-circle-fill"} changeFlagOP" 
           data-toggle="tooltip" 
           title="${data.flag_cancel == 0 ? "Anular" : "Aprobar"} Orden de Produccion" 
           style="font-size:25px; color:${data.flag_cancel == 0 ? "#ff0000" : "#7bb520"};">
        </i>
      </a>
      <a href="/planning/details-production-order">
        <i id="${data.id_programming}" class="fas fa-eye" data-toggle="tooltip" title="Ver Orden de Producción" style="font-size:30px;color:black" onclick="seePO()"></i>
      </a>`;
    }
    return `
    <a href="/planning/details-production-order">
      <i id="${data.id_programming}" class="fas fa-eye" data-toggle="tooltip" title="Ver Orden de Producción" style="font-size:30px;color:black" onclick="seePO()"></i>
    </a>`;
  };

  // Función para formatear fechas
  const formatDateRange = (minDate, maxDate) => {
    const format = "DD/MM/YYYY HH:mm A";
    return `Inicio: ${moment(minDate).format(format)}<br>Fin: ${moment(maxDate).format(format)}`;
  };

  // Función para cargar pedidos de producción
  const loadTblProductionOrders = (data) => {
    tblProductionOrders = $("#tblProductionOrders").dataTable({
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
        { title: "OP", data: "num_production", className: "uniqueClassName dt-head-center" },
        { title: "Referencia", data: "reference", className: "uniqueClassName dt-head-center" },
        { title: "Producto", data: "product", className: "uniqueClassName dt-head-center" },
        { title: "Maquina", data: "machine", className: "uniqueClassName dt-head-center" },
        {
          title: "Cantidades",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => `Pedido: ${data.quantity_order}<br>Fabricar: ${data.quantity_programming}`,
        },
        { title: "Cliente", data: "client", className: "uniqueClassName dt-head-center" },
        {
          title: "Fechas",
          data: null,
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: (data) => formatDateRange(data.min_date_programming, data.max_date_programming),
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => buildActions(data),
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

  loadData(1);
});
