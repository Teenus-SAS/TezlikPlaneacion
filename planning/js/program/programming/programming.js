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
    
    if (resp) {
      toastr.error('Todos los pedidos se encuentran programados');
      return false;
    }

    $('.cardCreateProgramming').show(800);
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

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data programa de produccion */
  checkdataProgramming = async (url, idProgramming) => {
    let order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());
    
    let data = order * product * machine * quantity;
    
    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    // let quantityOrder = parseInt(getLastText(order));
    // order = parseInt(order);
    // let accumulated_quantity = 0;

    // if (quantity < quantityOrder)
    //   accumulated_quantity = quantityOrder - quantity;

    
    // dataProgramming.append('order', order);
    // dataProgramming.append('accumulatedQuantity', accumulated_quantity);
    
    dataProgramming = new FormData(formCreateProgramming);

    let machines = await searchData(`/api/programmingByMachine/${machine}/${product}`);

    if (machines == 1) {
      toastr.error('Ciclo de maquina no existe para ese producto');
      return false;
    }

    if (machines.length > 0) {
      if (idProgramming)
        dataProgramming.append('idProgramming', idProgramming);
      
      dataProgramming.append('minDate', machines[machines.length - 1].max_date);
              
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
    } else {
      bootbox.confirm({
        title: 'Ingrese Fecha De Inicio!',
        message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date"></input>
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
          if (result == true) {
            let date = $('#date').val();

            if (!date) {
              toastr.error('Ingrese los campos');
              return false;
            }

            dataProgramming.append('minDate', date);

            if (idProgramming != '' || idProgramming != null)
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
          }
        },
      });
    } 
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
