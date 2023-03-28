$(document).ready(function () {
  $.ajax({
    url: '/api/orderTypes',
    success: function (r) {
      let $select = $(`#orderType`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_order_type}> ${value.order_type} </option>`
        );
      });
    },
  });
});
