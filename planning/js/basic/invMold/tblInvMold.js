// var editor;

$(document).ready(function () {
  /* Cargue tabla de Moldes */
  tblInvMold = $("#tblInvMold").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    pageLength: 50,
    ajax: {
      url: "/api/invMolds",
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "Activo",
        data: null,
        className: "dt-body-center",
        render: function (data) {
          if (data.active == 1) checked = "checked";
          else checked = "";
          return `<input type="checkbox" id="check-${data.id_mold}" onclick="activeMold(${data.id_mold})" ${checked}>`;
        },
      },
      {
        title: "Referencia",
        data: "reference",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Molde",
        data: "mold",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Tiempo Montaje en Produccion (Horas)",
        data: "assembly_production",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Tiempo Montaje (Minutos)",
        data: "assembly_time",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "N° Cavidades",
        data: "cavity",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "N° Cavidades Disponibles",
        data: "cavity_available",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      // {
      //   title: 'Tiempo Montaje (Minutos)',
      //   data: 'assembly_time',
      //   className: 'uniqueClassName dt-head-center',
      // },
      {
        title: "Acciones",
        data: "id_mold",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
                <a href="javascript:;" <i class="bx bx-edit-alt updateMold" id="${data}" data-toggle='tooltip' title='Actualizar Molde' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever deleteMold" id="${data}" data-toggle='tooltip' title='Eliminar Molde' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
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
