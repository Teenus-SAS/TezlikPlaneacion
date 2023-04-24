$(document).ready(function () {
  /* Cargar unidades por magnitud */
  loadUnitsByMagnitude = async (id_magnitude) => {
    let data = await searchData(`/api/units/${id_magnitude}`);

    let $select = $(`#units`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_unit}> ${value.unit} </option>`
      );
    });
  };
});
