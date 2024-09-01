/* $(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/planAreas',
    success: function (r) {
      let $select = $(`#idArea`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_plan_area}> ${value.area} </option>`
        );
      });
    },
  });
}); */

$(document).ready(function () {
  $.get("/api/planAreas", function (response) {
    const $select = $("#idArea");

    // Vaciar el select y agregar la opción por defecto
    $select.empty().append("<option disabled selected>Seleccionar</option>");

    // Usar map para optimizar el ciclo de iteración
    const options = response.map(
      (value) => `<option value="${value.id_plan_area}">${value.area}</option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $select.append(options.join(""));
  });
});
