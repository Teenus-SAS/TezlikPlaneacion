$(document).ready(function () {
  $(".cardAddNewProduct").hide();

  $("#btnAddNewProduct").click(async function (e) {
    e.preventDefault();

    $(
      ".cardImportProductsMaterials, .inputQuantityPCalc, .cardAddMaterials"
    ).hide(800);

    $(".cardAddNewProduct").toggle(800);
    $("#btnAddProduct").text("Asignar");
    $("#units2").empty();

    sessionStorage.removeItem("id_composite_product");

    $(".inputs").css("border-color", "");
    $("#formAddNewProduct").trigger("reset");
  });

  $(".compositeProduct").change(function (e) {
    e.preventDefault();

    let product_type = parseInt($(this).find("option:selected").attr("class"));
    $(`#idProductType option[value=${product_type}]`).prop("selected", true);

    let data = JSON.parse(sessionStorage.getItem("dataUnits"));
    let filterData = data.filter((item) => item.unit == "UNIDAD");

    let $select = $(`#unit2`);
    $select.empty().append(`<option disabled>Seleccionar</option>`);
    $select.append(
      `<option value ='${filterData[0].id_unit}' selected>UNIDAD</option>`
    );
  });

  // Tipo Producto
  $("#idProductType").change(async function (e) {
    e.preventDefault();

    $(".inputQuantityPCalc").hide();
    $("#units2").empty();
    $("#quantityCP, #quantityPCalc").val("");

    let type = this.value;
    let typeName = $("#idProductType option:selected").text().trim();

    let dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));

    let dataP = dataProducts.filter(
      (item) => item.id_product_type == type && item.composite == 1
    );
    await populateOptions("#refCompositeProduct", dataP, "reference");
    await populateOptions("#compositeProduct", dataP, "product");

    if (typeName != "CAJA") $(".inputQuantityPCalc").show();
  });

  if (flag_products_measure == "1") {
    $("#quantityCP").prop("readonly", true);

    $(document).on("change keyup", ".calcPWeight", async function () {
      const idPProduct = parseInt($("#selectNameProduct").val());
      const idCProduct = parseInt($("#refCompositeProduct").val());
      const type = parseInt($("#idProductType").val());
      const typeName = $("#idProductType option:selected").text().trim();

      const validate = idPProduct * idCProduct * type;

      if (isNaN(validate) || validate <= 0) {
        return false;
      }

      const quantity = parseFloat($("#quantityPCalc").val());

      isNaN(quantity) ? (quantity = 0) : quantity;

      $.ajax({
        type: "POST",
        url: "/api/calcQuantityFTCP",
        data: {
          idPProduct: idPProduct,
          idCProduct: idCProduct,
          typeName: typeName,
          quantityCalc: quantity,
        },
        success: function (resp) {
          $("#quantityCP").val(resp.weight);
        },
      });
    });
  }

  $("#btnAddProduct").click(function (e) {
    e.preventDefault();

    let idCompositeProduct =
      sessionStorage.getItem("id_composite_product") || null;

    const apiUrl = idCompositeProduct
      ? "/api/updateCompositeProduct"
      : "/api/addCompositeProduct";

    checkDataRequisition(apiUrl, idCompositeProduct);
  });

  /* Actualizar productos materials */

  $(document).on("click", ".updateComposite", function (e) {
    $(".cardImport").hide(800);
    $(".cardAddNewProduct").show(800);
    $(".inputs").css("border-color", "");
    $("#btnAddProduct").text("Actualizar");

    //Obtener data
    let row = $(this).closest("tr")[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem("id_composite_product", data.id_composite_product);
    $(`#refCompositeProduct option[value=${data.id_child_product}]`).prop(
      "selected",
      true
    );
    $(`#compositeProduct option[value=${data.id_child_product}]`).prop(
      "selected",
      true
    );
    $(`#idProductType option[value=${data.id_product_type}]`).prop(
      "selected",
      true
    );

    $("#quantityCP").val(data.quantity);

    data = JSON.parse(sessionStorage.getItem("dataUnits"));

    let filterData = data.filter((item) => item.unit == "UNIDAD");

    let $select = $(`#unit2`);
    $select.empty().append(`<option disabled>Seleccionar</option>`);
    $select.append(
      `<option value ='${filterData[0].id_unit}' selected>UNIDAD</option>`
    );

    // Animación de desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  function validateForm() {
    let emptyInputs = [];
    let selectNameProduct = parseInt($("#selectNameProduct").val());
    let quantityCP = parseFloat($("#quantityCP").val());

    // Verificar cada campo y agregar los vacíos a la lista
    if (!selectNameProduct) emptyInputs.push("#selectNameProduct");
    if (!quantityCP) emptyInputs.push("#quantityCP");

    // Marcar los campos vacíos con borde rojo
    emptyInputs.forEach(function (selector) {
      $(selector).css("border-color", "red");
    });

    // Mostrar mensaje de error si hay campos vacíos
    if (emptyInputs.length) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    return true;
  }

  /* Revision data Productos materiales */
  checkDataProducts = async (url, idCompositeProduct) => {
    if (!validateForm()) return false;

    const ref = parseInt($("#compositeProduct").val());
    const quan = parseFloat($("#quantityCP").val());
    const idProduct = parseInt($("#selectNameProduct").val());

    if (ref == idProduct) {
      $("#compositeProduct").css("border-color", "red");
      toastr.error("Seleccione un producto compuesto diferente");
      return false;
    }

    quant = 1 * quan;

    if (quan <= 0 || isNaN(quan)) {
      $("#quantityCP").css("border-color", "red");
      toastr.error("La cantidad debe ser mayor a cero (0)");
      return false;
    }

    const dataProduct = new FormData(formAddNewProduct);
    dataProduct.append("idProduct", idProduct);

    if (idCompositeProduct != "" || idCompositeProduct != null)
      dataProduct.append("idCompositeProduct", idCompositeProduct);

    const resp = await sendDataPOST(url, dataProduct);

    messageMaterial(resp);
  };
});
