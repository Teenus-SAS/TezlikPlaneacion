$(document).ready(function () {
  tblMaterials = $("#tblMaterials").dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: "/api/explosionMaterials",
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
      // {
      //   title: "Pedido",
      //   data: "num_order",
      //   className: "uniqueClassName dt-head-center",
      // },
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
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          let quantity = parseFloat(data.quantity_material);

          if (data.abbreviation === "UND")
            quantity = quantity.toLocaleString("es-CO", {
              maximumFractionDigits: 0,
            });
          else
            quantity = quantity.toLocaleString("es-CO", {
              minimumFractionDigits: 2,
            });

          return quantity;
        },
      },
      {
        title: "Stock Min",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          let minimum_stock = parseFloat(data.minimum_stock);

          if (data.abbreviation === "UND")
            minimum_stock = minimum_stock.toLocaleString("es-CO", {
              maximumFractionDigits: 0,
            });
          else
            minimum_stock = minimum_stock.toLocaleString("es-CO", {
              minimumFractionDigits: 2,
            });

          return minimum_stock;
        },
      },
      {
        title: "En Transito",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          let transit = parseFloat(data.transit);

          if (data.abbreviation === "UND")
            transit = transit.toLocaleString("es-CO", {
              maximumFractionDigits: 0,
            });
          else
            transit = transit.toLocaleString("es-CO", {
              minimumFractionDigits: 2,
            });

          return transit;
        },
      },
      {
        title: "Necesidad",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          let need = parseFloat(data.need);

          if (data.abbreviation === "UND")
            need = need.toLocaleString("es-CO", { maximumFractionDigits: 0 });
          else
            need = need.toLocaleString("es-CO", { minimumFractionDigits: 2 });

          return need;
        },
      },
      {
        title: "Disponible",
        data: "available",
        className: "uniqueClassName dt-head-center",
        render: function (data, type, full, meta) {
          const available = parseFloat(full.available);

          // Format the available value
          const formattedNumber = Math.round(available).toLocaleString(
            "de-DE",
            { thousandsSeparator: "." }
          );

          // Check if the available value is less than or equal to 0
          if (formattedNumber <= 0)
            return `<span class="badge badge-warning">Sin Stock: ${formattedNumber}</span>`;
          else return `${formattedNumber}`;
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
