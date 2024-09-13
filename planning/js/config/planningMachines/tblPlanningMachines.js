$(document).ready(function () {
  let = opcionesDeFormato = {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  };

  // Mostrar Tabla planeacion maquinas
  tblPlanMachines = $("#tblPlanMachines").dataTable({
    pageLength: 50,
    ajax: {
      url: "/api/planningMachines",
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
        title: "Acciones",
        data: "id_program_machine",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updatePMachines" data-toggle='tooltip' title='Actualizar Plan Maquina' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Plan Maquina' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
      {
        title: "Disponible",
        data: null,
        className: "uniqueClassName dt-head-center ",
        render: function (data) {
          return `<a href="javascript:;">
                    <i id="${data.id_program_machine}" class="${
            data.status == 0 ? "fa fa-times" : "fas fa-check"
          } statusPM" data-toggle='tooltip' title='${
            data.status == 0 ? "Activar" : "Desactivar"
          } maquina' style="font-size:25px; color: ${
            data.status == 0 ? "#7bb520" : "#ff0000"
          };"></i>
                  </a>`;
        },
      },
      {
        title: "Máquina/Proceso Manual",
        data: "machine",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Trabajadores",
        data: "number_workers",
        className: "text-center",
      },
      {
        title: "Horas Dia",
        data: "hours_day",
        className: "text-center",
      },
      {
        title: "Total Turnos",
        data: "work_shift",
        className: "text-center",
      },
      {
        title: "Horario",
        data: null,
        className: "text-center",
        render: function (data, type, full, meta) {
          const hourStart = moment(full.hour_start.toFixed(2), [
            "HH:mm",
          ]).format("h:mm A");
          const hourEnd = moment(full.hour_end.toFixed(2), ["HH:mm"]).format(
            "h:mm A"
          );

          return `Inicio: ${hourStart}<br>Fin: ${hourEnd}`;
        },
      },
      {
        title: "Enero",
        data: "january",
        className: "text-center",
      },
      {
        title: "Febrero",
        data: "february",
        className: "text-center",
      },
      {
        title: "Marzo",
        data: "march",
        className: "text-center",
      },
      {
        title: "Abril",
        data: "april",
        className: "text-center",
      },
      {
        title: "Mayo",
        data: "may",
        className: "text-center",
      },
      {
        title: "Junio",
        data: "june",
        className: "text-center",
      },
      {
        title: "Julio",
        data: "july",
        className: "text-center",
      },
      {
        title: "Agosto",
        data: "august",
        className: "text-center",
      },
      {
        title: "Septiembre",
        data: "september",
        className: "text-center",
      },
      {
        title: "Octubre",
        data: "october",
        className: "text-center",
      },
      {
        title: "Noviembre",
        data: "november",
        className: "text-center",
      },
      {
        title: "Diciembre",
        data: "december",
        className: "text-center",
      },
    ],
  });

  $("#idMachine").change(function (e) {
    e.preventDefault();

    let dataMachines = JSON.parse(sessionStorage.getItem("machinesData"));
    let data = dataMachines.find((item) => item.id_machine == this.value);
    $("#numberWorkers").val(data.employees);
  });
});
