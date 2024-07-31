$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardAddRequisitions').hide();

  /* Abrir panel crear producto */

  $('#btnNewRequisition').click(function (e) {
    e.preventDefault();

    $('.cardImportRequisitions').hide(800);
    $('.cardTableConfigMaterials').show(800);
    $('.cardRequired').hide();
    $('.cardAddRequisitions').toggle(800);
    $('#btnAddRequisition').html('Asignar');
    $('.cardSelect').show();
    $('.cardDescription').hide();
    document.getElementById('requestedQuantity').readOnly = false;

    sessionStorage.removeItem('id_requisition');

    $('#formAddRequisition').trigger('reset');
  });

  /* Adicionar nueva materia prima */

  $('#btnAddRequisition').click(function (e) {
    e.preventDefault();

    let idRequisition = sessionStorage.getItem('id_requisition');

    if (idRequisition == '' || idRequisition == null) {
      checkDataRequisition('/api/addRequisition', idRequisition);
    } else {
      checkDataRequisition('/api/updateRequisition', idRequisition);
    }
  });
  
  $('.cardSearchDate').hide();

  $('#btnOpenSearchDate').click(function (e) {
    e.preventDefault();

    $('.cardSearchDate').toggle(800);
    $('#formSearchDate').trigger('reset');
    let date = new Date().toISOString().split('T')[0];

    $('#lastDate').val(date);

    let maxDate = document.getElementById('lastDate');
    let minDate = document.getElementById('firtsDate');

    maxDate.setAttribute("max", date);
    minDate.setAttribute("max", date);
  });

  $('#btnSearchDate').click(async function (e) {
    e.preventDefault();
        
    let firtsDate = $('#firtsDate').val();
    let lastDate = $('#lastDate').val();
                
    if (!firtsDate || firtsDate == '' || !lastDate || lastDate == '') {
      toastr.error('Ingrese los campos');
      return false;
    }

    loadTblRequisitions(firtsDate, lastDate);
  });

  function handleMaterialChange(event) {
    event.preventDefault();

    $('#client option').removeAttr('selected');
    $(`#client option[value='0']`).prop('selected', true);
    $('#rMQuantity').val('');
    $('#rMAverage').val('');

    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.filter(item => item.id_material == this.value);

    setInputClient(arr);

    if (arr.length == 1) {
      updateClientSelection(arr[0]);
    } else if (arr.length > 1) {
      arr.sort((a, b) => a.average - b.average);

      // Verificar si todos los tiempos promedio son iguales 
      const firstValue = arr[0]['average'];
      const allSame = arr.every(item => item['average'] === firstValue);

      if (allSame) {
        arr.sort((a, b) => a.min_quantity - b.min_quantity);
      }

      updateClientSelection(arr[0], true);
    }
  }

  function updateClientSelection(item, isMultiple = false) {
    $(`#client option[value=${item.id_provider}]`).prop('selected', true);
    $('#rMQuantity').val(`${item.min_quantity} ${item.abbreviation}`);
    $('#rMAverage').val(isMultiple ? (parseFloat(item.max_term) - parseFloat(item.min_term)) : item.average);
  }

  $('#refMaterial').change(handleMaterialChange);
  $('#material').change(handleMaterialChange);

  // $('#refMaterial').change(function (e) {
  //   e.preventDefault();

  //   $('#client option').removeAttr('selected');
  //   $(`#client option[value='0']`).prop('selected', true);
  //   $('#rMQuantity').val('');
  //   $('#rMAverage').val('');

  //   let dataStock = JSON.parse(sessionStorage.getItem('stock'));
  //   let arr = dataStock.filter(item => item.id_material == this.value); 

  //   setInputClient(arr);

  //   if (arr.length == 1) {
  //     $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
  //     $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
  //     $('#rMAverage').val(arr[0].average);
  //   } else if (arr.length > 1) {
  //     arr = arr.sort((a, b) => a.average - b.average);

  //     // Verificar si todos los tiempos promedio son iguales 
  //     const firstValue = arr[0]['average'];
  //     const allSame = arr.every(item => item['average'] === firstValue);

  //     if (allSame == true) {
  //       arr = arr.sort((a, b) => a.min_quantity - b.min_quantity);
  //     }

  //     $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
  //     $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
  //     $('#rMAverage').val(parseFloat(arr[0].max_term) - parseFloat(arr[0].min_term));
  //   }
  // });

  // $('#material').change(function (e) {
  //   e.preventDefault();

  //   $('#client option').removeAttr('selected');
  //   $(`#client option[value='0']`).prop('selected', true);
  //   $('#rMQuantity').val('');
  //   $('#rMAverage').val('');

  //   let dataStock = JSON.parse(sessionStorage.getItem('stock'));
  //   let arr = dataStock.filter(item => item.id_material == this.value); 

  //   setInputClient(arr);

  //   if (arr.length == 1) {
  //     $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
  //     $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
  //     $('#rMAverage').val(arr[0].average);
  //   } else if (arr.length > 1) {
  //     arr = arr.sort((a, b) => a.average - b.average);

  //     // Verificar si todos los tiempos promedio son iguales 
  //     const firstValue = arr[0]['average'];
  //     const allSame = arr.every(item => item['average'] === firstValue);

  //     if (allSame == true) {
  //       arr = arr.sort((a, b) => a.min_quantity - b.min_quantity);
  //     }

  //     $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
  //     $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
  //     $('#rMAverage').val(parseFloat(arr[0].max_term) - parseFloat(arr[0].min_term));
  //   }
  // });

  $('#client').change(function (e) {
    e.preventDefault();
    let id_material = $('#material').val();

    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.find(item => item.id_material == id_material && item.id_provider == this.value);

    if (arr) {
      $('#rMQuantity').val(`${arr.min_quantity} ${arr.abbreviation}`);
      $('#rMAverage').val(arr.average);
    }
  });

  /* Actualizar productos materials */

  $(document).on('click', '.updateRequisition', async function (e) {
    $('.cardImportRequisitions').hide(800);
    $('.cardAddRequisitions').show(800);
    $('.cardRequired').show();
    $('#btnAddRequisition').html('Actualizar');
    $('.cardDescription').show();
    $('.cardSelect').hide();

    let row = $(this).parent().parent()[0];
    let data = tblRequisitions.fnGetData(row);

    sessionStorage.setItem('id_requisition', data.id_requisition);
 
    $(`#refMaterial option[value=${data.id_material}]`).prop('selected', true);
    $(`#material option[value=${data.id_material}]`).prop('selected', true);
    $('#referenceMName').val(data.reference);
    $('#materialName').val(data.material);

    if (data.application_date != '0000-00-00' && data.application_date)
      $('#applicationDate').val(data.application_date);
    if (data.delivery_date != '0000-00-00' && data.delivery_date)
      $('#deliveryDate').val(data.delivery_date);
    if (data.purchase_order != '0000-00-00' && data.purchase_order)
      $('#purchaseOrder').val(data.purchase_order);
    
    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.filter(item => item.id_material == data.id_material);

    setInputClient(arr);
 
    $(`#client option[value=${data.id_provider}]`).prop('selected', true);

    if (arr.length == 1 && data.id_provider != 0) {
      $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
      $('#rMAverage').val(arr[0].average);
    } else if (arr.length > 1) {
      arr = arr.sort((a, b) => a.average - b.average);

      // Verificar si todos los tiempos promedio son iguales 
      const firstValue = arr[0]['average'];
      const allSame = arr.every(item => item['average'] === firstValue);

      if (allSame == true) {
        arr = arr.sort((a, b) => a.min_quantity - b.min_quantity);
      }

      $('#rMQuantity').val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
      $('#rMAverage').val(parseFloat(arr[0].max_term) - parseFloat(arr[0].min_term));
    }

    let quantity_required = parseFloat(data.quantity_required).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
    if (data.abbreviation === 'UND') quantity_required = Math.floor(data.quantity_required);
    
    $('#requiredQuantity').val(`${quantity_required} ${data.abbreviation}`);
    
    let quantity_requested = data.quantity_requested
            
    if (data.abbreviation === 'UND') quantity_requested = Math.floor(quantity_requested);

    $('#requestedQuantity').val(quantity_requested);
    
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataRequisition = async (url, idRequisition) => {
    let material = $('#material').val();
    let provider = $('#client').val();
    let applicationDate = $('#applicationDate').val();
    let deliveryDate = $('#deliveryDate').val();
    // let quan = $('#requiredQuantity').val();
    let r_quan = $('#requestedQuantity').val();

    let data = r_quan * material * provider;

    if (!data || applicationDate == '' || deliveryDate == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }
    
    if (applicationDate > deliveryDate) {
      toastr.error('Ingrese fecha de solicitud menor a la fecha de entrega');
      return false;
    }

    let dataRequisition = new FormData(formAddRequisition);

    if (idRequisition != '' || idRequisition != null)
      dataRequisition.append('idRequisition', idRequisition);

    let resp = await sendDataPOST(url, dataRequisition);

    message(resp);
  }

  /* Eliminar materia prima */

  deleteFunction = (op) => {
    let row = $(this.activeElement).parent().parent()[0];

    let data = tblRequisitions.fnGetData(row);

    let dataRequisition = {};
    dataRequisition['idRequisition'] = data.id_requisition;
    dataRequisition['idMaterial'] = data.id_material;
    dataRequisition['op'] = op;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta requisicion? Esta acción no se puede reversar.',
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
          $.post('/api/deleteRequisition', dataRequisition,
            function (data, textStatus, jqXHR) {
              message(data);
            }, 
          );
        }
      },
    });
  };

  $(document).on('click', '.changeDate', function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split('T')[0];
    let row = $(this).parent().parent()[0];
    let data = tblRequisitions.fnGetData(row);

    bootbox.confirm({
      title: 'Ingrese Fecha De Ingreso!',
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date" max="${date}"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
      buttons: {
        confirm: {
          label: 'Agregar',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancelar',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result) {
          let date = $('#date').val();

          if (!date) {
            toastr.error('Ingrese los campos');
            return false;
          }

          let form = new FormData();
          form.append('idRequisition', data.id_requisition);
          form.append('idMaterial', data.id_material);
          form.append('date', date);

          $.ajax({
            type: "POST",
            url: '/api/saveAdmissionDate',
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              message(resp);
            }
          });
        }
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardImportRequisitions').hide(800);
      $('#formImportRequisitions').trigger('reset');
      $('.cardAddRequisitions').hide(800);
      $('#formAddRequisition').trigger('reset');
      loadAllData(null, null, null);
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
