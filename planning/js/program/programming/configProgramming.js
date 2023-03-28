$(document).ready(function () {
  data = {};

  $(document).on('change', '.programmingSelect', function (e) {
    e.preventDefault();
    if (!data.idMachine && !data.idOrder && !data.idProduct) {
      id = this.id;

      if (id.includes('machines')) {
        id_machine = $('#idMachine').val();
        loadProductsAndOrders(id_machine);
      }
      if (id.includes('orders')) {
        id_order = $('#order').val();
        loadProducts(id_order);
      }
      if (id.includes('products')) {
        id_product = $('#selectNameProduct').val();
        loadMachinesAndOrders(id_product);
      }
    }
  });

  /* Cargar Pedidos y Productos */
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
  };

  /* Cargar Maquinas y Pedidos */
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
  };

  /* Cargar Productos y Maquinas */
  loadProducts = (id_order) => {
    data['idOrder'] = id_order;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select5 = $(`#idMachine`);
        $select5.empty();

        $select5.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select5.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
          );
          $(`#idMachine option[value=${value.id_machine}]`).prop(
            'selected',
            true
          );
        });

        let $select6 = $(`#selectNameProduct`);
        $select6.empty();

        $select6.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select6.append(
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
      },
    });
    delete data.idOrder;
  };
});
