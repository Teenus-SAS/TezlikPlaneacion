$(document).ready(function () {
  // Función para formatear números
  const formatNumber = (value, abbreviation) => {
    let number = parseFloat(value);
    if (abbreviation === "UND") {
      return number.toLocaleString("es-CO", { maximumFractionDigits: 0 });
    } else {
      return number.toLocaleString("es-CO", { minimumFractionDigits: 2 });
    }
  };
  
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
        render: (data) => formatNumber(data.quantity_material, data.abbreviation),
      },
      {
        title: "Stock Min",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: (data) => formatNumber(data.minimum_stock, data.abbreviation),
      },
      {
        title: "En Transito",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: (data) => formatNumber(data.transit, data.abbreviation),
      },
      {
        title: "Necesidad",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: (data) => formatNumber(data.need, data.abbreviation),
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
