$(document).ready(function () {
  $.ajax({
    url: '/api/clients',
    success: function (r) {
      let $select = $(`#client`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_client}> ${value.client} </option>`
        );
      });
    },
  });
});
