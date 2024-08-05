$(document).ready(function () {
  // $('.selectNavigation').hide();

  $('.selectNavigation').click(function (e) {
    e.preventDefault();
    let data = [];

    if (this.id == 'processOP')
      data = JSON.parse(sessionStorage.getItem('dataPOP'));
    else if (this.id == 'completeOP')
      data = JSON.parse(sessionStorage.getItem('dataPON'));

    loadTblProductionOrders(data);
  });

  loadData = async (op) => {
    try { 
      const [dataPO, dataFTMaterials] = await Promise.all([
        searchData('/api/productionOrder'),
        op == 1 ? searchData('/api/allProductsMaterials') : ''
      ]);

      let dataPOP = dataPO.filter(item => item.status == 'EN PRODUCCION');
      let dataPON = dataPO.filter(item => item.status != 'EN PRODUCCION');

      sessionStorage.setItem('dataPOP', JSON.stringify(dataPOP));
      sessionStorage.setItem('dataPON', JSON.stringify(dataPON));

      if(op == 1)
        sessionStorage.setItem('dataFTMaterials', JSON.stringify(dataFTMaterials));
      
      loadTblProductionOrders(dataPOP); 
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const loadTblProductionOrders = (data) => {
    if ($.fn.dataTable.isDataTable("#tblProductionOrders")) {
      $("#tblProductionOrders").DataTable().clear();
      $("#tblProductionOrders").DataTable().rows.add(data).draw();
      return;
    }

    tblProductionOrders = $("#tblProductionOrders").dataTable({
      // destroy: true,
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
          title: "OP",
          data: "op",
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
          render: function (data, type, full, meta) {
            const quantityOrder = full.quantity_order;
            const quantityProgramming = full.quantity_programming;

            return `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}`;
          },
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName dt-head-center",
        },

        {
          title: "Fechas",
          data: null,
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: function (data, type, full, meta) {
            const minDate = full.min_date;
            const maxDate = full.max_date;

            return `Inicio: ${moment(minDate).format(
              "DD/MM/YYYY HH:mm A"
            )}<br>Fin: ${moment(maxDate).format("DD/MM/YYYY HH:mm A")}`;
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            if (data.status == "EN PRODUCCION") {
              return `
                <a href="/planning/details-production-order" <i id="${data.id_programming}" class="mdi mdi-playlist-check" data-toggle='tooltip' title='Ver Orden' style="font-size: 30px;color:black" onclick="seePO()"></i></a>
                <button type="button" id="${data.status}" class="btn btn-sm btn-warning changeStatus" style="font-size: 12px;">Fabricado</button>
              `;
            } else {
              return "";
            }
          },
        },
      ],
    });
  }

  loadData(1);
});
