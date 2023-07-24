$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateProduct').hide();

  /* Cargar imagen de producto */
  $('#formFile').change(function (e) {
    e.preventDefault();

    $('#preview').html(
      `<img id="img" src="${URL.createObjectURL(
        event.target.files[0]
      )}" style="width:50%;padding-bottom:15px"/>`
    );
  });

  /* Abrir panel crear producto */

  $('#btnNewProduct').click(function (e) {
    e.preventDefault();

    $('.cardCreateProduct').toggle(800);
    $('.cardImportProducts').hide(800);
    $('#btnCreateProduct').html('Crear Producto');

    sessionStorage.removeItem('id_product');
 
    $('#formCreateProduct').trigger('reset');
    $('#img').remove();
  });

  /* Crear producto */

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();
    let idProduct = sessionStorage.getItem('id_product');

    if (idProduct == '' || idProduct == null) {
      ref = $('#referenceProduct').val();
      prod = $('#product').val();

      if (
        ref == '' ||
        ref == 0 ||
        prod == '' ||
        prod == 0 
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }
 
      let imageProd = $('#formFile')[0].files[0];

      dataProduct = new FormData(formCreateProduct);
      dataProduct.append('img', imageProd);

      $.ajax({
        type: 'POST',
        url: '/api/addProduct',
        data: dataProduct,
        contentType: false,
        cache: false,
        processData: false,

        success: function (resp) {
          $('.cardCreateProduct').hide(800);
          $('.cardImportProducts').hide(800);
          $('#formFile').val(''); 
          message(resp);
          updateTable();
        },
      });
    } else {
      updateProduct();
    }
  });

  /* Actualizar productos */

  $(document).on('click', '.updateProducts', function (e) {
    $('.cardImportProducts').hide(800);
    $('.cardCreateProduct').show(800);
    $('#btnCreateProduct').html('Actualizar Producto');

    idProduct = this.id;
    idProduct = sessionStorage.setItem('id_product', idProduct);

    let row = $(this).parent().parent()[0];
    let data = tblProducts.fnGetData(row);
    $('#referenceProduct').val(data.reference);
    $('#product').val(data.product);
    $('#quantity').val(data.quantity); 
    
    if(data.img)
      $('#preview').html(
        `<img id="img" src="${data.img}" style="width:50%;padding-bottom:15px"/>`
      );

    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  updateProduct = () => {
    let idProduct = sessionStorage.getItem('id_product');
    let imageProd = $('#formFile')[0].files[0];

    dataProduct = new FormData(formCreateProduct);
    dataProduct.append('idProduct', idProduct);
    dataProduct.append('img', imageProd);

    $.ajax({
      type: 'POST',
      url: '/api/updatePlanProduct',
      data: dataProduct,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        $('.cardCreateProduct').hide(800);
        $('.cardImportProducts').hide(800);
        updateTable();
        $('#formFile').val('');
        message(resp);
      },
    });
  };

  /* Eliminar productos */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    let idProduct = data.id_product;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este producto? Esta acción no se puede reversar.',
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
        if (result == true) {
          $.get(
            `/api/deletePlanProduct/${idProduct}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* $(document).on('click', '.deleteProducts', function (e) {
    let idProduct = this.id;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este producto? Esta acción no se puede reversar.',
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
        if (result == true) {
          $.get(
            `/api/deletePlanProduct/${idProduct}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  }); */

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('.cardCreateProduct').hide(800);
      $('#formCreateProduct').trigger('reset');
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
