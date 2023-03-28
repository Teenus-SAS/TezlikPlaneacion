$(document).ready(function () {
    /* Cargue tabla PUC */
  
    tblPUC = $('#tblPUC').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/findPUC`,
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
          title: 'Número de Cuenta',
          data: 'number_count',
        },
        {
          title: 'Cuenta',
          data: 'count',
        },
        {
          title: 'Acciones',
          data: 'id_puc',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePuc" data-toggle='tooltip' title='Actualizar Cuenta' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  });
  
