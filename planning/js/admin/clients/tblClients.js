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
        title: 'NIT',
        data: 'nit',
        className: 'uniqueClassName',
      },
      {
        title: 'Cliente',
        data: 'client',
        className: 'uniqueClassName',
      },
      {
        title: 'Dirección',
        data: 'address',
        className: 'uniqueClassName',
      },
      {
        title: 'Telefono',
        data: 'phone',
        className: 'uniqueClassName',
      },
      {
        title: 'Ciudad',
        data: 'city',
        className: 'uniqueClassName',
      },
      {
        title: 'Img',
        data: 'img',
        className: 'uniqueClassName',
        render: (data, type, row) => {
          return data
            ? `<img src="${data}" alt="" style="width:50px;border-radius:100px">`
            : '';
        },
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
      }
    ],
  });
});
