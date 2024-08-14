$(document).ready(function () { 
  /* Ocultar panel crear producto */

  $(".cardCreatePMeasure").hide();

  /* Abrir panel crear producto */
  $('#btnNewPMeasure').click(function (e) {
    e.preventDefault();
    $(".cardCreatePMeasure").toggle(800);
    $(".cardImportPMeasure").hide(800);
    $("#btnCreatePMeasure").html("Crear Medida");

    sessionStorage.removeItem("id_product_measure");

    $("#formCreatePMeasure").trigger("reset"); 
  });

  // Calcular Peso
  /* $(document).on('keyup', '.inputsCalc', function () {
    let grammage = parseFloat($('#grammage').val());
    let usefulLength = parseFloat($('#usefulLength').val());
    let totalWidth = parseFloat($('#totalWidth').val());

    isNaN(grammage) ? (grammage = 0) : grammage;
    isNaN(usefulLength) ? (usefulLength = 0) : usefulLength;
    isNaN(totalWidth) ? (totalWidth = 0) : totalWidth;

    let weight = (grammage * usefulLength * totalWidth) / 10000000;
    !isFinite(weight) ? (weight = 0) : weight;

    $('#weight').val(weight);
  }); */

  /* Crear producto */
  $('#btnCreatePMeasure').click(function (e) {
    e.preventDefault();
    let idProductMeasure = sessionStorage.getItem("id_product_measure");

    if (idProductMeasure == "" || idProductMeasure == null) {
      checkDataProduct("/api/addProductMeasure", idProductMeasure);
    } else {
      checkDataProduct("/api/updateProductMeasure", idProductMeasure);
    }
  });

  /* Actualizar productos */

  $(document).on("click", ".updatePMeasure", function (e) {
    $(".cardImportPMeasure").hide(800);
    $(".cardCreatePMeasure").show(800);
    $("#btnCreatePMeasure").html("Actualizar Medida");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idProductMeasure = id.split('-')[1]; 

    sessionStorage.setItem("id_product_measure", idProductMeasure);

    let row = $(this).parent().parent().parent()[0];
    let data = tblProducts.fnGetData(row);
    $(`#refProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop('selected', true);

    //$("#grammage").val(data.grammage);
    $("#width").val(data.width);
    $("#high").val(data.high);
    $("#length").val(data.length);
    $("#usefulLength").val(data.useful_length);
    $("#totalWidth").val(data.total_width);
    $("#window").val(data.window);
    //$("#weight").val(data.weight); 

    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  const checkDataProduct = async (url, idProductMeasure) => {
    let idProduct = parseFloat($("#refProduct").val());
    //let grammage = parseFloat($("#grammage").val());
    let width = parseFloat($("#width").val());
    let high = parseFloat($("#high").val());
    let length = parseFloat($("#length").val());
    let usefulLength = parseFloat($("#usefulLength").val());
    let totalWidth = parseFloat($("#totalWidth").val());
    let window = parseFloat($("#window").val());
    //let weight = parseFloat($("#weight").val());

    let data = idProduct * width * high * length * usefulLength * totalWidth;

    if (isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    } 

    let dataProduct = new FormData(formCreatePMeasure); 

    if (idProductMeasure != "" || idProductMeasure != null) {
      dataProduct.append("idProductMeasure", idProductMeasure);
    }

    let resp = await sendDataPOST(url, dataProduct);

    messageProducts(resp);
  };

  /* Eliminar productos */
  deletePMeasureFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    let idProductMeasure = data.id_product_measure;

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
          $.get(
            `/api/deleteProductMeasure/${idProductMeasure}`,
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
      $(".cardCreatePMeasure").hide(800);
      $(".cardImportPMeasure").hide(800);
      $("#formCreatePMeasure").trigger("reset");
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblProducts').DataTable().clear();
    $('#tblProducts').DataTable().ajax.reload();
  }
});
