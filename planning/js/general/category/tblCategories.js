$(document).ready(function () {
  /* Cargue tabla de Categorias */

  tblCategories = $('#tblCategories').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/categories',
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
        title: 'Categoría',
        data: 'category',
        className: 'uniqueClassName',
      },
      {
        title: 'Tipo Categoría',
        data: 'type_category',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_category',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                  <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateCategory" data-toggle='tooltip' title='Actualizar Categoría' style="font-size: 30px;"></i></a>
                  <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Categoría' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
