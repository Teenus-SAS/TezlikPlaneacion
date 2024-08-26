$(document).ready(function () { 
   $('.selectNavigation').click(function (e) {
    e.preventDefault();

    let option = this.id;
    $('.cardPMeasure').hide();
    $('.cardCreatePMeasure').hide();
    $('.cardImportPMeasure').hide();
    $('.cardPTypes').hide();
    $('.cardCreatePType').hide(); 

    switch (option) {
      case 'link-products':
        $('.cardPMeasure').show();
        break;
      case 'link-productsType':
        $('.cardPTypes').show();
        break;
    }

    let tables = document.getElementsByClassName('dataTable');

    for (let table of tables) {
      table.style.width = '100%';
      table.firstElementChild.style.width = '100%';
    }
   });
  
  /* Ocultar panel crear producto */
  $(".cardCreatePMeasure").hide();

  /* Abrir panel crear producto */
  $('#btnNewPMeasure').click(function (e) {
    e.preventDefault();
    $(".cardCreatePMeasure").toggle(800);
    $('.inputsMeasures').show();
    $(".cardImportPMeasure").hide(800);
    $("#btnCreatePMeasure").html("Crear");

    sessionStorage.removeItem("id_product_measure");

    $("#formCreatePMeasure").trigger("reset"); 
  });

  $('#prodOrigin').change(function (e) { 
    e.preventDefault();

    let option = this.value;

    switch (option) {
      case '1': // Comercializado
        $('.inputsMeasures').hide(800);
        break;
    }    
  }); 

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
    $("#btnCreatePMeasure").html("Actualizar");

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idProductMeasure = id.split('-')[1]; 

    sessionStorage.setItem("id_product_measure", idProductMeasure);
    
    let row = $(this).parent().parent().parent()[0];
    let data = tblProducts.fnGetData(row);
    
    sessionStorage.setItem("id_product", data.id_product);
    $("#referenceProduct").val(data.reference);
    $("#product").val(data.product);
    $(`#idProductType option[value=${data.id_product_type}]`).prop('selected', true);
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
    let ref = $("#referenceProduct").val();
    let prod = $("#product").val(); 
    let idProductType = parseFloat($('#idProductType').val());
    let width = parseFloat($("#width").val());
    let high = parseFloat($("#high").val());
    let length = parseFloat($("#length").val());
    let usefulLength = parseFloat($("#usefulLength").val());
    let totalWidth = parseFloat($("#totalWidth").val());
    let window = parseFloat($("#window").val());
    let prodOrigin = parseFloat($("#prodOrigin").val());

    let data = 1 * idProductType;

    if(prodOrigin == '1')
      data = width * high * length * usefulLength * totalWidth;

    if (ref.trim() == "" || !ref.trim() || prod.trim() == "" || !prod.trim()|| isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    } 

    let dataProduct = new FormData(formCreatePMeasure); 

    if (idProductMeasure != null) {
      dataProduct.append("idProductMeasure", idProductMeasure);
      
      let idProduct = sessionStorage.getItem('id_product');
      dataProduct.append("idProduct", idProduct);
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
