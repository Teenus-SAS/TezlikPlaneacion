$(document).ready(function () {
  $.ajax({
    url: '/api/products',
    success: function (r) {
      let $select = $(`.refProduct`);
      $select.empty();

      let ref = r.sort(sortReference);

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(ref, function (i, value) {
        $select.append(
          `<option value =${value.id_product}> ${value.reference} </option>`
        );
      });

      let $select1 = $(`.selectNameProduct`);
      $select1.empty();

      let prod = r.sort(sortNameProduct);

      $select1.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(prod, function (i, value) {
        $select1.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });

      let $select2 = $(`#compositeProduct`);
      $select2.empty();

      let compositeProduct = prod.filter(item => item.composite == 1);

      $select2.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(compositeProduct, function (i, value) {
        $select2.append(
          `<option value ="${value.id_product}"> ${value.product} </option>`
        );
      });
    },
  });
});
