$(document).ready(function () {
  sessionStorage.removeItem('dataMaterials');
  $('.cardRawMaterials').hide();

  $('#btnNewMaterial').click(function (e) {
    e.preventDefault();
    $('.cardRawMaterials').toggle(800);
  });

  $.ajax({
    url: '/api/materials',
    success: function (r) {
      dataMaterials = JSON.stringify(r);
      sessionStorage.setItem('dataMaterials', dataMaterials);

      let $select = $(`#material`);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_material}> ${value.material} </option>`
        );
      });
    },
  });
});
