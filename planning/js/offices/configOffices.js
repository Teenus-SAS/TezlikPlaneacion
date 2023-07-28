$(document).ready(function () {
  $.ajax({
    url: '/api/offices',
    success: function (r) {  
      let $select = $(`#order`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value ="${value.id_order}"> ${value.num_order} _ ${value.reference} </option>`
        );
      });
    },
  });
});
