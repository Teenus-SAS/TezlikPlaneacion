$(document).ready(function () {
  /* Ocultar panel crear producto */
  $(".cardCreateProduct").hide();

  /* Cargar imagen de producto */
  $("#formFile").change(function (e) {
    // e.preventDefault();

    $("#preview").html(
      `<img id="img" src="${URL.createObjectURL(
        event.target.files[0]
      )}" style="width:50%;padding-bottom:15px"/>`
    );
  });

  /* Abrir panel crear producto */
  // $("#btnNewProduct").click(async function (e) {
  //   e.preventDefault();

  //   let dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));

  //await setSelectsProducts(dataProducts);
  //$(".cardCreateProduct").toggle(800);
  //$(".cardImportProducts").hide(800);
  //$("#btnCreateProduct").html("Crear Producto");

  //   sessionStorage.removeItem("id_product");

  //   $("#formCreateProduct").trigger("reset");
  //   $("#img").remove();
  // });

  /* Crear producto */
  $("#btnCreateProduct").click(function (e) {
    e.preventDefault();

    const idProductInventory =
      sessionStorage.getItem("id_product_inventory") || null;
    const apiUrl =
      idProductInventory && idProductInventory != 0
        ? "/api/updatePlanProduct"
        : "/api/addProduct";
    checkDataProducts(apiUrl, idProductInventory);
  });

  /* Actualizar productos */

  $(document).on("click", ".updateProducts", function (e) {
    $(".cardImportProducts").hide(800);
    $(".cardCreateProduct").show(800);
    $("#btnCreateProduct").html("Actualizar Producto");

    // Obtener el ID del elemento
    let idProductInventory = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_product_inventory", idProductInventory);

    // Obtener data
    let row = $(this).parent().parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    let $select = $(`#refProduct`);
    $select.empty();
    $select.append(`<option value='0' disabled>Seleccionar</option>`);
    $select.append(
      `<option value='${data.id_product}' selected>${data.reference}</option>`
    );

    let $select1 = $(`#selectNameProduct`);
    $select1.empty();
    $select1.append(`<option value='0' disabled>Seleccionar</option>`);
    $select1.append(
      `<option value='${data.id_product}' selected>${data.product}</option>`
    );

    // Asignar valores a los campos del formulario y animar
    $("#pQuantity").val(data.quantity);

    if (data.img)
      $("#preview").html(
        `<img id="img" src="${data.img}" style="width:50%;padding-bottom:15px"/>`
      );

    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  const checkDataProducts = async (url, idProductInventory) => {
    const idProduct = parseFloat($("#refProduct").val());
    const quantity = parseFloat($("#pQuantity").val());

    if (quantity < 0 || !idProduct) {
      toastr.error("Ingrese una cantidad válida y seleccionar un producto.");
      return false;
  }

    /* let data = idProduct * quantity;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    } */

    let imageProd = $("#formFile")[0].files[0];

    let dataProduct = new FormData(formCreateProduct);
    dataProduct.append("img", imageProd);

    if (idProductInventory != "" || idProductInventory != null) {
      let dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));

      let arr = dataProducts.find((item) => item.id_product == idProduct);

      if (quantity < parseFloat(arr.reserved)) {
        toastr.error("Existencias con menor cantidad de las reservadas");
        return false;
      }

      dataProduct.append("idProductInventory", idProductInventory);
    }
    dataProduct.append("idProduct", idProduct);

    let resp = await sendDataPOST(url, dataProduct);

    messageProducts(resp);
  };

  /* Eliminar productos */

  deleteProductsFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblProducts.fnGetData(row);

    let idProductInventory = data.id_product_inventory;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este producto? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.get(
            `/api/deletePlanProduct/${idProductInventory}`,
            function (data, textStatus, jqXHR) {
              messageProducts(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageProducts = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreateProduct, .cardImportProducts").hide(800);
      $("#formImportProduct, #formCreateProduct").trigger("reset");
      loadAllData();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
