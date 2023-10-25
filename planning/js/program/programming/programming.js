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
    $('#btnCreateProgramming').hide();

    let resp = await loadOrdersProgramming(); 
    
    sessionStorage.removeItem('minDate');
    if (resp) {
      toastr.error('Todos los pedidos se encuentran programados');
      return false;
    } 
 
    $('.date').hide(); 
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

    document.getElementById('minDate').readOnly = false;
    $('.date').show(800);
    $('#btnCreateProgramming').show(800);

    max_date = convetFormatDateTime(data.max_date);
    min_date = convetFormatDateTime(data.min_date);

    $('#minDate').val(min_date);
    $('#maxDate').val(max_date);

    dataProgramming = new FormData(formCreateProgramming); 

    $(document).one('click', '#minDate', function (e) {
      e.preventDefault();
 
      document.getElementById('minDate').type = 'date';
    });

    $('#minDate').change(function (e) {
      e.preventDefault();

      if (!this.value) {
        toastr.error('Ingrese fecha inicial');
        return false;
      }

      let min_date = convetFormatDate(this.value);

      sessionStorage.setItem('minDate', min_date);
      dataProgramming.append('minDate', min_date);
      calcMaxDate(min_date, 0, 2);
    });

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $(document).on('blur', '#quantity', function () {
    checkData(2, this.id);
  });

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
  message = async (data) => {
    if (data.success == true) {
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming').trigger('reset'); 
      $('#searchMachine option').removeAttr('selected');
      $(`#searchMachine option[value='0']`).prop('selected', true);

      await loadAllDataProgramming();
      loadTblProgramming(0);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  loadDataMachines(3);

});
