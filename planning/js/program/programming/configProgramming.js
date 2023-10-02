$(document).ready(function () {
  data = {};

  $(document).on('change', '#order', function (e) {
    e.preventDefault();

    let num_order = $('#order :selected').text().trim();
    loadProducts(num_order);
  });

  $(document).on('change', '#idMachine', function (e) {
    e.preventDefault();

    checkData(1);
  });

  checkData = async (op) => {
    let inputs = document.getElementsByClassName('input');
    let cont = 0;
    
    for (let i = 0; i < inputs.length; i++) {
      if (inputs[i].value == '' || inputs[i].value == '0')
        cont += 1;
    }
    $('#minDate').val('');
    $('#maxDate').val('');

    let order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());

    if (op == 1 || !isNaN(machine)) {
      machines = await searchData(`/api/programmingByMachine/${machine}/${product}`);
  
      if (machines == 1) {
        toastr.error('Ciclo de maquina no existe para ese producto');
        return false;
      }
  
      planningMachine = await searchData(`/api/planningMachine/${machine}`);
  
      if (!planningMachine) {
        toastr.error('Programacion de maquina no existe');
        return false;
      }
    }

    if (cont == 0) { 
      let data = order * product * machine * quantity;
    
      if (isNaN(data) || data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }; 

      if (machines.length > 0) {
        dataProgramming.append('minDate', machines[machines.length - 1].max_date);
        let hour = new Date(machines[machines.length - 1].max_date).getHours();
        calcMaxDate(machines[machines.length - 1].max_date, hour, 1);
      } else {
        let date = sessionStorage.getItem('minDate');

        if (!date) {
          $('.date').show(800);
          document.getElementById('minDate').readOnly = false;

          $('#minDate').blur(function (e) {
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
        
          // bootbox.confirm({
          //   title: 'Ingrese Fecha De Inicio!',
          //   message: `<div class="col-sm-12 floating-label enable-floating-label">
          //               <input class="form-control" type="date" name="date" id="date"></input>
          //               <label for="date">Fecha</span></label>
          //             </div>`,
          //   buttons: {
          //     confirm: {
          //       label: 'Agregar',
          //       className: 'btn-success',
          //     },
          //     cancel: {
          //       label: 'Cancelar',
          //       className: 'btn-danger',
          //     },
          //   },
          //   callback: function (result) {
          //     if (result == true) {
          //       let date = $('#date').val();

          //       if (!date) {
          //         toastr.error('Ingrese los campos');
          //         return false;
          //       }

          //       sessionStorage.setItem('minDate', date);
          //       dataProgramming.append('minDate', date);
          //       calcMaxDate(date, 0, 2);
          //     }
          //   },
          // });
        } else {
          document.getElementById('minDate').readOnly = true;

          dataProgramming.append('minDate', date);
          calcMaxDate(date, 0, 2);
        }
      }
    }
  };


  /* Cargar Pedidos y Productos 
  loadProductsAndOrders = (id_machine) => {
    data['idMachine'] = id_machine;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select = $(`#selectNameProduct`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
          $(`#selectNameProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
          // Obtener referencia producto
          $(`#refProduct option[value=${value.id_product}]`).prop(
            'selected',
            true
          );
        });

        let $select1 = $(`#order`);
        $select1.empty();

        $select1.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select1.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
          $(`#order option[value=${value.id_order}]`).prop('selected', true);
        });
      },
    });
    delete data.idMachine;
  }; */

  /* Cargar Maquinas y Pedidos 
  loadMachinesAndOrders = (id_product) => {
    data['idProduct'] = id_product;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select3 = $(`#idMachine`);
        $select3.empty();

        $select3.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select3.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
          );
          $(`#idMachine option[value=${value.id_machine}]`).prop(
            'selected',
            true
          );
        });

        let $select4 = $(`#order`);
        $select4.empty();

        $select4.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select4.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
          $(`#order option[value=${value.id_order}]`).prop('selected', true);
        });
      },
    });
    delete data.idProduct;
  }; */

  /* Cargar Productos y Maquinas */
  loadProducts = async (num_order) => {
    let r = await searchData(`/api/programming/${num_order}`);

    $('#quantityOrder').val('');
    
    let $select = $(`#selectNameProduct`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(r, function (i, value) {
      $select.append(
        `<option value ='${value.id_product}' class='${value.id_order}'> ${value.product} </option>`
      );
    });

    $('#selectNameProduct').change(function (e) { 
      e.preventDefault();

      for (let i = 0; i < r.length; i++) {
        if (this.value == r[i].id_product) {
          $('#quantityOrder').val(parseFloat(r[i].original_quantity).toLocaleString());

          dataProgramming = new FormData(formCreateProgramming);

          dataProgramming.append('order', r[i].id_order);
          
          break;
        } 
      }

      checkData(2); 
    });
      
  };
});
