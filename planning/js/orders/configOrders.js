$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/orders',
    success: function (r) {
      let $select = $(`#order`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_order}> ${value.num_order} </option>`
        );
      });
    },
  });
});
