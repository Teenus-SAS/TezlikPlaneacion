$(document).ready(function () {
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
});