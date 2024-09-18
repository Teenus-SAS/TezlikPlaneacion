$(document).ready(function () {
  /* Cargue tabla de stock */

  tblPStock = $("#tblPStock").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/pStock",
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
        title: "Producto",
        data: "product",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Tiempo Mínimo de Producción",
        data: "min_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Tiempo Máximo de Producción",
        data: "max_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Clasificación",
        data: classification,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          if (data.classification == "A") badge = "badge-success";
          else if (data.classification == "B") badge = "badge-info";
          else badge = "badge-danger";
          return `<span class="badge ${badge}" style="font-size: large;">${data.classification}</span>`;
        },
      },
      {
        title: "Acciones",
        data: "id_stock_product",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePStock" data-toggle='tooltip' title='Actualizar Stock' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
