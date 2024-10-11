$(document).ready(function () {
  /* Cargue tabla de Materias Primas */
  flag_products_measure == "1" ? (visible = true) : (visible = false);

  tblRawMaterials = $("#tblRawMaterials").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    pageLength: 50,
    ajax: {
      url: "/api/materials",
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
        title: "Tipo",
        data: "material_type",
        className: "uniqueClassName dt-head-center",
        visible: visible,
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
        title: "Costo",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          data.unit == "UNIDAD"
            ? (number = parseFloat(data.cost).toLocaleString("es-CO", {
              maximumFractionDigits: 0,
            }))
            : (number = parseFloat(data.cost).toLocaleString("es-CO", {
              minimumFractionDigits: 2,
            }));
          return number;
        },
      },
      {
        title: "Acciones",
        data: "id_material",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterialsFunction()"></i></a>`;
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
    footerCallback: function (row, data, start, end, display) {
      let cost = 0;

      for (i = 0; i < display.length; i++) {
        cost += parseFloat(data[display[i]].cost);
      }

      $("#totalCost").html(
        cost.toLocaleString("es-CO", {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })
      );
    },
  });
});
