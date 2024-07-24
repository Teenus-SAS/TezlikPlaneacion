$(document).ready(function () {
  /* Cargue tabla de stock */

  tblRMStock = $("#tblRMStock").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/rMStock",
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
        title: "Medida",
        data: "abbreviation",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Proveedor",
        data: "client",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Plazo Min Despacho",
        data: "min_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Plazo Max Despacho",
        data: "max_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Cantidad Min de Venta",
        data: "min_quantity",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Tiempo Promedio Despacho",
        data: 'average',
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Acciones",
        data: "id_stock_material",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRMStock" data-toggle='tooltip' title='Actualizar Stock' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
