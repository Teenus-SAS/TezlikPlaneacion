$(document).ready(function () {
  /* Cargue tabla de procesos */

  tblProcess = $('#tblProcess').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/process',
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
        title: 'Proceso',
        data: 'process',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Acciones',
        data: 'id_process',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteProcessFunction()"></i></a>`;
        },
      },
    ],
  });
});
