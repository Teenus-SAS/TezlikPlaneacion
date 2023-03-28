$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  tblProducts = $('#tblProducts').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/planProducts',
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
        title: 'Molde',
        data: 'mold',
        className: 'uniqueClassName',
      },
      {
        title: 'Img',
        data: 'img',
        className: 'uniqueClassName',
        render: (data, type, row) => {
          data ? data : (data = '');
          ('use strict');
          return `<img src="${data}" alt="" style="width:80px;border-radius:100px">`;
        },
      },
      {
        title: 'Categoria',
        data: 'category',
        className: 'uniqueClassName',
        //render: function (data) {},
      },
      {
        title: 'Acciones',
        data: 'id_product',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
