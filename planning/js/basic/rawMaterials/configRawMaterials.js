$(document).ready(function () {
  /* Cargar data materia prima */
  loadDataMaterial = async () => {
    sessionStorage.removeItem('dataMaterials');
    let data = await searchData('/api/materials');

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem('dataMaterials', dataMaterials);

    let $select = $(`#material`);
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  };

  loadDataMaterial();
});
