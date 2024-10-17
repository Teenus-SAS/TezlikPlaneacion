$(document).ready(function () {
  flag_products_measure == "1" ? (visible = true) : (visible = false);

  tblProducts = $("#tblProducts").dataTable({
    destroy: true,
    pageLength: 50,
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    ajax: {
      url: "/api/productsMeasures",
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "Acciones",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: (data) => `
          <a href="javascript:;">
            <i id="${data.id_product}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Producto' 
               style="font-size: 30px; color:green" onclick="copyFunction()">
            </i>
          </a>
          <a href="javascript:;">
            <i id="${data.id_product}" class="${
          data.composite == 0
            ? "bi bi-plus-square-fill"
            : "bi bi-dash-square-fill"
        } composite" data-toggle='tooltip' title='${
          data.composite == 0 ? "Agregar" : "Eliminar"
        } a producto compuesto' style="font-size:25px; color: #ffaa00;"></i>
          </a>
          <a href="javascript:;">
            <i id="upd-${
              data.id_product_measure
            }" class="bx bx-edit-alt updatePMeasure" data-toggle='tooltip' title='Actualizar Medida' style="font-size: 30px;"></i>
          </a>
          <a href="javascript:;">
            <i id="${
              data.id_product_measure
            }" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Medida' style="font-size: 30px;color:red" onclick="deletePMeasureFunction()"></i>
          </a>
        `,
      },
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
        title: "Imagen",
        data: "img",
        className: "uniqueClassName dt-head-center",
        render: (data) =>
          data
            ? `<img src="${data}" alt="" style="width:30px;border-radius:100px">`
            : "",
      },
      {
        title: "Tipo",
        data: "product_type",
        visible: visible,
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Ancho",
        data: "width",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Alto",
        data: "high",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Largo Total",
        data: "length",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Largo Útil",
        data: "useful_length",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Ancho Total",
        data: "total_width",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Ventanilla/Und x Tamaño",
        data: "window",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
      },
      {
        title: "Tinta",
        data: "inks",
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: (data) =>
          parseFloat(data).toLocaleString("es-CO", {
            maximumFractionDigits: 2,
          }),
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
