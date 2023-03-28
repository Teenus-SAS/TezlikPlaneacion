$(document).ready(function () {
  tblClients = $('#tblClients').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/clients',
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
        title: 'Ean',
        data: 'ean',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ' '),
      },
      {
        title: 'Nit',
        data: 'nit',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, ' '),
      },
      {
        title: 'Cliente',
        data: 'client',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_client',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i class="bx bx-edit-alt updateClient" id="${data}" data-toggle='tooltip' title='Actualizar Cliente' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever deleteClient" id="${data}" data-toggle='tooltip' title='Eliminar Cliente' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
