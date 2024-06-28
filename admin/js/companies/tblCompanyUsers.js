$(document).ready(function () {
  /* Cargue tabla Usuarios Empresa */

  let idCompany = sessionStorage.getItem('id_company');

  tblCompanyUsers = $('#tblCompanyUsers').dataTable({
    pageLength: 50,
    ajax: {
      url: `/api/companyUsers/${idCompany}`,
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
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Nombres',
        data: 'firstname',
      },
      {
        title: 'Apellidos',
        data: 'lastname',
      },
      {
        title: 'E-mail',
        data: 'email',
      },
      {
        title: 'Estado',
        data: 'active',
        render: function (data) {
          if (data === 1) {
            return 'Activo';
          } else {
            return 'Inactivo';
          }
        },
      },
      {
        title: 'Acciones',
        data: 'id_user',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="us-${data}" class="bx bx-x-circle userStatus" data-toggle='tooltip' title='Estado Usuario' style="font-size: 30px;"></i></a>
                    `;
        },
      },
    ],
  });
});
