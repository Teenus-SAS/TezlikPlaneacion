$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/invMolds',
    success: function (r) {
      let $select = $(`#idMold`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_mold}> ${value.mold} </option>`
        );
      });
    },
  });
});
