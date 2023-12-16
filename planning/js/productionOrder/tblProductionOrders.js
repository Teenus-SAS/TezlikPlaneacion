$(document).ready(function () {
  // $('.selectNavigation').hide();

  $('.selectNavigation').click(function (e) {
    e.preventDefault();
    
    if (this.id == 'processOP')
      loadTblProductionOrders(dataOP);
    else if (this.id == 'completeOP')
      loadTblProductionOrders(dataNOP);
  });

  loadData = async () => {
    try {
      const data = await searchData('/api/productionOrder');

      dataOP = data.filter(item => item.status == 'En Produccion');
      dataNOP = data.filter(item => item.status != 'En Produccion');
      
      loadTblProductionOrders(dataOP);
      // $('.selectNavigation').show();
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadTblProductionOrders = (data) => {
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
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName",
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName",
        },
        {
          title: "Maquina",
          data: "machine",
          className: "uniqueClassName",
        },
        {
          title: "Cantidades",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            const quantityOrder = full.quantity_order;
            const quantityProgramming = full.quantity_programming;

            return `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}`;
          },
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName",
        },

        {
          title: "Fechas",
          data: null,
          className: "uniqueClassName",
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
          data: "status",
          className: "uniqueClassName",
          render: function (data) {
            if (data == "En Produccion") {
              return `<button type="button" id="${data}" class="btn btn-sm btn-warning changeStatus" style="font-size: 12px;">Fabricado</button>`;
            } else {
              return "";
            }
          },
        },
      ],
    });
  }

  loadData();
});
