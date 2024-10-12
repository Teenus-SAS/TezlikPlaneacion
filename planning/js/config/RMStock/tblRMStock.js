$(document).ready(function () {
  $(".selectMaterial").change(function (e) {
    e.preventDefault();
    let id = this.value;

    let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));
    let arr = dataMaterials.find(item => item.id_material == id);
    
    if (arr.origin != 0) {
      let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
      let prod = dataProducts.find(item => item.id_product == arr.origin);

      $("#rMMin").val(prod.min_term);
      $("#rMMax").val(prod.max_term);
    }
  });

  /* Cargue tabla de stock */
  tblRMStock = $("#tblRMStock").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
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
        data: "average",
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
