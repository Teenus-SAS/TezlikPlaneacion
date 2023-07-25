$(document).ready(function () {
  /* Cargar pedidos */
  tblOffices = $('#tblOffices').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/offices',
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
        data: 'num_Offices',
        className: 'uniqueClassName',
      },
      {
        title: 'Fecha Pedido',
        data: 'date_Offices',
        className: 'uniqueClassName',
        // render: $.fn.dataTable.render.moment('YYYY/MM/DD'),
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
      {
        title: 'Acciones',
        data: 'id_Offices',
        className: 'classCenter',
        render: function (data) {
          return `
                <a href="javascript:;" <i class="bx bx-edit-alt updateOffices" id="${data}" data-toggle='tooltip' title='Actualizar Pedido' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever" id="${data}" data-toggle='tooltip' title='Eliminar Pedido' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
