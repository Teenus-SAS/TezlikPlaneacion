$(document).ready(function () {
  /* Cargar unidades */

  tblUnits = $('#tblUnits').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/units`,
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        title: 'Magnitud',
        data: 'magnitude',
        visible: false,
      },
      {
        title: 'Unidad',
        data: 'unit',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Abreviación',
        data: 'abbreviation',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Acciones',
        data: 'id_unit',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUnit" data-toggle='tooltip' title='Actualizar Unidad' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Unidad' style="font-size: 30px; color:red" onclick="deleteFunction()"></i></a>         
                `;
        },
      },
    ],
    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="4" style="font-weight: bold;"> ${row.magnitude} </th>`;
      },
      startRender: function (rows, group) {
        return $('<tr/>').append(group);
      },
      className: 'odd',
    },
  });
});
