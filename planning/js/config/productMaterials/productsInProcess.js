$(document).ready(function () {
  let finalProduct;
  sessionStorage.removeItem('dataTable');

  // Ocultar card productos en proceso
  $('.cardAddProductInProccess').hide();

  // Mostrar lista de productos en proceso
  $.ajax({
    type: 'GET',
    url: '/api/productsInProcess',
    success: function (r) {
      let $select = $(`#product`);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });
    },
  });

  // Mostrar card productos en proceso
  $('#btnCreateProductInProcess').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').hide(800);
    $('.cardTableConfigMaterials').hide(800);
    $('.cardTableProductsInProcess').show(800);
    $('.cardAddProductInProccess').toggle(800);

    $('#comment').html('Asignación de productos en proceso');
    $('#btnAddProductInProccess').html('Asignar');

    sessionStorage.removeItem('id_product_category');

    $('#formAddProductInProccess').trigger('reset');
  });

  // Seleccionar producto final
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    finalProduct = $('#selectNameProduct').val();
  });

  // Guardar Productos en proceso
  $('#btnAddProductInProccess').click(function (e) {
    e.preventDefault();

    let idProductCategory = sessionStorage.getItem('id_product_category');

    if (idProductCategory == '' || idProductCategory == null) {
      idProduct = $('#product').val();
      finalProduct = $('#selectNameProduct').val();

      data = idProduct * finalProduct;

      if (!data || data == 0) {
        toastr.error('Seleccione producto');
        return false;
      }

      productInProcess = $('#formAddProductInProccess').serialize();
      productInProcess = `${productInProcess}&finalProduct=${finalProduct}`;

      $.post(
        '/api/addProductInProcess',
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
    let row = $(this.activeElement).parent().parent()[0];

    let data = tblProductsInProcess.fnGetData(row);

    idProductCategory = data.id_product_category;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este Producto? Esta acción no se puede reversar.',
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
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
    if (data.success == true) {
      $('.cardAddProductInProccess').hide(800);
      $('#formAddProductInProccess').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblProductsInProcess').DataTable().clear();
    $('#tblProductsInProcess').DataTable().ajax.reload();
  }
});
