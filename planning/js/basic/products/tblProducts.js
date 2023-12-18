$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  tblProducts = $('#tblProducts').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/products',
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    },
    columns: [
      {
        title: 'No.',
        data: null,
        className: 'uniqueClassName  dt-head-center',
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: 'Referencia',
        data: 'reference',
        className: 'uniqueClassName dt-head-center',
      },
      {
        title: 'Producto',
        data: 'product',
        className: 'uniqueClassName dt-head-center',
      }, 
      {
        title: 'Existencia',
        data: 'quantity',
        className: 'uniqueClassName dt-head-center',
        render: $.fn.dataTable.render.number('.', ',', 0),
      }, 
      {
        title: 'Medida',
        data: 'abbreviation',
        className: 'uniqueClassName dt-head-center', 
      },
      /* {
        title: 'Reservado',
        data: 'reserved',
        className: 'uniqueClassName', 
      },  */
      {
        title: 'Stock Min',
        data: 'minimum_stock',
        className: 'uniqueClassName dt-head-center', 
        render: $.fn.dataTable.render.number('.', ',', 0),
      }, 
      {
          title: "Dias Inv",
          data: "days",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number('.', ',', 0),
        },
      
      {
        title: 'Img',
        data: 'img',
        className: 'uniqueClassName dt-head-center',
        render: (data, type, row) => {
          data ? img = `<img src="${data}" alt="" style="width:80px;border-radius:100px">` : (img = ''); 
          return img;
        },
      }, 
      {
        title: 'Acciones',
        data: 'id_product',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
