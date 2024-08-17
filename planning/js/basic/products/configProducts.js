$(document).ready(function () {
  $.ajax({
    url: '/api/products',
    success: function (r) { 
      sessionStorage.setItem('dataProducts', JSON.stringify(r));

      let viewCreateProduct = document.getElementById('pQuantity');

      if (viewCreateProduct) {
        if (flag_products_measure == '1') {
          r = r.filter(item => item.id_product_inventory == 0);
        }
      } 

      setSelectsProducts(r);
    },
  });

  setSelectsProducts = (data) => {
    let $select = $(`#refProduct`);
    $select.empty();
    
    let ref = sortFunction(data, 'reference');

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

    let prod = sortFunction(data, 'product');

    $select1.append(
      `<option value='0' disabled selected>Seleccionar</option>`
    );
    $.each(prod, function (i, value) {
      $select1.append(
        `<option value = ${value.id_product}> ${value.product} </option>`
      );
    });
  };

  /* Seleccion producto */
  $('#refProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    let viewOrders = document.getElementById('inptQuantity');
    if (viewOrders) {
      let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

      let data = dataProducts.find(item => item.id_product == id);

      viewOrders.value = data.quantity;
    }
  });

  $('#selectNameProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    let viewOrders = document.getElementById('inptQuantity');
    if (viewOrders) {
      let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

      let data = dataProducts.find(item => item.id_product == id);

      viewOrders.value = data.quantity;
    }    
  });   
});
