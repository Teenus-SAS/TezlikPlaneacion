$(document).ready(function () {
  tblMaterials = $("#tblMaterials").dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: "/api/explosionMaterials",
      dataSrc: "",
    },
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
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
        data: "reference_material",
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
        title: "Inventario",
        data: "quantity_material",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 2, ""),
      },
      {
        title: "Stock Min",
        data: "minimum_stock",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 2, ""),
      },
      {
        title: "En Transito",
        data: "transit",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 2, ""),
      },
      {
        title: "Necesidad",
        data: "need",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 2, ""),
      },
      {
        title: "Disponible",
        data: "available",
        className: "uniqueClassName dt-head-center",
        render: function (data, type, full, meta) {
          const available = full.available;

          // Format the available value
          const formattedNumber = Math.round(available).toLocaleString("de-DE", { thousandsSeparator: "." });
          
          // Check if the available value is less than or equal to 0
          if (formattedNumber <= 0)
            return `<span class="badge badge-warning">Sin Stock: ${formattedNumber}</span>`;
          else return `${formattedNumber}`;
        },
      },
    ],
  });
});
