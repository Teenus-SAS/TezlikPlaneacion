$(document).ready(function () {
  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    let card = document.getElementsByClassName('cardHeader')[0];
    $('#card').remove();
    if (this.id == 'products') {
      $('.cardProducts').show(800);
      $('.cardMaterials').hide(800);
      $('.cardRawMaterials').hide(800);
      $('.cardImportMaterials').hide(800);

      card.insertAdjacentHTML('beforeend', `
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end" id="card">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewProduct"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewProducts"><i class="bi bi-cloud-arrow-up-fill mr-1"></i></button>
                                    </div>
                                </div>`);
    } else if (this.id == 'materials') {
      $('.cardMaterials').show(800);
      $('.cardProducts').hide(800);
      $('.cardCreateProduct').hide(800);
      $('.cardImportProducts').hide(800);

      card.insertAdjacentHTML('beforeend', `
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end" id="card">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewMaterial" name="btnNewMaterial"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewMaterials" name="btnNewImportMaterials"><i class="bi bi-cloud-arrow-up-fill mr-1"></i></button>
                                    </div>
                                </div>`);
    }

    let tables = document.getElementsByClassName(
      'dataTable'
    );

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = '100%';
      attr = tables[i].firstElementChild;
      attr.style.width = '100%';
    }
  });

  /* Ocultar panel crear producto */

  $('.cardCreateProduct').hide();

  /* Cargar imagen de producto */
  $('#formFile').change(function (e) {
    // e.preventDefault();

    $('#preview').html(
      `<img id="img" src="${URL.createObjectURL(
        event.target.files[0]
      )}" style="width:50%;padding-bottom:15px"/>`
    );
  });

  /* Abrir panel crear producto */
  $(document).on('click', '#btnNewProduct', function () { 
    $('.cardCreateProduct').toggle(800);
    $('.cardImportProducts').hide(800);
    $('#btnCreateProduct').html('Crear Producto');

    sessionStorage.removeItem('id_product');
 
    $('#formCreateProduct').trigger('reset');
    $('#img').remove();
  });

  /* Crear producto */
$(document).on('click','#btnCreateProduct', function () { 
    let idProduct = sessionStorage.getItem('id_product');

    if (idProduct == '' || idProduct == null) {
      checkDataProducts('/api/addProduct', idProduct);
    } else {
      checkDataProducts('/api/updatePlanProduct', idProduct); 
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
    $('#pQuantity').val(data.quantity); 
    
    if(data.img)
      $('#preview').html(
        `<img id="img" src="${data.img}" style="width:50%;padding-bottom:15px"/>`
      );

    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  checkDataProducts = async (url, idProduct) => {
    let ref = $('#referenceProduct').val();
    let prod = $('#product').val();

    if (ref.trim() == '' || !ref.trim() || prod.trim() == '' || !prod.trim()) {
      toastr.error('Ingrese todos los campos');
      return false;
    } 

    let imageProd = $('#formFile')[0].files[0];

    let dataProduct = new FormData(formCreateProduct);
    dataProduct.append('img', imageProd);

    if (idProduct != '' || idProduct != null) {
      dataProduct.append('idProduct', idProduct);
    }

    let resp = await sendDataPOST(url, dataProduct);

    messageProducts(resp);
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
      $('#formImportProduct').trigger('reset');
      $('.cardCreateProduct').hide(800);
      $('.cardImportProducts').hide(800);
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
