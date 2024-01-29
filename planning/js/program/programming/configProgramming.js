$(document).ready(function () {
  data = {};
  allOrdersProgramming = [];
  allProcess = [];
  let allMachines = [];
  let allCiclesMachines = [];
  let allPlanningMachines = [];
  allOrders = [];
  allProgramming = [];
  allProductsMaterials = [];
  allTblData = [];
  let selectProduct = false;
  let selectProcess = false;

  loadAllDataProgramming = async (op, machine) => {
    try {
      const [
        ordersProgramming,
        process,
        machines,
        ciclesMachines,
        planningMachines,
        orders,
        programming,
        programmingMachines,
        productsMaterials
      ] = await Promise.all([
        op == 1 ? searchData('/api/ordersProgramming') : null,
        op == 1 ? searchData('/api/processOrder') : null,
        op == 1 ? searchData('/api/machines') : null,
        op == 1 ? searchData('/api/planCiclesMachine') : null,
        op == 1 ? searchData('/api/planningMachines') : null,
        op == 1 ? searchData('/api/orders') : null,
        op == 1 ? searchData('/api/programming') : null,
        op == 2 ? searchData(`/api/programmingByMachine/${machine}/0`) : null,
        op == 1 ? searchData('/api/allProductsMaterials') : null
      ]);
      let data = [];

      if (op == 1) {
        allOrdersProgramming = ordersProgramming;
        allProcess = process;
        allMachines = machines;
        allCiclesMachines = ciclesMachines;
        allPlanningMachines = planningMachines;
        allOrders = orders;
        allProducts = products;
        allProgramming = programming;
        copyAllProgramming = allProgramming;
        allProductsMaterials = productsMaterials;
        data = programming;
      } else {
        data = programmingMachines;
      }

      $('.cardBottons').show(800)

      loadOrdersProgramming(allOrdersProgramming);
      loadTblProgramming(data);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadOrdersProgramming = async (data) => {
    if (data.length === 0) {
      return 1;
    }

    data = data.reduce((acc, current) => {
      if (!acc.some(item => item.num_order === current.num_order)) {
        acc.push(current);
      }
      return acc;
    }, []);

    let $select = $(`#order`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);

    $.each(data, function (i, value) {
      $select.append(
        `<option value ='${value.id_order}'> ${value.num_order} </option>`
      );
    });
  };

  loadAllDataProgramming(1);

  $(document).on('change', '#order', function (e) {
    e.preventDefault();

    let value = this.value;

    let num_order = $('#order :selected').text().trim();
    $("#formCreateProgramming").trigger("reset");
    $(`#order option[value=${value}]`).prop('selected', true);
    $('#selectNameProduct').empty();
    $('#idProcess').empty();
    $('#idMachine').empty();
    selectProduct = false;
    
    loadProducts(num_order);
  });

  $(document).on('change', '.selects', function (e) {
    e.preventDefault();

    sessionStorage.removeItem('minDate');
    checkData(1, this.id);
  });

  $(document).on('click', '#minDate', function () {
    if (sessionStorage.getItem('minDate')) {
      $('#minDate').val('');
      $('#maxDate').val('');
      document.getElementById('minDate').readOnly = false;
      document.getElementById('minDate').type = 'date';
      $('#btnCreateProgramming').hide(800);
    }
  });

  checkData = async (op, id) => {
    let inputs = document.getElementsByClassName('input');
    let cont = 0;
    
    for (let i = 0; i < inputs.length; i++) {
      if (inputs[i].value == '' || inputs[i].value == '0')
        cont += 1;
    }

    $('#btnCreateProgramming').hide();
    
    $('#minDate').val('');
    $('#maxDate').val('');
    document.getElementById('minDate').readOnly = false;
    document.getElementById('minDate').type = 'date';
    $('.date').hide();

    let order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());

    if (op == 1 && !isNaN(machine)) {
      machines = [];

      for (let i = 0; i < allTblData.length; i++) {
        if (allTblData[i].id_machine == machine)
          machines.push(allTblData[i]);
      }

      let planningMachine = false;

      for (let i = 0; i < allPlanningMachines.length; i++) {
        if (allPlanningMachines[i].id_machine == machine) {
          planningMachine = true;
          break;
        }
      }
  
      if (planningMachine == false) {
        toastr.error('Programacion de maquina no existe');
        return false;
      }
    }

    if (cont == 0) {
      let productMaterial = false;
      
      for (let i = 0; i < allProductsMaterials.length; i++) {
        productMaterial = true;
        if (allProductsMaterials[i].id_product == product && allProductsMaterials[i].quantity <= 0) {
          productMaterial = false;
          break;
        }
      }

      if (productMaterial == false) {
        toastr.error('Materia prima no existente o sin cantidad disponible');
        return false;
      }

      if (id == 'quantity') {
        let productMaterial = true;

        for (let i = 0; i < allProductsMaterials.length; i++) {
          if (allProductsMaterials[i].id_product == product && allProductsMaterials[i].quantity < quantity) {
            productMaterial = false;
            break;
          }
        }

        if (productMaterial == false) {
          toastr.error('Materia prima sin cantidad disponible');
          return false;
        }
      }

      let data = order * product * machine * quantity;
    
      if (isNaN(data) || data <= 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      };

      if (machines.length > 0) { 
        dataProgramming['min_date'] = machines[machines.length - 1].max_date;

        let hour = new Date(machines[machines.length - 1].max_date).getHours();
        let min_date = machines[machines.length - 1].max_date;
        let date = new Date();
        let dateFormat = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;;

        if (min_date < dateFormat) {
          $('#minDate').val('');
          $('#maxDate').val('');
          document.getElementById('minDate').readOnly = false;
          document.getElementById('minDate').type = 'date';
          $('.date').show();
          return false;
        }

        sessionStorage.setItem('minDate', min_date);

        calcMaxDate(min_date, hour, 1);
      } else {
        let date = sessionStorage.getItem('minDate');

        if (!date) {
          $('.date').show(800);
          document.getElementById('minDate').readOnly = false;
          document.getElementById('minDate').type = 'date';
        } else {
          document.getElementById('minDate').readOnly = true;
 
          dataProgramming['min_date'] = date;
          calcMaxDate(date, 0, 2);
        }
      }
    }
  };

  $('#minDate').change(function (e) {
    e.preventDefault();

    let date = this.value;

    if (!date) {
      toastr.error('Ingrese fecha inicial');
      return false;
    }

    if (date.includes('T')) {
      date = date.split('T')[0];
    }

    let min_date = convetFormatDate(date);

    sessionStorage.setItem('minDate', min_date); 
    dataProgramming['min_date'] = min_date;
    calcMaxDate(min_date, 0, 2);
  });

  calcMaxDate = async (min_date, last_hour, op) => {
    try {
      let id_order = parseFloat($('#order').val());
      let product = parseFloat($('#selectNameProduct').val());
      let machine = parseFloat($('#idMachine').val());
      let quantity = parseInt($('#quantity').val());

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
        if (Number.isInteger(planningMachine.hour_start)) {
          min_date = new Date(`${min_date} ${planningMachine.hour_start}:00:00`);
        } else {
          const hoursInteger = Math.floor(planningMachine.hour_start);
          const minutes = Math.round((planningMachine.hour_start % 1) * 60);
          const formattedMinutes = minutes < 10 ? `0${minutes}` : `${minutes}`;
          min_date = new Date(min_date + 'T00:00:00');
          min_date.setHours(hoursInteger, formattedMinutes, 0);
        }
        min_date =
          min_date.getFullYear() + "-" +
          ("00" + (min_date.getMonth() + 1)).slice(-2) + "-" +
          ("00" + min_date.getDate()).slice(-2) + " " + ("00" + min_date.getHours()).slice(-2) + ':' + ("00" + min_date.getMinutes()).slice(-2) + ':' + '00';
      }
      let final_date = new Date(min_date);
    
      let days = (quantity / ciclesMachine.cicles_hour / planningMachine.hours_day);

      if (days >= 1) {
        final_date.setDate(final_date.getDate() + Math.floor(days));
      }
      let sobDays = (days % 1);
      let hours = sobDays * planningMachine.hours_day;
      
      let sobHours = (hours % 1);
      let minutes = sobHours * 60;
      let minutes1 = (Math.floor(hours) * 60) + parseInt(minutes);

      final_date.setMinutes(final_date.getMinutes() + Math.floor(minutes));
      final_date.setHours(final_date.getHours() + Math.floor(hours));
        
      final_date =
        final_date.getFullYear() + "-" +
        ("00" + (final_date.getMonth() + 1)).slice(-2) + "-" +
        ("00" + final_date.getDate()).slice(-2) + " " + ("00" + final_date.getHours()).slice(-2) + ':' + ("00" + final_date.getMinutes()).slice(-2) + ':' + '00';

      dataProgramming['id_product'] = product;
      dataProgramming['id_machine'] = machine;
      dataProgramming['quantity_programming'] = quantity;
      dataProgramming['min_date'] = min_date;
      dataProgramming['client'] = order.client;
      dataProgramming['max_date'] = final_date;
      dataProgramming['min_programming'] = minutes1;

      final_date = convetFormatDateTime(final_date);
      min_date = convetFormatDateTime(min_date);

      let maxDate = document.getElementById('maxDate');
      document.getElementById('minDate').type = 'datetime-local';
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
  const loadProducts = (num_order) => {
    let orders = allOrders.filter(item => item.num_order == num_order &&
      (item.status == 'Programar' || item.status == 'Programado') &&
      (item.accumulated_quantity_order == null || item.accumulated_quantity_order != 0)
    );

    $('#quantityOrder').val('');
    
    let $select = $(`#selectNameProduct`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(orders, function (i, value) {
      $select.append(
        `<option value ='${value.id_product}' class='${value.id_order}'> ${value.product} </option>`
      );
    });

    selectProduct = true;
  };

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();

    if (selectProduct == true) {
      let num_order = $('#order :selected').text().trim();
      let productOrders = allOrders.filter(item => item.num_order == num_order &&
        (item.status == 'Programar' || item.status == 'Programado') &&
        (item.accumulated_quantity_order == null || item.accumulated_quantity_order != 0)
      );

      let product = productOrders.find(item => item.id_product == this.value);

      dataProgramming = [];
      dataProgramming['reference'] = product.reference;
      dataProgramming['product'] = product.product;

      let id_product;
      let id_order = parseInt($(this).find('option:selected').attr('class'));

      for (let i = 0; i < productOrders.length; i++) {
        if (this.value == productOrders[i].id_product) {
          let process = allProcess.filter(item => item.id_product == this.value && item.id_order == id_order);
          id_product = this.value;
          
          let $select = $(`#idProcess`);
          $select.empty();
          $select.append(`<option value="0" disabled selected>Seleccionar</option>`);
          
          $.each(process, function (i, value) {
            $select.append(
              `<option class="${value.route1}" value ='${value.id_process}'> ${value.process} </option>`
            );
          });
          
          $('#quantityOrder').val(parseFloat(productOrders[i].original_quantity).toLocaleString());

          if (productOrders[i].accumulated_quantity_order == 0 || productOrders[i].accumulated_quantity_order == null)
            accumulated_quantity = parseFloat(productOrders[i].original_quantity).toLocaleString(); 
          else
            accumulated_quantity = parseFloat(productOrders[i].accumulated_quantity_order).toLocaleString(); 

          $('#quantityMissing').val(accumulated_quantity);

          let productsMaterials = allProductsMaterials.filter(item => item.id_product == this.value);
          productsMaterials = productsMaterials.sort((a, b) => a.quantity - b.quantity);
          $('#quantityMP').val(productsMaterials[0].quantity.toLocaleString('es-CO', { maximumfractiondigits: 2 }));

          dataProgramming['id_order'] = productOrders[i].id_order;
          dataProgramming['num_order'] = num_order;
          break;
        }
      }

      selectProcess = true;
      checkData(2, this.id); 
    }
  });

  $('#idProcess').change(function (e) {
    e.preventDefault();

    if (selectProcess == true) {
      // Obtener el classname de la opción seleccionada
      var route = parseInt($(this).find('option:selected').attr('class')); 
      dataProgramming['route'] = route + 1;
      let id_product = parseInt($('#selectNameProduct').val());

      let ciclesMachine = allCiclesMachines.filter(item => item.id_product == id_product && item.id_process == this.value && item.route == route);
            
      let $select = $(`#idMachine`);
      $select.empty();
      $select.append(`<option value="0" disabled selected>Seleccionar</option>`);
       
      $.each(ciclesMachine, function (i, value) {
        $select.append(
          `<option value = ${value.id_machine}> ${value.machine} </option>`
        );
      });
    }
  });
});
