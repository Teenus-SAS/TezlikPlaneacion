$(document).ready(function () {
  $.get("/api/planAreas", function (response) {
    const $selectArea = $("#idArea");

    // Vaciar el select y agregar la opción por defecto
    $selectArea
      .empty()
      .append("<option disabled selected>Seleccionar</option>");

    // Usar map para optimizar el ciclo de iteración
    const options = response.map(
      (value) => `<option value="${value.id_plan_area}">${value.area}</option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $selectArea.append(options.join(""));
  });
});
