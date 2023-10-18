$(document).ready(function () {
  data = {};
  let allCiclesMachines = [];
  let allPlanningMachines = [];
  let allOrders = [];
  let allProgramming = [];

  loadAllDataProgramming = async () => {
    allCiclesMachines = await searchData('/api/planCiclesMachine');
    allPlanningMachines = await searchData('/api/planningMachines');
    allOrders = await searchData('/api/orders');
    allProgramming = await searchData('/api/programming');
  } 

  loadAllDataProgramming();

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
    $('#btnCreateProgramming').hide();
    $('.date').hide(); 

    $('#minDate').val('');
    $('#maxDate').val('');

    let order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());

    if (op == 1 || !isNaN(machine)) {
      // machines = await searchData(`/api/programmingByMachine/${machine}/${product}`);
      machines = false;

      for (let i = 0; i < allCiclesMachines.length; i++) {
        if (allCiclesMachines[i].id_machine == machine && allCiclesMachines[i].id_product == product) {
          machines = true;
          break;
        } 
      }
  
      if (machines == false) {
        toastr.error('Ciclo de maquina no existe para ese producto');
        return false;
      }
      machines = [];

      for (let i = 0; i < allProgramming.length; i++) {
        if (allProgramming[i].id_machine == machine)
          machines.push(allProgramming[i]);   
      }

      let planningMachine = false;

      for (let i = 0; i < allPlanningMachines.length; i++) {
        if (allPlanningMachines[i].id_machine == machine) {
          planningMachine = true;
          break;
        }
      }
  
      // planningMachine = await searchData(`/api/planningMachine/${machine}`);
  
      if (planningMachine == false) {
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
        } else {
          document.getElementById('minDate').readOnly = true;

          dataProgramming.append('minDate', date);
          calcMaxDate(date, 0, 2);
        }
      }
    }
  };

  calcMaxDate = async (min_date, last_hour, op) => {
    try {
      let id_order = parseFloat($('#order').val());
      let product = parseFloat($('#selectNameProduct').val());
      let machine = parseFloat($('#idMachine').val());
      let quantity = $('#quantity').val();

      for (let i = 0; i < allOrders.length; i++) {
        if (allOrders[i].id_order == id_order) {
          order = allOrders[i];
          break;
        }
      }

      for (let i = 0; i < allCiclesMachines.length; i++) {
        if (allCiclesMachines[i].id_machine == machine && allCiclesMachines[i].id_product == product) {
          ciclesMachine = allCiclesMachines[i];
          break;
        }
      }

      for (let i = 0; i < allPlanningMachines.length; i++) {
        if (allPlanningMachines[i].id_machine == machine) {
          planningMachine = allPlanningMachines[i];
          break;
        }
      }
    
      if (op == 2) {
        min_date = `${min_date} ${planningMachine.hour_start}:00:00`;
      }
    
      let days = Math.trunc((order.original_quantity / ciclesMachine.cicles_hour / planningMachine.hours_day)) + 1;
      let final_date = new Date(min_date);
    
      final_date.setDate(final_date.getDate() + days);
    
      let max_hour = (order.original_quantity / ciclesMachine.cicles_hour) - (days * planningMachine.hours_day) + last_hour;
    
      max_hour < 0 ? max_hour = max_hour * -1 : max_hour;

      final_date =
        final_date.getFullYear() + "-" +
        ("00" + (final_date.getMonth() + 1)).slice(-2) + "-" +
        ("00" + final_date.getDate()).slice(-2) + " " + max_hour + ':' + '00' + ':' + '00';
      dataProgramming.append('idProduct', product);
      dataProgramming.append('idMachine', machine);
      dataProgramming.append('quantity', quantity);
      dataProgramming.append('minDate', min_date);
      dataProgramming.append('maxDate', final_date);

      final_date = convetFormatDateTime(final_date);
      min_date = convetFormatDateTime(min_date);

      let maxDate = document.getElementById('maxDate');
      let minDate = document.getElementById('minDate');

      maxDate.value = final_date;
      minDate.value = min_date;

      $('.date').show(800);
      $('#btnCreateProgramming').show(800);
    } catch (error) {
      console.log(error);
    }
  };

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
