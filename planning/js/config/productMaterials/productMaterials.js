$(document).ready(function () { 
  /* Ocultar panel crear producto */

  $('.cardAddMaterials').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsMaterials').hide(800); 
    // $('.cardTableConfigMaterials').show(800);
    $('.cardAddMaterials').toggle(800);
    $('#btnAddMaterials').html('Asignar');

    sessionStorage.removeItem('id_product_material');

    $('#formAddMaterials').trigger('reset');
  });

  /* Adicionar unidad de materia prima */

  $('#material').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = sessionStorage.getItem('dataMaterials');
    if (data) {
      dataMaterials = JSON.parse(data);
      sessionStorage.removeItem('dataMaterials');
    }

    for (i = 0; i < dataMaterials.length; i++) {
      if (id == dataMaterials[i].id_material) {
        await loadUnitsByMagnitude(dataMaterials[i], 2);
      }
    }
  });

  /* Adicionar nueva materia prima */

  $('#btnAddMaterials').click(function (e) {
    e.preventDefault();

    let idProductMaterial = sessionStorage.getItem('id_product_material');

    if (idProductMaterial == '' || idProductMaterial == null) { 
      checkDataPMaterial('/api/addProductsMaterials', idProductMaterial);
    } else {
      checkDataPMaterial('/api/updatePlanProductsMaterials', idProductMaterial);
    }
  });

  /* Actualizar productos materials */

  $(document).on('click', '.updateMaterials',async function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').show(800);
    $('#btnAddMaterials').html('Actualizar');
    $('#units').empty();

    let row = $(this).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem('id_product_material', data.id_product_material);

    $(`#material option[value=${data.id_material}]`).prop('selected', true);
 
    $('#quantity').val(data.quantity);
    await loadUnitsByMagnitude(data, 2);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  checkDataPMaterial = async (url, idProductMaterial) => {
    let ref = $('#material').val();
    let quan = $('#quantity').val();
    let idProduct = $('#selectNameProduct').val();
 
    let data = ref * idProduct;

    if (!data || quan == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataMaterials = new FormData(formAddMaterials);
    dataMaterials.append('idProduct', idProduct);

    if (idProductMaterial != '' || idProductMaterial != null)
      dataMaterials.append('idProductMaterial', idProductMaterial);

    let resp = await sendDataPOST(url, dataMaterials);

    message(resp);
  } 

  /* Eliminar materia prima */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];

    let data = tblConfigMaterials.fnGetData(row);
    let dataMaterials = {};

    dataMaterials['idProductMaterial'] = data.id_product_material;
    dataMaterials['idMaterial'] = data.id_material;
    dataMaterials['idProduct'] = data.id_product;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta Materia prima? Esta acción no se puede reversar.',
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
          $.post('/api/deletePlanProductMaterial', dataMaterials,
            function (data, textStatus, jqXHR) {
              message(data);
            }, 
          ); 
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardImportProductsMaterials').hide(800);
      $('.cardAddMaterials').hide(800);
      $('#formImport').trigger('reset');
      $('#formAddMaterials').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblConfigMaterials').DataTable().clear();
    $('#tblConfigMaterials').DataTable().ajax.reload();
  }
});
