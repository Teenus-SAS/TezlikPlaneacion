$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  tblUsers = $('#tblUsers').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/usersCompany',
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
        title: 'Nombres',
        data: 'firstname',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Apellidos',
        data: 'lastname',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Email',
        data: 'email',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Posicion',
        data: 'position',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Empresa',
        data: 'company',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Acciones',
        data: 'id_user',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
