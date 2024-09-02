$(document).ready(function () {
  let finalProduct;
  sessionStorage.removeItem("dataTable");

  // Ocultar card productos en proceso
  $(".cardAddProductInProccess").hide();

  // Mostrar lista de productos en proceso
  $.ajax({
    type: "GET",
    url: "/api/productsInProcess",
    success: function (data) {
      const $selectProduct = $(`#product`);

      // Vaciar el select y agregar la opción por defecto
      $selectProduct
        .empty()
        .append(`<option disabled selected>Seleccionar</option>`);

      // Usar map para optimizar el ciclo de iteración
      const options = response.map(
        (value) =>
          `<option value="${value.id_product}">${value.product}</option>`
      );

      // Insertar todas las opciones de una vez para mejorar el rendimiento
      $select.append(options.join(""));
    },
  });

  // Mostrar card productos en proceso
  $("#btnCreateProductInProcess").click(function (e) {
    e.preventDefault();

    $(
      ".cardImportProductsMaterials, .cardAddMaterials, .cardTableConfigMaterials"
    ).hide(800);
    $(".cardTableProductsInProcess").show(800);
    $(".cardAddProductInProccess").toggle(800);

    $("#comment").text("Asignación de productos en proceso");
    $("#btnAddProductInProccess").text("Asignar");

    sessionStorage.removeItem("id_product_category");

    $("#formAddProductInProccess").trigger("reset");
  });

  // Seleccionar producto final
  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    finalProduct = $("#selectNameProduct").val();
  });

  // Guardar Productos en proceso
  $("#btnAddProductInProccess").click(function (e) {
    e.preventDefault();

    let idProductCategory = sessionStorage.getItem("id_product_category");

    if (!idProductCategory) {
      idProduct = $("#product").val();
      finalProduct = $("#selectNameProduct").val();

      data = idProduct * finalProduct;

      if (!data) {
        toastr.error("Seleccione producto");
        return false;
      }

      productInProcess = $("#formAddProductInProccess").serialize();
      productInProcess = `${productInProcess}&finalProduct=${finalProduct}`;

      $.post(
        "/api/addProductInProcess",
        productInProcess,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else updateProductInProcess();
  });

  /* Actualizar producto en proceso 
  $(document).on('click', '.updateProduct', function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddProductInProccess').show(800);
    $('#btnAddProductInProccess').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblProductsInProcess.fnGetData(row);

    sessionStorage.setItem('id_product_category', data.id_product_category);

    $(`#product option[value=${data.id_product}]`).prop('selected', true);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateProductInProcess = () => {
    let data = $('#formAddProductInProccess').serialize();
    idProductCategory = sessionStorage.getItem('id_product_category');

    data = `${data}&idProductCategory=${idProductCategory}`;

    $.post(
      '/api/updateProductInProcess',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  }; */

  // Eliminar producto
  deleteProduct = () => {
    //Obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblProductsInProcess.fnGetData(row);

    idProductCategory = data.id_product_category;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este Producto? Esta acción no se puede reversar.",
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
            `/api/deleteProductInProcess/${idProductCategory}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  const message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardAddProductInProccess").hide(800);
      $("#formAddProductInProccess").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblProductsInProcess").DataTable().clear();
    $("#tblProductsInProcess").DataTable().ajax.reload();
  }
});
