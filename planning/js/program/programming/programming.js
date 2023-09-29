$(document).ready(function () {
  // Obtener referencia producto
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).prop('selected', true);
  });
  
  /* Ocultar panel crear programa de producción */
  $('.cardCreateProgramming').hide();
  
  /* Abrir panel crear programa de producción */
  
  $('#btnNewProgramming').click(async function (e) {
    e.preventDefault();
    let resp = await loadOrdersProgramming(); 
    
    sessionStorage.removeItem('minDate');
    if (resp) {
      toastr.error('Todos los pedidos se encuentran programados');
      return false;
    } 
    $('#selectNameProduct').empty();
    $('.cardCreateProgramming').toggle(800); 
    $('#btnCreateProgramming').html('Crear');
    $('#formCreateProgramming').trigger('reset'); 
  });

  /* Crear nuevo programa de produccion */
  $('#btnCreateProgramming').click(function (e) {
    e.preventDefault();
    let idProgramming = sessionStorage.getItem('id_programming'); 

    if (idProgramming == '' || idProgramming == null) {
      checkdataProgramming('/api/addProgramming', idProgramming);
    } else {
      checkdataProgramming('/api/updateProgramming', idProgramming);
    }
  });

  /* Actualizar programa de produccion */

  $(document).on('click', '.updateProgramming', async function (e) {
    $('.cardCreateProgramming').show(800); 
    $('#btnCreateProgramming').html('Actualizar');

    let row = $(this).parent().parent()[0];
    // i = row.rowIndex;
    let data = tblProgramming.fnGetData(row);

    sessionStorage.setItem('id_programming', data.id_programming);
    $('#order').empty();
    $('#order').append(`<option disabled>Seleccionar</option>`);
    $('#order').append(
      `<option value ='${data.id_order}' selected> ${data.num_order} </option>`
      );
      $('#selectNameProduct').empty();
      $('#selectNameProduct').append(`<option disabled>Seleccionar</option>`);
      $('#selectNameProduct').append(
        `<option value ='${data.id_product}' selected> ${data.product} </option>`
        );
    $('#quantityOrder').val(data.quantity_order.toLocaleString());
    
    // await loadProducts(data.num_order);

    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);

    $('#quantity').val(data.quantity_programming);

    $('#minDate').val(data.min_date);
    $('#maxDate').val(data.max_date);

    dataProgramming = new FormData(formCreateProgramming); 

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $(document).on('blur', '#quantity', function () {
    checkData();
  });

  calcMaxDate = async (min_date, last_hour, op) => {
    let id_order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());

    let order = await searchData(`/api/orders/${id_order}`);
    let planningMachine = await searchData(`/api/planningMachine/${machine}`);
    let ciclesMachine = await searchData(`/api/planCiclesMachine/${product}/${machine}`);
    
    if (op == 2) {
      min_date = `${min_date} ${planningMachine.hour_start}:00:00`;
    }
    
    let days = Math.trunc((order.original_quantity / ciclesMachine.cicles_hour / planningMachine.hours_day)) + 1;
    let final_date = new Date(min_date);
    
    final_date.setDate(final_date.getDate() + days);
    
    let max_hour = (order.original_quantity / ciclesMachine.cicles_hour) - (days * planningMachine.hours_day) + last_hour;
    final_date =
      final_date.getFullYear() + "-" +
      ("00" + (final_date.getMonth() + 1)).slice(-2) + "-" +
      ("00" + final_date.getDate()).slice(-2) + " " + max_hour + ':' + '00' + ':' + '00';
    dataProgramming.append('idProduct', product); 
    dataProgramming.append('idMachine', machine);
    dataProgramming.append('quantity', quantity);
    dataProgramming.append('minDate', min_date);
    dataProgramming.append('maxDate', final_date); 

    // max_date = new Date(final_date).toISOString().split('T')[0];
    // min_date = new Date(min_date).toISOString().split('T')[0];

    let maxDate = document.getElementById('maxDate');
    let minDate = document.getElementById('minDate');

    maxDate.value = final_date;
    minDate.value = min_date; 

  };

  /* Revision data programa de produccion */
  checkdataProgramming = async (url, idProgramming) => {  
    if (idProgramming)
      dataProgramming.append('idProgramming', idProgramming);
    
    $.ajax({
      type: "POST",
      url: url,
      data: dataProgramming,
      contentType: false,
      cache: false,
      processData: false,
      success: function (resp) {
        message(resp)
      }
    });
  };

  /* Eliminar programa de produccion */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProgramming.fnGetData(row);

    let dataProgramming = {};

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este programa de produccion? Esta acción no se puede reversar.',
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
          dataProgramming['idProgramming'] = data.id_programming;
          dataProgramming['order'] = data.id_order;
          dataProgramming['accumulatedQuantity'] = null;

          $.post(
            `/api/deleteProgramming`, dataProgramming,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  message = (data) => {
    if (data.success == true) {
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming').trigger('reset'); 
      $('#searchMachine option').removeAttr('selected');
      $(`#searchMachine option[value='0']`).prop('selected', true);

      loadTblProgramming(0);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  loadDataMachines(2);

});
