$(document).ready(function () {
  $.ajax({
    url: "/api/materialsType",
    success: function (data) {
      const $selectMaterialType = $(`#materialType`);

      // Vaciar el select y agregar la opción por defecto
      $selectMaterialType
        .empty()
        .append(`<option disabled selected>Seleccionar</option>`);

      // Usar map para optimizar el ciclo de iteración
      const options = data.map(
        (value) =>
          `<option value="${value.id_material_type}">${value.material_type}</option>`
      );

      // Insertar todas las opciones de una vez para mejorar el rendimiento
      $selectMaterialType.append(options.join(""));
    },
  });
});
