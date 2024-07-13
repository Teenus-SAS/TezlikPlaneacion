$(document).ready(function () { 
  /* Ocultar panel crear producto */

  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    if (this.id == 'materials') {
      $('.cardProductsMaterials').show();
      $('.cardPlanCicles').hide();
      $('.cardRoutes').hide();
      $('.cardCreatePlanCiclesMachine').hide();
      $('.cardImportPlanCiclesMachine').hide();
    } else if (this.id == 'planCicles') {
      $('.cardPlanCicles').show();
      $('.cardProductsMaterials').hide();
      $('.cardRoutes').hide();
      $('.cardAddMaterials').hide();
      $('.cardImport').hide();
    } else {
      $('.cardRoutes').show();
      $('.cardPlanCicles').hide();
      $('.cardProductsMaterials').hide();
      $('.cardAddMaterials').hide();
      $('.cardImport').hide();
      
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

  $(document).on('click', '.updateMaterials', function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').show(800);
    $('#btnAddMaterials').html('Actualizar');
    $('#units').empty();

    let row = $(this).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem('id_product_material', data.id_product_material);

    $(`#refMaterial option[value=${data.id_material}]`).prop('selected', true);
    $(`#material option[value=${data.id_material}]`).prop('selected', true);
    
    $('#quantity').val(data.quantity);
    
    if (data.id_magnitude == 0 || data.id_unit == 0) {
      let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

      let arr = dataMaterials.find(item => item.id_material == data.id_material);

      data.id_magnitude = arr.id_magnitude;
      data.id_unit = arr.id_unit;
    }

    loadUnitsByMagnitude(data, 2);
    $(`#units option[value=${data.id_unit}]`).prop('selected', true);

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
    let unit = $('#units').val();
 
    let data = ref * idProduct * unit;

    if (!data || quan == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataMaterials = new FormData(formAddMaterials);
    dataMaterials.append('idProduct', idProduct);

    if (idProductMaterial != '' || idProductMaterial != null)
      dataMaterials.append('idProductMaterial', idProductMaterial);

    let resp = await sendDataPOST(url, dataMaterials);

    messageMaterial(resp);
  } 

  /* Eliminar materia prima */

  deleteMaterial = () => {
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
        if (result) {
          $.post('/api/deletePlanProductMaterial', dataMaterials,
            function (data, textStatus, jqXHR) {
              messageMaterial(data);
            }, 
          ); 
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMaterial = (data) => {
    if (data.success == true) {
      $('.cardImport').hide(800);
      $('.cardAddMaterials').hide(800);
      $('#formImport').trigger('reset');
      $('#formAddMaterials').trigger('reset'); 

      const idProduct = $('#selectNameProduct').val()

      if(idProduct)
        loadtableMaterials(idProduct);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
