$(document).ready(function () {
  /* Cargue tabla de stock */

  tblStock = $("#tblStock").dataTable({
    pageLength: 50,
    ajax: {
      url: "../../api/stock",
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
        title: "Proveedor",
        data: "client",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Plazo Max",
        data: "max_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Plazo Habitual",
        data: "usual_term",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Acciones",
        data: "id_stock",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateStock" data-toggle='tooltip' title='Actualizar Stock' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
