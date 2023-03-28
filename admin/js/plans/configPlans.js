$(document).ready(function () {
  $.ajax({
    url: '/api/plans',
    success: function (r) {
      let $select = $(`#plan`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_plan}> ${value.plan} </option>`
        );
      });
    },
  });
});
