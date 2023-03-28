$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/categories',
    success: function (r) {
      let $select = $(`#category`);
      $select.empty();

      $select.append(`<option disabled selected>Categorias</option>`);
      $.each(r, function (i, value) {
        // if (value.type_category.includes('Inventario'))
        $select.append(
          `<option value="${value.id_category}-${value.category}"> ${value.category} </option>`
        );
      });
      $select.append(`<option value=Todos>Todos</option>`);

      let $select1 = $(`#productsCategories`);
      $select1.empty();

      $select1.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        if (value.type_category == 'Producto')
          $select1.append(
            `<option value=${value.id_category}> ${value.category} </option>`
          );
      });
    },
  });
});
