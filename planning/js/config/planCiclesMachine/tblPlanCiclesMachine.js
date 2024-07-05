$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  loadTblPlanCiclesMachine = (idProduct) => {
    tblPlanCiclesMachine = $('#tblPlanCiclesMachine').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/planCiclesMachine/${idProduct}`,
        dataSrc: '',
      },
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Proceso',
          data: 'process',
          className: 'text-center',
        },
        {
          title: 'Máquina',
          data: 'machine',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Und/Hora',
          data: 'cicles_hour',
          className: 'text-center',
          render: $.fn.dataTable.render.number('.', ',', 0),
        },
        {
          title: 'Und/Turno',
          data: 'units_turn',
          className: 'text-center',
          render: $.fn.dataTable.render.number('.', ',', 0),
        },
        {
          title: 'Und/Mes',
          data: 'units_month',
          className: 'text-center',
          render: $.fn.dataTable.render.number('.', ',', 0),
        },
        {
          title: 'Acciones',
          data: 'id_cicles_machine',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `
                    <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updatePCMachine" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red" onclick="deleteMachine()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let cicles_hour = 0; 
        let units_turn = 0; 
        let units_month = 0; 

        for (i = 0; i < display.length; i++) {
          cicles_hour += parseFloat(data[display[i]].cicles_hour); 
          units_turn += parseFloat(data[display[i]].units_turn); 
          units_month += parseFloat(data[display[i]].units_month); 
        }

        $(this.api().column(3).footer()).html(
          cicles_hour.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 
        
        $(this.api().column(4).footer()).html(
          units_turn.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 

        $(this.api().column(5).footer()).html(
          units_month.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 
      },
    });
  }
});
