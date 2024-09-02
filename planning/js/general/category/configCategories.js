$(document).ready(function () {
  $.ajax({
    type: "GET",
    url: "/api/categories",
    success: function (data) {
      const $selectCategory = $(`#category`);
      const $selectProdCategories = $(`#productsCategories`);

      // Vaciar el select y agregar la opción por defecto
      $selectCategory
        .empty()
        .append(`<option disabled selected>Categorias</option>`);

      $selectCategory.append(`<option value=Todos>Todos</option>`);
      $selectProdCategories
        .empty()
        .append(`<option disabled selected>Seleccionar</option>`);

      // Optimizacion ciclo de iteración
      const optionsCategory = data.map(
        (value) =>
          `<option value="${value.id_category}-${value.category}"> ${value.category} </option>`
      );

      const optionsProdCategories = data.map(
        (value) =>
          `<option value=${value.id_category}> ${value.category} </option>`
      );

      // Insertar todas las opciones de una vez para mejorar el rendimiento
      $selectCategory.append(optionsCategory.join(""));
      $selectProdCategories.append(optionsProdCategories.join(""));
    },
  });
});
