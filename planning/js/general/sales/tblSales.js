$(document).ready(function () {
  const nombresMeses = {
    1: "Enero",
    2: "Febrero",
    3: "Marzo",
    4: "Abril",
    5: "Mayo",
    6: "Junio",
    7: "Julio",
    8: "Agosto",
    9: "Septiembre",
    10: "Octubre",
    11: "Noviembre",
    12: "Diciembre",
  };

  // Cargar tabla de Ventas
  tblSales = $("#tblSales").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    pageLength: 50,
    ajax: {
      url: "/api/unitSales",
      dataSrc: "",
    },
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
        data: "id_unit_sales",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateSale" data-toggle='tooltip' title='Actualizar Venta' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Venta' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
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
        title: "Año",
        data: "year",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Promedio Diario",
        data: "average_day",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Promedio Mensual",
        data: "average_month",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Enero",
        data: "jan",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Febrero",
        data: "feb",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Marzo",
        data: "mar",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Abril",
        data: "apr",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Mayo",
        data: "may",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Junio",
        data: "jun",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Julio",
        data: "jul",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Agosto",
        data: "aug",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Septiembre",
        data: "sept",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Octubre",
        data: "oct",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Noviembre",
        data: "nov",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Diciembre",
        data: "dece",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
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

  // Cargar tabla de Dias Ventas
  tblSalesDays = $("#tblSalesDays").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/saleDays",
      dataSrc: "",
    },
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
        title: "Año",
        data: "year",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Mes",
        data: "month",
        className: "uniqueClassName dt-head-center",
        render: function (data, type, row) {
          return nombresMeses[data];
        },
      },
      {
        title: "Dias",
        data: "days",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Acciones",
        data: "id_sale_day",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateDays" data-toggle='tooltip' title='Actualizar Dias' style="font-size: 30px;"></i></a>`;
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
});
