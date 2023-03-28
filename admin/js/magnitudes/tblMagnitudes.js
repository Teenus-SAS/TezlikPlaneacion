$(document).ready(function () {
  /* Cargar unidades */

  tblMagnitudes = $('#tblMagnitudes').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/magnitudes`,
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
        title: 'Magnitud',
        data: 'magnitude',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_magnitude',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMagnitude" data-toggle='tooltip' title='Actualizar Magnitud' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Magnitud' style="font-size: 30px; color:red" onclick="deleteFunction()"></i></a>         
                `;
        },
      },
    ],
  });
});
