$(document).ready(function () {
  /* Ocultar panel para crear materiales */

  $('.cardRawMaterials').hide();

  /* Abrir panel para crear materiales */
  $('#btnNewMaterial').click(function (e) {
    e.preventDefault();
    $('.cardImportMaterials').hide(800);
    $('.cardRawMaterials').toggle(800);
    $('#btnCreateMaterial').html('Crear');
    $('#units').empty();

    sessionStorage.removeItem('id_material');

    $('#formCreateMaterial').trigger('reset');
  });

  /* Crear producto */
  $('#btnCreateMaterial').click(function (e) {
    e.preventDefault();
    let idMaterial = sessionStorage.getItem('id_material');

    if (idMaterial == '' || idMaterial == null) {
      checkDataMaterial('/api/addMaterials', idMaterial);
    } else {
      checkDataMaterial('/api/updateMaterials', idMaterial);
    }
  });

  /* Actualizar productos */

  $(document).on('click', '.updateRawMaterials', function (e) {
    $('.cardImportMaterials').hide(800);
    $('#units').empty();
    $('.cardRawMaterials').show(800);
    $('#btnCreateMaterial').html('Actualizar');

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idMaterial = id.split('-')[1]; 

    sessionStorage.setItem('id_material', idMaterial);

    let row = $(this).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);

    $('#refRawMaterial').val(data.reference);
    $('#nameRawMaterial').val(data.material);
    $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
    loadUnitsByMagnitude(data.id_magnitude, 1);
    $(`#units option[value=${data.id_unit}]`).prop('selected', true);

    quantity = data.quantity;

    // if (quantity.isInteger) quantity = quantity.toLocaleString('es-CO');
    // else
    //   quantity = quantity.toLocaleString(undefined, {
    //     minimumFractionDigits: 2,
    //     maximumFractionDigits: 2,
    //   });
    $('#mQuantity').val(quantity);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data materia prima */
  const checkDataMaterial = async (url, idMaterial) => {
    let ref = $('#refRawMaterial').val();
    let material = $('#nameRawMaterial').val();
    let unity = $('#unit').val();
    let quantity = parseFloat($('#mQuantity').val());

    if (ref == '' || material == '' || unity == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    // quantity = parseFloat(strReplaceNumber(quantity));

    quantity = 1 * quantity;

    if (quantity <= 0 || isNaN(quantity)) {
      toastr.error('La cantidad debe ser mayor a cero (0)');
      return false;
    }

    let dataMaterial = new FormData(formCreateMaterial);

    if (idMaterial != '' || idMaterial != null)
      dataMaterial.append('idMaterial', idMaterial);

    let resp = await sendDataPOST(url, dataMaterial);

    messageMaterials(resp);
  };

  /* Eliminar productos */

  deleteMaterialsFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblRawMaterials.fnGetData(row);

    let idMaterial = data.id_material;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta materia prima? Esta acción no se puede reversar.',
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
            `/api/deleteMaterial/${idMaterial}`,
            function (data, textStatus, jqXHR) {
              messageMaterials(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMaterials = (data) => {
    if (data.success == true) {
      $('.cardImportMaterials').hide(800);
      $('#formImportMaterials').trigger('reset');
      $('.cardRawMaterials').hide(800);
      $('#formCreateMaterial').trigger('reset');
      toastr.success(data.message);
      loadAllData();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
