$(document).ready(function () {
  let programming = [];
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
      toastr.error('Sin pedidos para programar');
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
    $('#quantityMissing').val(data.accumulated_quantity.toLocaleString());
    
    // await loadProducts(data.num_order);
    let $select = $(`#idMachine`);
    $select.empty();
    $select.append(`<option value="0" disabled>Seleccionar</option>`);
     
    $select.append(`<option value ='${data.id_machine}' selected> ${data.machine} </option>`);
    // $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);

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
    sessionStorage.removeItem('minDate');
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

  /* Cambiar estado */
  $(document).on('click', '.changeStatus', function () {
    let row = $(this).parent().parent()[0];
    let data = tblProgramming.fnGetData(row);

    let dataProgramming = {};
    dataProgramming['idProgramming'] = data.id_programming;
    dataProgramming['idOrder'] = data.id_order;

    bootbox.confirm({
      title: 'Cambiar Estado',
      message:
        'Está seguro de cambiar estado este programa de produccion? Esta acción no se puede reversar.',
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
          $.post(`/api/changeStatusProgramming`, dataProgramming,
            function (data, textStatus, jqXHR) {
              message(data);
            },
          );
        }
      },
    });
  });

  $(document).on('click', '#btnChangeStatus', function () {
    $('#tblStatusProgramming').empty();
    
    let tblStatusProgramming = document.getElementById(
      'tblStatusProgramming'
    );

    tblStatusProgramming.insertAdjacentHTML('beforeend',
      `<thead>
        <th>No</th>
        <th>Pedido</th>
        <th>Referencia</th>
        <th>Producto</th>
        <th>Maquina</th>
        <th>Cant. Pedido</th>
        <th>Cant. Maquina</th>
        <th></th>
        </tr>
      </thead>
      <tbody id="tblStatusProgrammingBody"></tbody>`);
    
    setTblStatusProgramming();

  });

  // setTblStatusProgramming = () => {
  //   let data = copyAllProgramming;
    
  //   let tblStatusProgrammingBody = document.getElementById(
  //     'tblStatusProgrammingBody'
  //   );
      
  //   for (i = 0; i < data.length; i++) {
  //     programming.push({ idProgramming: data[i].id_programming });

  //     tblStatusProgrammingBody.insertAdjacentHTML(
  //       'beforeend',
  //       `
  //       <tr>
  //           <td>${i + 1}</td>
  //           <td>${data[i].num_order}</td>
  //           <td>${data[i].reference}</td>
  //           <td>${data[i].product}</td>
  //           <td>${data[i].machine}</td>
  //           <td>${data[i].quantity_order}</td>
  //           <td>${data[i].quantity_programming}</td>
  //           <td>
  //               <input type="checkbox" class="form-control-updated checkStatusProgramming" id="checkIn-${data[i].id_programming
  //       }" checked>
  //           </td>
  //       </tr>
  //     `
  //     );
  //   }

  //   $('#changeStatusProgramming').modal('show');

  //   $('#tblStatusProgramming').DataTable({
  //     destroy: true,
  //     scrollY: '150px',
  //     scrollCollapse: true,
  //     // language: {
  //     //   url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
  //     // },
  //     dom: '<"datatable-error-console">frtip',
  //     fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
  //       if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
  //         console.error(oSettings.json.error);
  //       }
  //     },
  //   });

  //   let tables = document.getElementsByClassName(
  //     'dataTables_scrollHeadInner'
  //   );

  //   let attr = tables[0];
  //   attr.style.width = '100%';
  //   attr = tables[0].firstElementChild;
  //   attr.style.width = '100%';
  // };

  // $(document).on('click', '.checkStatusProgramming', function () {
  //   let id = this.id;
  //   let idProgramming = id.slice(8, id.length); 

  //   if ($(`#${id}`).is(':checked')) {
  //     let data = {
  //       idProgramming: idProgramming,
  //     };

  //     programming.push(data);
  //   } else {
  //     for (i = 0; i < programming.length; i++)
  //       if (programming[i].idProgramming == idProgramming)
  //         programming.splice(i, 1);
  //   }
  // });

  $('#btnSaveProgramming').click(function (e) {
    e.preventDefault();
    
    if (programming.length == 0) {
      toastr.error('No hay ningún dato para guardar');
      return false;
    }

    $.ajax({
      type: "POST",
      url: '/api/changeStatusProgramming',
      data: { data: programming },
      success: function (resp) {
        programming = [];
        $('#changeStatusProgramming').modal('hide');

        message(resp);
      }
    });
  });

  $('.btnCloseStatusProgramming').click(function (e) {
    e.preventDefault();
    programming = [];
    $('#changeStatusProgramming').modal('hide');
    $('#tblStatusProgrammingBody').empty();
  });

  /* Mensaje de exito */
  message = async (data) => {
    try {
      if (data.success) {
        hideCardAndResetForm();
        toastr.success(data.message);
        await loadAllDataProgramming();
        loadTblProgramming(0);
      } else if (data.error) {
        toastr.error(data.message);
      } else if (data.info) {
        toastr.info(data.message);
      }
    } catch (error) {
      console.error('Error in message function:', error);
    }
  };

  // Función auxiliar para ocultar la tarjeta y reiniciar el formulario
  const hideCardAndResetForm = () => {
    $('.cardCreateProgramming').hide(800);
    $('#formCreateProgramming').trigger('reset');
    $('#searchMachine').val('0');
  };

  loadDataMachines(3);

});
