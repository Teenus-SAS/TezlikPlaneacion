$(document).ready(function () {
  /* Cargar bitacora */

  tblBinnacle = $('#tblBinnacle').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/binnacle`,
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
        title: 'Usuario',
        data: 'user',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Fecha Creación',
        data: 'date_binnacle',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Actividad Realizada',
        data: 'activity_performed',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Información Actual',
        data: 'actual_information',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Información Anterior',
        data: 'previous_information',
        className: 'uniqueClassName dt-head-center',
      },
    ],
  });
});
