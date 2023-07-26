$(document).ready(function () {
  /* Cargar unidades por magnitud */
  loadUnitsByMagnitude = async (data, op) => {
    Object.prototype.toString.call(data) === '[object Object]'
      ? (id_magnitude = data.id_magnitude)
      : (id_magnitude = data);

    let dataPMaterials = await searchData(`/api/units/${id_magnitude}`);

    let $select = $(`#units`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(dataPMaterials, function (i, value) {
      if (id_magnitude == '5' && op == 2) {
        if (value.id_unit == data.id_unit) {
          $select.empty();
          $select.append(
            `<option value ='${value.id_unit}' selected> ${value.unit} </option>`
          );
          return false;
        }
      } else $select.append(`<option value = ${value.id_unit}> ${value.unit} </option>`);
    });
  };
});
