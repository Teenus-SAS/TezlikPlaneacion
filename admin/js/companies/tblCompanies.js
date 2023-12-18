$(document).ready(function () {
  $('.statusCompanies').on('click', function () {
    if ($(this).is(':checked')) loadtableCompanies(1);
    else loadtableCompanies(0);
  });

  const loadtableCompanies = (stat) => {
    tblCompanies = $('#tblCompanies').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/companies/${stat}`,
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
          title: 'Empresa',
          data: 'company',
        },
        {
          title: 'Departamento',
          data: 'state',
        },
        {
          title: 'Ciudad',
          data: 'city',
        },
        {
          title: 'País',
          data: 'country',
        },
        {
          title: 'Dirección',
          data: 'address',
        },
        {
          title: 'Teléfono',
          data: 'telephone',
        },
        {
          title: 'NIT',
          data: 'nit',
        },
        {
          title: 'Logo',
          data: 'logo',
          render: function (data) {
            return `<img src="${data}" width="100px">`;
          },
        },
        {
          title: 'Fecha de creación',
          data: 'created_at',
        },
        {
          title: 'Acciones',
          data: 'id_company',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateCompany" data-toggle='tooltip' title='Actualizar Empresa' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  };

  loadtableCompanies(1);
});
