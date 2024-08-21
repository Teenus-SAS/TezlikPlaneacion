$(document).ready(function () {  
  /* Ocultar panel para crear Areas */
  $(".cardCreatePType").hide();

  /* Abrir panel para crear Areas */

  $("#btnNewPType").click(function (e) {
    e.preventDefault();
    $('.cardImportPType').hide(800);
    $(".cardCreatePType").toggle(800);
    $("#btnCreatePType").html("Crear");

    sessionStorage.removeItem("id_product_type");

    $("#formCreatePType").trigger("reset");
  });

  /* Crear area */

  $("#btnCreatePType").click(function (e) {
    e.preventDefault();
    let idProductType = sessionStorage.getItem("id_product_type");
    if (idProductType == "" || idProductType == null) {
      checkDataPType("/api/addProductsTypes", idProductType);
    } else {
      checkDataPType("/api/updateProductsTypes", idProductType);
    }
  });

  /* Actualizar area */

  $(document).on("click", ".updateProductType", function (e) {
    $('.cardImportPType').hide(800);
    $(".cardCreatePType").show(800);
    $("#btnCreatePType").html("Actualizar");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idProductType = id.split('-')[1]; 

    sessionStorage.setItem("id_product_type", idProductType);

    let row = $(this).parent().parent()[0];
    let data = tblProductsType.fnGetData(row);

    $("#productType").val(data.product_type);

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

    let dataProduct = new FormData(formCreatePType);

    if (idProductType != "" || idProductType != null)
      dataProduct.append("idProductType", idProductType);

    let resp = await sendDataPOST(url, dataProduct);

    messagePType(resp);
  };

  /* Eliminar areas */
  deletePTFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
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
    if (data.success == true) {
      $(".cardImportPType").hide(800);
      $("#formImportPType").trigger("reset");
      $(".cardCreatePType").hide(800);
      $("#formCreatePType").trigger("reset");
      toastr.success(data.message);
      loadAllDataPType();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
