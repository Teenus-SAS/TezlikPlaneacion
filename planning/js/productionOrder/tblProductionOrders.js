$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();
    let data = [];

    if (this.id == "processOP")
      data = JSON.parse(sessionStorage.getItem("dataPOP"));
    else if (this.id == "completeOP")
      data = JSON.parse(sessionStorage.getItem("dataPON"));

    loadTblProductionOrders(data);
  });

  loadData = async () => {
    try {
      let dataPO = await searchData("/api/productionOrder");

      if (type_machine_op != '0') {
        dataPO = dataPO.filter(item => item.id_machine == type_machine_op);
      }

      let dataPOP = [];
      let dataPON = [];

      if (flag_type_program == 0) {
        dataPOP = dataPO.filter((item) => item.flag_op == 0);
        dataPON = dataPO.filter((item) => item.flag_op == 1);
      } else {
        const groupedData = groupBy(dataPO, 'num_production');
        dataPO = [];

        for (let i = 0; i < groupedData.length; i++) {
          groupedData[i].sort((a, b) => b.id_programming - a.id_programming);
          dataPO.push(groupedData[i][groupedData[i].length - 1]);
        }

        dataPOP = dataPO.filter((item) => item.route_programming != item.route_cicle || item.flag_op == 0);
        dataPON = dataPO.filter((item) => item.route_programming == item.route_cicle && item.flag_op == 1);
      }

      sessionStorage.setItem("dataPOP", JSON.stringify(dataPOP));
      sessionStorage.setItem("dataPON", JSON.stringify(dataPON));
      sessionStorage.setItem("dataOP", JSON.stringify(dataPO));

      let element = document.getElementsByClassName("selectNavigation");

      if (element[1].className.includes("active"))
        loadTblProductionOrders(dataPON);
      else loadTblProductionOrders(dataPOP);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const buildActions = (data) => {
    if (data.flag_op == 0) {
      return `
      <a href="javascript:;">
        <i id="${data.id_programming}" 
           class="${
             data.flag_cancel == 0
               ? "bi bi-x-circle-fill"
               : "bi bi-check-circle-fill"
           } changeFlagOP" 
           data-toggle="tooltip" 
           title="${
             data.flag_cancel == 0 ? "Anular" : "Aprobar"
           } Orden de Produccion" 
           style="font-size:25px; color:${
             data.flag_cancel == 0 ? "#ff0000" : "#7bb520"
           };">
        </i>
      </a>
      <a href="/planning/details-production-order">
        <i id="${
          data.id_programming
        }" class="fas fa-eye" data-toggle="tooltip" title="Ver Orden de Producción" style="font-size:30px;color:black" onclick="seePO()"></i>
      </a>`;
    }
    return `
    <a href="/planning/details-production-order">
      <i id="${data.id_programming}" class="fas fa-eye" data-toggle="tooltip" title="Ver Orden de Producción" style="font-size:30px;color:black" onclick="seePO()"></i>
    </a>`;
  };

  // Función para cargar pedidos de producción
  const loadTblProductionOrders = (data) => {
    tblProductionOrders = $("#tblProductionOrders").dataTable({
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      dom: "t",
      paging: false,
      info: false,
      searching: false,
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
          title: "OP",
          data: "num_production",
          className: "uniqueClassName dt-head-center",
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
          title: "Maquina",
          data: "machine",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidades",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            `Pedido: ${data.quantity_order}<br>Fabricar: ${data.quantity_programming}`,
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: `${flag_type_program == 0 ? "Fechas" : "Fecha Inicial"}`,
          data: null,
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: function (data) {
            
            if (flag_type_program == 0) { 
              const format = "DD/MM/YYYY HH:mm A";
              let min_date = moment(data.min_date_programming).format(format);
              let max_date = moment(data.max_date_programming).format(format);
              
              !min_date || min_date == 'Invalid date' ? min_date = '' : min_date;
              !max_date || max_date == "Invalid date" ? max_date = '' : max_date;
              
              return `Inicio: ${min_date}<br>Fin: ${max_date}`;
            } else {
              const format = "DD/MM/YYYY";
              
              let min_date = moment(data.min_date_programming).format(format); 
              !min_date || min_date == 'Invalid date' ? min_date = '' : min_date;

              return min_date;
            }
          },
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

  loadData();
});
