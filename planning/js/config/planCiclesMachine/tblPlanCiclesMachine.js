$(document).ready(function () {
  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true); 
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true); 
  });

  // Mostrar Tabla planeacion maquinas
  tblPlanCiclesMachine = $('#tblPlanCiclesMachine').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/planCiclesMachine',
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    },
    columns: [
      {
        title: 'No.',
        data: null,
        className: 'uniqueClassName',
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: 'Referencia',
        data: 'reference',
        className: 'uniqueClassName',
      },
      {
        title: 'Producto',
        data: 'product',
        className: 'text-center',
      },
      {
        title: 'Máquina',
        data: 'machine',
        className: 'uniqueClassName',
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
        className: 'uniqueClassName',
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePCMachine" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
