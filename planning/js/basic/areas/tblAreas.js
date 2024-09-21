$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblAreas = $("#tblAreas").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/planAreas",
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName dt-head-center ",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Areas",
        data: "area",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Acciones",
        data: "id_plan_area",
        className: "uniqueClassName dt-head-center ",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateArea" data-toggle='tooltip' title='Actualizar Area' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Area' style="font-size: 30px;color:red" onclick="deleteAreaFunction()"></i></a>`;
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
