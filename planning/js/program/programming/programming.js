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

    let cardCreateProgramming = $('.cardCreateProgramming').css('display');

    if (cardCreateProgramming == 'none') {

      let resp = await loadOrdersProgramming();
      
      if (resp) {
        toastr.error('Todos los pedidos se encuentran programados');
        return false;
      }

      let op = sessionStorage.getItem('opProgramming');

      if (op) {
        $('.cardCreateProgramming').show(800);
        $('#btnCreateProgramming').html('Crear');
        $('#formCreateProgramming').trigger('reset');
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

              sessionStorage.setItem('minDate', date);

              $('.cardCreateProgramming').show(800);
              $('#btnCreateProgramming').html('Crear');
              $('#formCreateProgramming').trigger('reset');
            }
          },
        });
      }
    } else {
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming').trigger('reset');
    }
  });

  /* Cargar datos generales 
  generalData = async () => {
    await $.ajax({
      url: '/api/generalData',
      success: function (r) {
        let $select = $(`#idMachine`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r.machines, function (i, value) {
          $select.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
          );
        });

        let $select1 = $(`#order`);
        $select1.empty();

        $select1.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r.orders, function (i, value) {
          $select1.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
        });

        let $select2 = $(`#refProduct`);
        $select2.empty();

        $select2.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r.products, function (i, value) {
          $select2.append(
            `<option value = ${value.id_product}> ${value.reference} </option>`
          );
        });

        let $select3 = $(`#selectNameProduct`);
        $select3.empty();

        $select3.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r.products, function (i, value) {
          $select3.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
        });
      },
    });
  };

  // Crear nueva programa de producción
  $('#btnCreateProgramming').click(function (e) {
    e.preventDefault();
    idMachine = parseInt($('#idMachine').val());
    idOrder = parseInt($('#order').val());
    idProduct = parseInt($('#selectNameProduct').val());
    quantity = $('#quantity').val();

    data = idMachine * idOrder * idProduct;

    if (!data || data == 0 || quantity == '') {
      toastr.error('Ingrese todos los campos');
      // generalData();
      return false;
    }
    programming = $('#formCreateProgramming').serialize();

    // Validar si existe tabla de programacion maquinas
    $.post('/api/dateMachine', programming, function (data) {
      if (data.error) {
        toastr.error(data.message);
        return false;
      } else {
        if (data.existing) {
          saveProgramming(programming);
          return false;
        }
        if (data.nonExisting) setStartDate(programming);
      }
    });
  });

  // Ingresar fecha de inicio
  setStartDate = (programming) => {
    bootbox.prompt({
      title: 'Programación',
      message: '<p>Ingrese fecha de inicio:</p>',
      inputType: 'date',
      callback: function (result) {
        if (result != null) {
          if (!result || result == '') {
            toastr.error('Ingrese fecha de inicio');
            return false;
          }
          programming = programming + `&startDate= ${result}`;
          saveProgramming(programming);
        }
      },
    });
  };

  // Guardar programa de producción a la tabla
  saveProgramming = (programming) => {
    machine = $('#idMachine').find('option:selected').text();
    numOrder = $('#order').find('option:selected').text();
    refProduct = $('#refProduct').find('option:selected').text();
    product = $('#selectNameProduct').find('option:selected').text();

    // Obtener información
    $.post(
      '/api/getProgrammingInfo',
      programming,
      function (data, textStatus, jqXHR) {
        $('.colProgramming').append(`
          <tr draggable="true" ondragstart="dragit(event)" ondragover="dragover(event)">
          <td>${numOrder}</td>
          <td>${refProduct}</td>
          <td>${product}</td>
          <td>${data.order.original_quantity}</td>
          <td>${data.order.quantity}</td>
          <td>${quantity}</td>
          <td>${data.order.client}</td>
          <td>${data.economicLot.toFixed(2)}</td>
          <td>${data.datesMachines.start_dat}</td>
          <td>${data.datesMachines.final_date}</td>
          </tr>`);

        message();
      }
    );
  };

  // Mensaje de exito
  message = () => {
    $('.cardCreateProgramming').hide(800);
    $('#formCreateProgramming').trigger('reset');
    toastr.success('Programación creada correctamente');
    return false;
  };

  */

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
    let data = tblProgramming.fnGetData(row);

    sessionStorage.setItem('id_programming', data.id_programming);
    $('#order').empty();
    $('#order').append(`<option disabled>Seleccionar</option>`);
    $('#order').append(
      `<option value ='${data.id_order} ${data.quantity_order}' selected> ${data.num_order} </option>`
    );
    
    await loadProducts(data.num_order);

    $(`#selectNameProduct option[value=${data.id_product}]`).prop('selected', true);

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
    
    let dataProgramming = new FormData(formCreateProgramming);
    let min_date = sessionStorage.getItem('minDate');

    if (min_date != '' || min_date != null)
      dataProgramming.append('minDate', min_date);

    if (idProgramming != '' || idProgramming != null)
      dataProgramming.append('idProgramming', idProgramming);

    let resp = await sendDataPOST(url, dataProgramming);

    message(resp);
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
