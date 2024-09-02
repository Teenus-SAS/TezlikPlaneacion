$(document).ready(function () {
  $.ajax({
    type: "GET",
    url: "/api/invMolds",
    success: function (data) {
      const $selectMold = $(`#idMold`);

      // Vaciar el select y agregar la opción por defecto
      $selectMold
        .empty()
        .append(`<option disabled selected>Seleccionar</option>`);

      // Map para optimizar el ciclo de iteración
      const options = data.map(
        (value) => `<option value = ${value.id_mold}> ${value.mold} </option>`
      );

      // Insertar todas las opciones de una vez para mejorar el rendimiento
      $selectMold.append(options.join(""));
    },
  });
});
