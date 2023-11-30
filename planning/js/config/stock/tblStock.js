$(document).ready(function () {
  /* Cargue tabla de stock */

  tblStock = $('#tblStock').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/stock',
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
        title: 'Material',
        data: 'material',
        className: 'uniqueClassName',
      },
      {
        title: 'Plazo Max',
        data: 'max_term',
        className: 'uniqueClassName',
      },
      {
        title: 'Plazo Habitual',
        data: 'usual_term',
        className: 'uniqueClassName',
      }, 
      {
        title: 'Acciones',
        data: 'id_stock',
        className: 'uniqueClassName',
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateStock" data-toggle='tooltip' title='Actualizar Stock' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
