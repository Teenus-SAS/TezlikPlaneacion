$(document).ready(function () {
  $('.cardCreateProduct').hide();

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();
    $('.cardCreateProduct').toggle(800);
  });

  $.ajax({
    url: '/api/planProducts',
    success: function (r) {
      let $select = $(`#refProduct`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_product}> ${value.reference} </option>`
        );
      });

      let $select1 = $(`#selectNameProduct`);
      $select1.empty();

      $select1.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select1.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });
    },
  });
});
