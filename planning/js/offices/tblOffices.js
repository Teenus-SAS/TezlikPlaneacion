$(document).ready(function () {
  /* Cargar pedidos */
  loadTblOffices = (min_date, max_date) =>
  {
    if (min_date == null && max_date == null)
      url = '/api/actualOffices';
    else
      url = `/api/offices/${min_date}/${max_date}`;

    tblOffices = $('#tblOffices').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: url,
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
          title: 'No Pedido',
          data: 'num_order',
          className: 'uniqueClassName',
        },
        {
          title: 'Fecha Pedido',
          data: 'date_order',
          className: 'uniqueClassName',
        },
        {
          title: 'Cliente',
          data: 'client',
          className: 'uniqueClassName',
        },
        {
          title: 'Fecha Minima',
          data: 'min_date',
          className: 'classCenter',
        },
        {
          title: 'Fecha Maxima',
          data: 'max_date',
          className: 'classCenter',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'Cantidad Solicitada',
          data: 'original_quantity',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Existencias',
          data: 'quantity',
          className: 'classCenter',
        },
        // {
        //   title: 'Fecha de Entrega',
        //   data: 'id_order',
        //   className: 'classCenter',
        //   render: function (data) { 
        //     return `<a href="javascript:;" <i class="bi bi-calendar-plus-fill changeDate" id="${data}" data-toggle='tooltip' title='Actualizar Fecha' style="font-size: 30px;"></i></a>`;
        //   }
        // },
      ],
    });
  };

  loadTblOffices(null, null);
});
