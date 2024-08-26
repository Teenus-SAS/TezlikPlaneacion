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
  $('#btnNewProduct').click(async function (e) {
    e.preventDefault();

    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

    await setSelectsProducts(dataProducts);
    $(".cardCreateProduct").toggle(800);
    $(".cardImportProducts").hide(800);
    $("#btnCreateProduct").html("Crear Producto");

    sessionStorage.removeItem("id_product");

    $("#formCreateProduct").trigger("reset");
    $("#img").remove();
  });

  /* Crear producto */
  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();
    let idProduct = sessionStorage.getItem("id_product");

    if (idProduct == "" || idProduct == null) {
      checkDataProducts("/api/addProduct", idProduct);
    } else {
      checkDataProducts("/api/updatePlanProduct", idProduct);
    }
  });

  /* Actualizar productos */

  $(document).on("click", ".updateProducts", function (e) {
    $(".cardImportProducts").hide(800);
    $(".cardCreateProduct").show(800);
    $("#btnCreateProduct").html("Actualizar Producto");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idProduct = id.split('-')[1];

    sessionStorage.setItem("id_product", idProduct);

    let row = $(this).parent().parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    // if (flag_products_measure == '1') { 
    let $select = $(`#refProduct`);
    $select.empty();
    $select.append(`<option value='0' disabled>Seleccionar</option>`);
    $select.append(`<option value='${data.id_product}' selected>${data.reference}</option>`);

    let $select1 = $(`#selectNameProduct`);
    $select1.empty();
    $select1.append(`<option value='0' disabled>Seleccionar</option>`);
    $select1.append(`<option value='${data.id_product}' selected>${data.product}</option>`);
    // } else {
    //   $("#referenceProduct").val(data.reference);
    //   $("#product").val(data.product);
    // }

    $("#pQuantity").val(data.quantity);

    if (data.img)
      $("#preview").html(
        `<img id="img" src="${data.img}" style="width:50%;padding-bottom:15px"/>`
      );

    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  const checkDataProducts = async (url, idProduct) => {
    // if (flag_products_measure == '1') {
      idProduct = parseFloat($("#refProduct").val());

      if (isNaN(idProduct) || idProduct <= 0) {
        toastr.error("Ingrese todos los campos");
        return false;
      }
    // } else {
    //   let ref = $("#referenceProduct").val();
    //   let prod = $("#product").val();
      
    //   if (ref.trim() == "" || !ref.trim() || prod.trim() == "" || !prod.trim()) {
    //     toastr.error("Ingrese todos los campos");
    //     return false;
    //   }
    // }

    let imageProd = $("#formFile")[0].files[0];

    let dataProduct = new FormData(formCreateProduct);
    dataProduct.append("img", imageProd);

    if (idProduct != "" || idProduct != null) {
      dataProduct.append("idProduct", idProduct);
    }

    // if (flag_products_measure == '1') { 
    dataProduct.append("idProduct", idProduct);
    // } else {
    //   dataProduct.append("idProductType", 0);      
    // }

    let resp = await sendDataPOST(url, dataProduct);

    messageProducts(resp);
  };

  /* Eliminar productos */

  deleteProductsFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    let idProduct = data.id_product;

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
            `/api/deletePlanProduct/${idProduct}`,
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
    if (data.success == true) {
      $("#formImportProduct").trigger("reset");
      $(".cardCreateProduct").hide(800);
      $(".cardImportProducts").hide(800);
      $("#formCreateProduct").trigger("reset");
      loadAllData();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
