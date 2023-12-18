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
      url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
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
        title: "Máquina",
        data: "machine",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Trabajadores",
        data: "number_workers",
        className: "text-center",
      },
      {
        title: "H.Dia",
        data: "hours_day",
        className: "text-center",
      },
      {
        title: "Horas",
        data: null,
        className: "text-center",
        render: function (data, type, full, meta) {
          const hourStart = moment(full.hour_start, ["HH:mm"]).format("h:mm A");
          const hourEnd = moment(full.hour_end, ["HH:mm"]).format("h:mm A");

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
      {
        title: "Acciones",
        data: "id_program_machine",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePMachines" data-toggle='tooltip' title='Actualizar Plan Maquina' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Plan Maquina' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
