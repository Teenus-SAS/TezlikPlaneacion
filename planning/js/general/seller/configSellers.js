$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/sellers',
    success: function (r) {
      let $select = $(`#seller`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_seller}> ${value.firstname} ${value.lastname} </option>`
        );
      });
    },
  });
});