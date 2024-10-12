$(document).ready(function () {
  tblRisks = $('#tblRisks').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/risks`,
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
        title: 'Riesgo',
        data: 'risk_level',
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
        data: 'id_risk',
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRisk" data-toggle='tooltip' title='Actualizar Riesgo' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
