$(document).ready(function () {
  data = {};

  $(document).on('change', '#order', function (e) {
    e.preventDefault();

       let num_order = $('#order :selected').text().trim();
        loadProducts(num_order);
  });

  /* Cargar Pedidos y Productos 
  loadProductsAndOrders = (id_machine) => {
    data['idMachine'] = id_machine;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select = $(`#selectNameProduct`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
          $(`#selectNameProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
          // Obtener referencia producto
          $(`#refProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
        });

        let $select1 = $(`#order`);
        $select1.empty();

        $select1.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select1.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
          $(`#order option[value=${value.id_order}]`).prop('selected', true);
        });
      },
    });
    delete data.idMachine;
  }; */

  /* Cargar Maquinas y Pedidos 
  loadMachinesAndOrders = (id_product) => {
    data['idProduct'] = id_product;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select3 = $(`#idMachine`);
        $select3.empty();

        $select3.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select3.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
          );
          $(`#idMachine option[value=${value.id_machine}]`).prop(
            'selected',
            true
          );
        });

        let $select4 = $(`#order`);
        $select4.empty();

        $select4.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select4.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
          $(`#order option[value=${value.id_order}]`).prop('selected', true);
        });
      },
    });
    delete data.idProduct;
  }; */

  /* Cargar Productos y Maquinas */
  loadProducts = async (num_order) => {
    let r = await searchData(`/api/programming/${num_order}`);
    
        let $select = $(`#selectNameProduct`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
          $(`#selectNameProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
          // Obtener referencia producto
          $(`#refProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
        });
      
  };
});
