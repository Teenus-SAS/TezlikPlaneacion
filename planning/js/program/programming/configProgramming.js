$(document).ready(function () {
  data = {};
  let allProcess = [];
  let allMachines = [];
  let allCiclesMachines = [];
  let allPlanningMachines = [];
  let allOrders = [];
  let allProgramming = []; 

  loadAllDataProgramming = async () => {
    try {
      const [
        process,
        machines,
        ciclesMachines,
        planningMachines,
        orders,
        programming,
        productsMaterials
      ] = await Promise.all([
        searchData('/api/process'),
        searchData('/api/machines'),
        searchData('/api/planCiclesMachine'),
        searchData('/api/planningMachines'),
        searchData('/api/orders'),
        searchData('/api/programming'),
        searchData('/api/allProductsMaterials')
      ]);

      allProcess = process;
      allMachines = machines;
      allCiclesMachines = ciclesMachines;
      allPlanningMachines = planningMachines;
      allOrders = orders;
      allProgramming = programming;
      copyAllProgramming = allProgramming;
      allProductsMaterials = productsMaterials;
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadAllDataProgramming();

  $(document).on('change', '#order', function (e) {
    e.preventDefault();

    let num_order = $('#order :selected').text().trim();
    loadProducts(num_order);
  });

  $(document).on('change', '#idMachine', function (e) {
    e.preventDefault();

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

    // if (quantity > quantityMissing) {
    //   toastr.error('');
    // }

    if (op == 1 || !isNaN(machine)) {
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
        dataProgramming.append('minDate', machines[machines.length - 1].max_date);
        let hour = new Date(machines[machines.length - 1].max_date).getHours();
        calcMaxDate(machines[machines.length - 1].max_date, hour, 1);
      } else {
        let date = sessionStorage.getItem('minDate');

        if (!date) {
          $('.date').show(800);
          document.getElementById('minDate').readOnly = false;
          document.getElementById('minDate').type = 'date';
          
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
        min_date = new Date(`${min_date} ${planningMachine.hour_start}:00:00`);
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

      let hours = (days % 1) * planningMachine.hours_day;
      
      let minutes = (hours % 1) * 60;
      final_date.setMinutes(final_date.getMinutes() + minutes);

      final_date.setHours(final_date.getHours() + hours);
        
      final_date =
        final_date.getFullYear() + "-" +
        ("00" + (final_date.getMonth() + 1)).slice(-2) + "-" +
        ("00" + final_date.getDate()).slice(-2) + " " + ("00" + final_date.getHours()).slice(-2) + ':' + ("00" + final_date.getMinutes()).slice(-2) + ':' + '00';

      dataProgramming.append('idProduct', product);
      dataProgramming.append('idMachine', machine);
      dataProgramming.append('quantity', quantity);
      dataProgramming.append('minDate', min_date);
      dataProgramming.append('maxDate', final_date);

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
          let ciclesMachine = allCiclesMachines.filter(item => item.id_product == this.value);
          let $select = $(`#idProcess`);
          $select.empty();
          $select.append(`<option value="0" disabled selected>Seleccionar</option>`);
     
          $.each(ciclesMachine, function (i, value) {
            $select.append(
              `<option value = ${value.id_process}> ${value.process} </option>`
            );
          });

          let $select1 = $(`#idMachine`);
          $select1.empty();
          $select1.append(`<option value="0" disabled selected>Seleccionar</option>`);
     
          $.each(ciclesMachine, function (i, value) {
            $select1.append(
              `<option value = ${value.id_machine}> ${value.machine} </option>`
            );
          });
          
          $('#quantityOrder').val(parseFloat(r[i].original_quantity).toLocaleString());
          $('#quantityMissing').val(parseFloat(r[i].accumulated_quantity) == 0 ? parseFloat(r[i].original_quantity).toLocaleString() : parseFloat(r[i].accumulated_quantity).toLocaleString());

          let productsMaterials = allProductsMaterials.filter(item => item.id_product == this.value);
          productsMaterials = productsMaterials.sort((a, b) => a.quantity - b.quantity);
          $('#quantityMP').val(productsMaterials[0].quantity.toLocaleString('es-CO', { maximumfractiondigits: 2 }));

          dataProgramming = new FormData(formCreateProgramming);

          dataProgramming.append('order', r[i].id_order);
          dataProgramming.append('num_order', num_order);
          break;
        }
      }

      checkData(2, this.id);
    });
  };
});
