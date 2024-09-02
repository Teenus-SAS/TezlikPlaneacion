$(document).ready(function () {
  $.ajax({
    url: "/api/products",
    success: function (data) {
      sessionStorage.setItem("dataProducts", JSON.stringify(data));

      let viewCreateProduct = document.getElementById("pQuantity");

      if (viewCreateProduct && flag_products_measure === "1")
        data = data.filter((item) => item.id_product_inventory == 0);

      const compositeProduct = data.filter((item) => item.composite === 1);
      populateOptions("#refCompositeProduct", compositeProduct, "reference");
      populateOptions("#compositeProduct", compositeProduct, "product");
      setSelectsProducts(data);
    },
  });

  setSelectsProducts = (data) => {
    let $selectRef = $(`#refProduct`);
    let $selectProd = $(`#selectNameProduct`);

    // Vaciar el select y agregar la opción por defecto
    $selectRef.empty().append("<option disabled selected>Seleccionar</option>");
    $selectProd
      .empty()
      .append("<option disabled selected>Seleccionar</option>");

    let sortRef = sortFunction(data, "reference");
    let sortProd = sortFunction(data, "product");

    // Usar map para optimizar el ciclo de iteración
    const optionsRef = sortRef.map(
      (value) =>
        `<option value ='${value.id_product}' class='${value.composite}'> ${value.reference} </option>`
    );
    const optionsProd = sortProd.map(
      (value) =>
        `<option value ='${value.id_product}' class='${value.composite}'> ${value.product} </option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $select.append(optionsRef.join(""));
    $select.append(optionsProd.join(""));
  };

  populateOptions = (selector, data, property) => {
    let $select = $(selector);

    // Vaciar el select y agregar la opción por defecto
    $select.empty().append(`<option disabled selected>Seleccionar</option>`);

    // Usar map para optimizar el ciclo de iteración
    const options = data.map(
      (value) =>
        `<option value ="${value.id_product}" class="${value.id_product_type}"> ${value[property]} </option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $select.append(options.join(""));
  };

  /* Seleccion producto */
  $("#refProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#selectNameProduct option").prop("selected", function () {
      return $(this).val() == id;
    });

    let viewOrders = document.getElementById("inptQuantity");

    if (viewOrders) {
      const dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
      const data = dataProducts.find((item) => item.id_product == id);
      viewOrders.value = data.quantity;
    }
  });

  $("#selectNameProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#refProduct option").prop("selected", function () {
      return $(this).val() == id;
    });

    let viewOrders = document.getElementById("inptQuantity");
    if (viewOrders) {
      const dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
      const data = dataProducts.find((item) => item.id_product == id);
      viewOrders.value = data.quantity;
    }
  });

  $("#refCompositeProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#compositeProduct option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $("#compositeProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#refCompositeProduct option").prop("selected", function () {
      return $(this).val() == id;
    });
  });
});
