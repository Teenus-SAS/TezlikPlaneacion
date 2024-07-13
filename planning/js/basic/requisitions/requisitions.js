$(document).ready(function () {
  loadClients(2);

  /* Ocultar panel crear producto */

  $('.cardAddRequisitions').hide();

  /* Abrir panel crear producto */

  $('#btnNewRequisition').click(function (e) {
    e.preventDefault();

    $('.cardImportRequisitions').hide(800);
    $('.cardTableConfigMaterials').show(800);
    $('.cardAddRequisitions').toggle(800);
    $('#btnAddRequisition').html('Asignar');

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

  $('#material').change(function (e) {
    e.preventDefault();

    $('#client option').removeAttr('selected');
    $(`#client option[value='0']`).prop('selected', true);
    $('#rMQuantity').val('');
    $('#rMAverage').val('');

    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.filter(item => item.id_material == this.value);

    if (arr.length == 1) {
      $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
      $('#rMQuantity').val(arr[0].min_quantity);
      $('#rMAverage').val(arr[0].average);
    } else if (arr.length > 1) {
      arr = arr.sort((a, b) => a.average - b.average);

      // Verificar si todos los tiempos promedio son iguales 
      const firstValue = arr[0]['average'];
      const allSame = arr.every(item => item['average'] === firstValue);

      if (allSame == true) {
        arr = arr.sort((a, b) => a.min_quantity - b.min_quantity);
      }

      $(`#client option[value=${arr[0].id_provider}]`).prop('selected', true);
      $('#rMQuantity').val(arr[0].min_quantity);
      $('#rMAverage').val(parseFloat(arr[0].max_term) - parseFloat(arr[0].min_term));
    }
  });

  $('#client').change(function (e) {
    e.preventDefault();
    let id_material = $('#material').val();

    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.find(item => item.id_material == id_material && item.id_provider == this.value);

    if (arr) {
      $('#rMQuantity').val(arr.min_quantity);
      $('#rMAverage').val(arr.average);
    }
  });

  /* Actualizar productos materials */

  $(document).on('click', '.updateRequisition', async function (e) {
    $('.cardImportRequisitions').hide(800);
    $('.cardAddRequisitions').show(800);
    $('#btnAddRequisition').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblRequisitions.fnGetData(row);

    sessionStorage.setItem('id_requisition', data.id_requisition);
 
    $(`#material option[value=${data.id_material}]`).prop('selected', true);
    $(`#client option[value=${data.id_provider}]`).prop('selected', true);
    $('#applicationDate').val(data.application_date);
    $('#deliveryDate').val(data.delivery_date);
    $('#purchaseOrder').val(data.purchase_order);
    
    let dataStock = JSON.parse(sessionStorage.getItem('stock'));
    let arr = dataStock.find(item => item.id_material == data.id_material && item.id_provider == data.id_provider);

    if (arr) {
      $('#rMQuantity').val(arr.min_quantity);
      $('#rMAverage').val(arr.average);
    }

    let quantity = data.quantity
            
    if (data.abbreviation === 'UND') quantity = Math.floor(quantity);
    
    $('#quantity').val(quantity);
    
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  checkDataRequisition = async (url, idRequisition) => {
    let material = $('#material').val();
    let provider = $('#client').val();
    let applicationDate = $('#applicationDate').val();
    let deliveryDate = $('#deliveryDate').val();
    let quan = $('#quantity').val();

    let data = quan * material * provider;

    if (!data || applicationDate == '' || deliveryDate == '' || quan == '') {
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

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];

    let data = tblRequisitions.fnGetData(row);

    idRequisition = data.id_requisition;

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
          $.get(
            `/api/deleteRequisition/${idRequisition}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
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
