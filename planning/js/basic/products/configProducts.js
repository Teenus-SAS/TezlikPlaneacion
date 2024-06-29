$(document).ready(function () {
  $.ajax({
    url: '/api/products',
    success: function (r) {
      let $select = $(`#refProduct`);
      $select.empty();

      // let ref = r.sort(sortReference);
      let ref = sortFunction(r, 'reference');

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(ref, function (i, value) {
        $select.append(
          `<option value =${value.id_product}> ${value.reference} </option>`
        );
      });

      let $select1 = $(`#selectNameProduct`);
      $select1.empty();

      let prod = sortFunction(r, 'product');

      $select1.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(prod, function (i, value) {
        $select1.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });
    },
  });

  /* Seleccion producto */
  $('#refProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  $('#selectNameProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });  
});
