$(document).ready(function () {
  /* Cargue tabla de Categorias */

  tblInventoryABC = $('#tblInventoryABC').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/inventoryABC',
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
        title: 'A',
        data: 'a',
        className: 'uniqueClassName dt-head-center',
          render: function (data) { 
              return `${data} %`;
         }
      },
      {
        title: 'B',
        data: 'b',
        className: 'uniqueClassName dt-head-center',
        render: function (data) { 
              return `${data} %`;
         }
      },
      {
        title: 'C',
        data: 'c',
        className: 'uniqueClassName dt-head-center',
        render: function (data) { 
              return `${data} %`;
         }
      },
      {
        title: 'Acciones',
        data: 'id_inventory',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateInventory" data-toggle='tooltip' title='Actualizar Inventario' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
