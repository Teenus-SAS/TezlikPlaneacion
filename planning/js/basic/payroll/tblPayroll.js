$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblEmployees = $("#tblEmployees").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/planPayroll",
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
        title: "Nombres",
        data: "firstname",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Apellidos",
        data: "lastname",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Área",
        data: "area",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Proceso",
        data: "process",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Posición",
        data: "position",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Acciones",
        data: "id_plan_payroll",
        className: "uniqueClassName dt-head-center ",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updatePayroll" data-toggle='tooltip' title='Actualizar Nomina' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Nomina' style="font-size: 30px;color:red" onclick="deletePayrollFunction()"></i></a>`;
        },
      },
    ],
  });
});