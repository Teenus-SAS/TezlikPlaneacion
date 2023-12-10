$(document).ready(function () {
  /* Cargue tabla de Materias Primas */

  tblRawMaterials = $("#tblRawMaterials").dataTable({
    pageLength: 50,
    ajax: {
      url: "../../api/materials",
      dataSrc: "",
    },
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Referencia",
        data: "reference",
        className: "classCenter",
      },
      {
        title: "Materia Prima",
        data: "material",
        className: "classCenter",
      },
      {
        title: "Existencia",
        data: null,
        className: "classCenter",
        render: function (data) {
          data.unit == "UNIDAD"
            ? (number = data.quantity.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
            : (number = data.quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              }));
          return number;
        },
      },
      {
        title: "Unidad",
        data: "unit",
        className: "classCenter",
      },
      {
        title: "Reservado",
        data: null,
        className: "uniqueClassName",
        render: function (data) {
          data.unit == "UNIDAD"
            ? (number = data.reserved.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
            : (number = data.reserved.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              }));
          return number;
        },
      },
      {
        title: "Stock Min",
        data: null,
        className: "uniqueClassName",
        render: function (data) {
          data.unit == "UNIDAD"
            ? (number = data.minimum_stock.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
            : (number = data.minimum_stock.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              }));
          return number;
        },
      },
      {
        title: "Acciones",
        data: "id_material",
        className: "uniqueClassName",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
