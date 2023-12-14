$(document).ready(function () {
  tblProductionOrders = $("#tblProductionOrders").dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: "/api/productionOrder",
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
      // {
      //   title: 'Acciones',
      //   data: 'id_programming',
      //   className: 'uniqueClassName',
      //   render: function (data) {
      //     return `
      //         <a href="javascript:;" <i id="${data}" class="bi bi-bookmark-plus-fill changeStatus" data-toggle='tooltip' title='Crear Orden de Produccion' style="font-size: 30px;"></i></a>
      //         <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
      //         <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
      //   },
      // },
    ],
  });
});
