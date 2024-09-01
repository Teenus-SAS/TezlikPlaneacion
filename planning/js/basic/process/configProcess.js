$(document).ready(function () {
  $.get("/api/process", function (response) {
    const $select = $("#idProcess");

    // Vaciar el select y agregar la opción por defecto
    $select.empty().append("<option disabled selected>Seleccionar</option>");

    // Usar map para optimizar el ciclo de iteración
    const options = response.map(
      (value) => `<option value="${value.id_process}">${value.process}</option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $select.append(options.join(""));
  });
});
