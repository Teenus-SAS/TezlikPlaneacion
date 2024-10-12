$(document).ready(function () {
  tblBenefits = $('#tblBenefits').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/benefits`,
      dataSrc: '',
    },
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
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
        title: 'Prestacion',
        data: 'benefit',
        className: 'uniqueClassName',
      },
      {
        title: 'Porcentaje',
        data: 'percentage',
        className: 'uniqueClassName',
        render: function (data) {
          return `${data.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %`;
        },
      },
      {
        title: 'Acciones',
        data: 'id_benefit',
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateBenefit" data-toggle='tooltip' title='Actualizar Prestacion' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
