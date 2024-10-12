$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblEmployees = $("#tblEmployees").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
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
        title: "Disponible",
        data: null,
        className: "uniqueClassName dt-head-center ",
        render: function (data) {
          return `<a href="javascript:;">
                    <i id="${data.id_plan_payroll}" class="${
            data.status == 1 ? "bi bi-person-fill-check" : "bi bi-person-fill-x"
          } statusPY" data-toggle='tooltip' title='${
            data.status == 0 ? "Activar" : "Desactivar"
          } empleado' style="font-size:25px; color: ${
            data.status == 0 ? "#ff0000" : "#7bb520"
          };"></i>
                  </a>`;
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
        title: "Maquina",
        data: "machine",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Posición",
        data: "position",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Salario Base",
        data: 'salary',
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return parseFloat(data).toLocaleString("es-CO", {
            minimumFractionDigits: 0, maximumFractionDigits: 2
          });
        },
      },
      {
        title: "Salario neto",
        data: 'salary_net',
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return parseFloat(data).toLocaleString("es-CO", {
            minimumFractionDigits: 0, maximumFractionDigits: 2
          });
        },
      }, 
      {
        title: "Valor Minuto",
        data: 'minute_value',
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return parseFloat(data).toLocaleString("es-CO", {
            minimumFractionDigits: 2, maximumFractionDigits: 2
          });
        },
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
