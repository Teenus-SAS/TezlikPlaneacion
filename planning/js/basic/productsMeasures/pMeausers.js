$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    const option = this.id;
    const sections = {
      "link-products": ".cardPMeasure",
      "link-productsType": ".cardPTypes",
    };

    // Ocultar todas las secciones
    $(
      ".cardPMeasure, .cardCreatePMeasure, .cardImportPMeasure, .cardPTypes, .cardCreatePType"
    ).hide();
    //Ocultar el Tipo de Producto
    //$(".cardSelect").hide();

    // Mostrar la sección correspondiente según la opción seleccionada
    $(sections[option] || "").show();

    // Ajustar el tamaño de todas las tablas con clase 'dataTable'
    $(".dataTable")
      .css({ width: "100%" })
      .children("thead")
      .css({ width: "100%" });
  });

  /* Ocultar panel crear producto */
  $(".cardCreatePMeasure").hide();

  /* Abrir panel crear producto */
  $("#btnNewPMeasure").click(function (e) {
    e.preventDefault();

    $(".cardCreatePMeasure").toggle(800);
    $(".inputsMeasures, .inputs").show();
    $(".cardImportPMeasure").hide(800);
    $("#btnCreatePMeasure").text("Crear");
    $("#lblWindow").text("Ventanilla");

    sessionStorage.removeItem("id_product_measure");

    $("#formCreatePMeasure").trigger("reset");
  });

  //Select origin product
  $("#prodOrigin").change(function (e) {
    e.preventDefault();
    const option = this.value;
    $(".cardSelect").show(800);
    $(".inputsMeasures").toggle(option === "2", 800);

    if ($("#idProductType").val() !== "Seleccionar") {
      $("#idProductType").change();
    }
  });

  // Select type Product
  $("#idProductType").change(function (e) {
    e.preventDefault();

    let optionOrigin = $("#prodOrigin").val();
    let option = $("#idProductType option:selected").text().trim();

    // Si el origen del producto es "MANUFACTURADO", ajustar la visualización de inputs
    if (optionOrigin === "2") {
      $(".inputs").toggle(
        !["CAJA", "SACHET", "LAMINA", "DOYPACK"].includes(option),
        800
      );
      $("#lblWindow").html(
        ["CAJA", "SACHET", "LAMINA", "DOYPACK"].includes(option)
          ? "Und x Tamaño"
          : "Ventanilla"
      );
    } else {
      $(".inputsMeasures").hide(800);
      $(".cardSelect").hide();
    }
  });

  /* Crear producto */
  $("#btnCreatePMeasure").click(function (e) {
    e.preventDefault();
    let idProductMeasure = sessionStorage.getItem("id_product_measure") || "";

    const apiUrl = idProductMeasure
      ? "/api/updateProductMeasure"
      : "/api/addProductMeasure";

    checkDataProduct(apiUrl, idProductMeasure);
  });

  /* Actualizar productos */
  $(document).on("click", ".updatePMeasure", function (e) {
    $(".cardImportPMeasure").hide(800);
    $(".cardCreatePMeasure, .inputsMeasures").show(800);
    $("#btnCreatePMeasure").text("Actualizar");
    $("#lblWindow").text("Ventanilla");

    // Obtener ID
    let idProductMeasure = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_product_measure", idProductMeasure);

    //obtener data
    let row = $(this).parent().parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    sessionStorage.setItem("id_product", data.id_product);
    $(`#prodOrigin option[value=${data.origin}]`).prop("selected", true);
    $("#referenceProduct").val(data.reference);
    $("#product").val(data.product);
    $(`#idProductType option[value=${data.id_product_type}]`).prop(
      "selected",
      true
    );
    $("#width").val(data.width);
    $("#high").val(data.high);
    $("#length").val(data.length);
    $("#usefulLength").val(data.useful_length);
    $("#totalWidth").val(data.total_width);
    $("#window").val(data.window);
    $("#inks").val(data.inks);

    if (data.origin == "1") {
      // Comercializado
      $(".inputsMeasures").hide();
    }

    if (data.product_type == "CAJA") {
      $(".inputs").hide();
      $("#lblWindow").text("Und x Tamaño");
    }

    //Hide inputs according Product
    $("#prodOrigin").change();

    //animacion desplazamiento
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  const checkDataProduct = async (url, idProductMeasure) => {
    const prodOrigin = parseFloat($("#prodOrigin").val());
    const productType = $("#idProductType option:selected").text().trim();
    const idProductType = parseFloat($("#idProductType").val());
    const ref = $("#referenceProduct").val().trim();
    const prod = $("#product").val().trim();
    let width, high, length, usefulLength, totalWidth, window;

    if (flag_products_measure == "1") {
      if (isNaN(idProductType) || idProductType <= 0) {
        toastr.error("Ingrese todos los campos");
        return false;
      }
    }

    let data = 1 * prodOrigin;

    if (prodOrigin == 2) {
      if (idProductType === 1) {
        width = parseFloat($("#width").val());
        high = parseFloat($("#high").val());
        length = parseFloat($("#length").val());
        usefulLength = parseFloat($("#usefulLength").val());
        totalWidth = parseFloat($("#totalWidth").val());
        window = parseFloat($("#window").val());
      } else {
        length = parseFloat($("#length").val());
        totalWidth = parseFloat($("#totalWidth").val());
        window = parseFloat($("#window").val());
      }
      let inks = parseFloat($("#inks").val());

      if (flag_products_measure == "1" && idProductType == "1") {
        data *=
          idProductType * width * high * length * usefulLength * totalWidth;
        if (prodOrigin == "2" && productType == "CAJA") data *= window;
      } else if (flag_products_measure == "1" && idProductType == "2") {
        data *= idProductType * length * totalWidth;
      }
    }

    if (!ref.trim() || !prod.trim() || isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    // Preparar datos
    const dataProduct = new FormData(formCreatePMeasure);

    if (idProductMeasure) {
      const idProduct = sessionStorage.getItem("id_product");
      dataProduct.append("idProductMeasure", idProductMeasure);
      dataProduct.append("idProduct", idProduct);
    }

    if (flag_products_measure == "0") dataProduct.append("idProductType", 0);

    // Envío de datos
    let resp = await sendDataPOST(url, dataProduct);
    messageProducts(resp);
  };

  /* Eliminar productos */
  deletePMeasureFunction = () => {
    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblProducts.fnGetData(row);

    let dataProduct = {};
    dataProduct["idProductMeasure"] = data.id_product_measure;
    dataProduct["idProduct"] = data.id_product;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta medida? Esta acción no se puede reversar.",
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
          $.post(
            "/api/deleteProductMeasure",
            dataProduct,
            function (data, textStatus, jqXHR) {
              messageProducts(data);
            }
          );
        }
      },
    });
  };

  /* Productos Compuestos */

  $(document).on("click", ".composite", function () {
    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblProducts.fnGetData(row);

    bootbox.confirm({
      title: "Producto Compuesto",
      message: `Está seguro de que este producto ${
        data.composite == "0"
          ? "se <b>convierta en un subproducto</b> para ser agregado a un producto compuesto"
          : "se <b>Elimine</b> como subproducto"
      }?`,
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
        if (result == true) {
          $.get(
            `/api/changeComposite/${data.id_product}/${
              data.composite == "0" ? "1" : "0"
            }`,
            function (data, textStatus, jqXHR) {
              messageProducts(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */
  messageProducts = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreatePMeasure, .cardImportPMeasure").hide(800);
      $("#formImportProduct, #formCreatePMeasure").trigger("reset");

      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblProducts").DataTable().clear();
    $("#tblProducts").DataTable().ajax.reload();
  }
});
