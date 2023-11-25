$(document).ready(function () {
  /* Cargar pedidos */
  tblOrder = $('#tblOrder').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/orders',
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
        title: 'Fecha Pedido',
        data: 'date_order',
        className: 'uniqueClassName', 
      },
      {
        title: 'No Pedido',
        data: 'num_order',
        className: 'uniqueClassName',
      },
      {
        title: 'Cliente',
        data: 'client',
        className: 'uniqueClassName',
      },
      {
        title: 'Producto',
        data: 'product',
        className: 'uniqueClassName',
      },
      {
        title: 'Cantidad',
        data: 'original_quantity',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, ''),
      },
      {
        title: 'Fecha Maxima',
        data: 'max_date',
        className: 'classCenter',
      }, 
      {
        title: 'Estado',
        data: 'status',
        className: 'classCenter',
      },
      {
        title: 'Acciones',
        data: null,
        className: 'classCenter',
        render: function (data) {
          !data.delivery_date && data.status == 'Programar' ? action = `<a href="javascript:;" <i class="bx bx-edit-alt updateOrder" id="${data.id_order}" data-toggle='tooltip' title='Actualizar Pedido' style="font-size: 30px;"></i></a><a href="javascript:;" <i class="mdi mdi-delete-forever" id="${data.id_order}" data-toggle='tooltip' title='Eliminar Pedido' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`
            : action = '';

          return action;
        },
      },
    ],
    rowCallback: function (row, data, index) { 
      if (data.status == 'Sin Ficha Tecnica') $(row).css('color', 'red'); 
    },
  });
});
