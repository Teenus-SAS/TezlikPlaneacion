$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  // Función para formatear los numeros
  const renderNum = (data) => {
    let num = parseFloat(data);
    const options = Math.abs(num) < 0.01
      ? { minimumFractionDigits: 2, maximumFractionDigits: 9 }
      : { maximumFractionDigits: 2 };

    num = num.toLocaleString('es-CO', options);
    return num;
  };

  tblMachines = $("#tblMachines").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    pageLength: 50,
    ajax: {
      url: "/api/machines",
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
        title: "Máquina",
        data: "machine",
        className: "uniqueClassName dt-head-center ",
      },
      {
        title: "Costo",
        data: "cost",
        className: "uniqueClassName dt-head-center",
        render: (data, type, row) => { return `$ ${renderNum(data)}` },
      },
      {
        title: "Años de Depreciación",
        data: "years_depreciation",
        className: "uniqueClassName dt-head-center",
        render: (data, type, row) => renderNum(data),
      },
      {
        title: "Depreciación X Minuto",
        data: "minute_depreciation",
        className: "uniqueClassName dt-head-center",
        render: (data, type, row) => renderNum(data),
      },
      {
        title: "Acciones",
        data: "id_machine",
        className: "uniqueClassName dt-head-center ",
        render: function (data) {
          return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateMachines" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red" onclick="deleteMachineFunction()"></i></a>`;
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
