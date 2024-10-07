$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  loadTblPlanCiclesMachine = (idProduct, visible) => {
    tblPlanCiclesMachine = $("#tblPlanCiclesMachine").dataTable({
      autoWidth: false,
      fixedHeader: true,
      scrollCollapse: true,
      scrollY: "400px",
      destroy: true,
      pageLength: 50,

      ajax: {
        url: `/api/planCiclesMachine/${idProduct}`,
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
          title: "Proceso",
          data: "process",
          className: "text-center",
        },
        {
          title: "Máquina",
          data: "machine",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Operadores",
          data: "employees",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Und/Hora",
          data: "cicles_hour",
          className: "text-center",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Und/Turno",
          data: "units_turn",
          className: "text-center",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Und/Mes",
          data: "units_month",
          className: "text-center",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Maquina Alterna",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            return `<a href="javascript:;">
                    <i id="${data.id_alternal_machine}" class="${
              data.id_alternal_machine != 0
                ? "fas fa-check-square"
                : "fa fa-window-close"
            }" data-toggle='tooltip' title='${
              data.alternal_machine
            }' style="font-size:25px; color: ${
              data.id_alternal_machine == 0 ? "#ff0000" : "#7bb520"
            };"></i>
                  </a>`;
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let action;

            if (visible == true) {
              action = `<a href="javascript:;" <i id="upd-${data.id_cicles_machine}" class="bx bx-edit-alt updatePCMachine" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="ext-${data.id_cicles_machine}" class="bx bi bi-sliders alternalMachine" data-toggle='tooltip' title='Maquina Alterna' style="font-size: 30px;color:#d36e17;"></i></a>
                        <a href="javascript:;" <i id="${data.id_cicles_machine}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red" onclick="deleteMachine()"></i></a>`;
            } else {
              action = `<a href="javascript:;" <i id="upd-${data.id_cicles_machine}" class="bx bx-edit-alt updatePCMachine" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data.id_cicles_machine}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red" onclick="deleteMachine()"></i></a>`;
            }

            return action;
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
        let cicles_hour = 0;
        let units_turn = 0;
        let units_month = 0;

        for (i = 0; i < display.length; i++) {
          cicles_hour += parseFloat(data[display[i]].cicles_hour);
          units_turn += parseFloat(data[display[i]].units_turn);
          units_month += parseFloat(data[display[i]].units_month);
        }

        $("#lblTotalCicles").html(
          cicles_hour.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#lblTotalUnitsTurn").html(
          units_turn.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#lblTotalUnitsMonth").html(
          units_month.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
      },
    });
  };
});
