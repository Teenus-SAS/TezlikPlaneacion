$(document).ready(function () {
  /* Ocultar panel para crear Areas */
  $(".cardCreatePType").hide();

  /* Abrir panel para crear Areas */

  $("#btnNewPType").click(function (e) {
    e.preventDefault();
    $(".cardImportPType").hide(800);
    $(".cardCreatePType").toggle(800);
    $("#btnCreatePType").text("Crear");

    sessionStorage.removeItem("id_product_type");

    $("#formCreatePType").trigger("reset");
  });

  /* Crear area */

  $("#btnCreatePType").click(function (e) {
    e.preventDefault();
    let idProductType = sessionStorage.getItem("id_product_type") || null;

    const apiUrl = idProductType
      ? "/api/updateProductsTypes"
      : "/api/addProductsTypes";

    checkDataPType(apiUrl, idProductType);
  });

  /* Actualizar area */

  $(document).on("click", ".updateProductType", function (e) {
    $(".cardImportPType").hide(800);
    $(".cardCreatePType").show(800);
    $("#btnCreatePType").text("Actualizar");

    // Obtener el ID del elemento
    let idProductType = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_product_type", idProductType);

    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblProductsType.fnGetData(row);

    $("#productType").val(data.product_type);

    //animacion desplazamienot
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  const checkDataPType = async (url, idProductType) => {
    let product_type = $("#productType").val();

    if (product_type.trim() == "" || product_type.trim() == null) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    //preparar data
    let dataProduct = new FormData(formCreatePType);
    if (idProductType) dataProduct.append("idProductType", idProductType);

    //enviar data
    let resp = await sendDataPOST(url, dataProduct);
    messagePType(resp);
  };

  /* Eliminar areas */
  deletePTFunction = () => {
    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblProductsType.fnGetData(row);

    let id_product_type = data.id_product_type;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este tipo de producto? Esta acción no se puede reversar.",
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
            `/api/deleteProductsType/${id_product_type}`,
            function (data, textStatus, jqXHR) {
              messagePType(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messagePType = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportPType, .cardCreatePType").hide(800);
      $("#formImportPType, #formCreatePType").trigger("reset");

      toastr.success(message);
      loadAllDataPType();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
