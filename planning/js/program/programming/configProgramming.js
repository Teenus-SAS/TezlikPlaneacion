$(document).ready(function () {
  data = {};
  allOrdersProgramming = [];
  allProcess = [];
  let machines = [];
  allCiclesMachines = [];
  let allPlanningMachines = [];
  allOrders = [];
  allProgramming = [];
  allProductsMaterials = [];
  let allTblData = [];
  generalMultiArray = [];

  let selectProduct = false;
  let selectProcess = false;
  // sessionStorage.removeItem('dataProgramming');

  loadAllDataProgramming = async () => {
    try {
      const [
        ordersProgramming,
        process,
        machines,
        ciclesMachines,
        planningMachines,
        orders,
        programming,
        productsMaterials,
        compositeProducts
      ] = await Promise.all([
        searchData('/api/ordersProgramming'),
        searchData('/api/processProgramming'),
        searchData('/api/machines'),
        searchData('/api/planCiclesMachine'),
        searchData('/api/planningMachines'),
        searchData('/api/orders'),
        searchData('/api/programming'),
        searchData('/api/allProductsMaterials'),
        searchData('/api/allCompositeProducts'),
      ]);
      let data = [];
  
      allOrdersProgramming = ordersProgramming.map(item => ({ ...item, flag_tbl: 1 }));
      allProcess = process;
      allMachines = machines;
      allCiclesMachines = ciclesMachines;
      allPlanningMachines = planningMachines;
      allOrders = orders.map(item => ({ ...item, flag_tbl: 1, flag_process: 0 }));
      allProducts = products;
      allProgramming = programming;
      // copyAllProgramming = allProgramming;
      allProductsMaterials = [...productsMaterials, ...compositeProducts];
      data = programming;

      if (!sessionStorage.getItem('dataProgramming') || sessionStorage.getItem('dataProgramming').includes('[object Object]'))
      {// Crear el mapa único
        let uniquePCMap = new Map(ciclesMachines.map(item => [item.id_process, { [`process-${item.id_process}`]: [] }]));

        // Convertir el mapa en un array
        let uniqueArrayPC = Array.from(uniquePCMap.values());

        // Crear una copia profunda del array para 'sim_2'
        let uniqueArrayPC2 = uniqueArrayPC.map(item => JSON.parse(JSON.stringify(item)));

        // Agregar los arrays al multiarray
        generalMultiArray.push(
          {
            sim_1: uniqueArrayPC,
          },
          {
            sim_2: uniqueArrayPC2,
          }
        );
      }
      else {
        generalMultiArray = JSON.parse(sessionStorage.getItem('dataProgramming'));
      };

      allTblData = flattenData(generalMultiArray);
      
      $('.cardBottons').show(800); 
      checkProcessMachines(allTblData);
 
      allTblData = allTblData.concat(data);
      data = data.concat(allTblData);

      for (let i = 0; i < data.length; i++) {
        for (let j = 0; j < allTblData.length; j++) {
          if (data[i].id_programming === allTblData[j].id_programming) {
            let arr = data.filter(item => item.id_programming === data[i].id_programming);

            if (arr.length > 1)
              data.splice(i, 1);
          }
        }
      }

      let uniqueIdsSet = new Set(); // Conjunto para almacenar IDs únicas
      let uniqueArray = []; // Array para almacenar elementos únicos

      data.forEach(item => {
        // Verificamos si el ID ya existe en el conjunto
        if (!uniqueIdsSet.has(item.id_machine)) {
          uniqueIdsSet.add(item.id_machine); // Si no existe, lo agregamos al conjunto
          uniqueArray.push(item); // También agregamos el elemento al array de elementos únicos
        }
      });

      // Cargar selects de maquinas por pedidos programados
      loadDataMachinesProgramming(uniqueArray);
      // Cargar selects de pedidos que esten por programar
      loadOrdersProgramming(allOrdersProgramming);
      // Cargar DataTable con los pedidos programados
      loadTblProgramming(data, 1);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadDataMachinesProgramming = (data) => {
    let $select = $(`.idMachine`);
    $select.empty();
    $select.append(`<option value="0" disabled selected>Seleccionar</option>`);
    
    $select.append(`<option value="0">Todos</option>`);
    
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_machine}> ${value.machine} </option>`
      );
    });
  };

  loadOrdersProgramming = async (data) => {
    data = data.filter(item => item.flag_tbl == 1);

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

  loadAllDataProgramming();

  $(document).on('change', '#order', function (e) {
    e.preventDefault();

    let value = this.value;

    let num_order = $('#order :selected').text().trim();
    $("#formCreateProgramming").trigger("reset");
    $(`#order option[value=${value}]`).prop('selected', true);
    $('#refProduct').empty();
    $('#selectNameProduct').empty();
    $('#classification').empty();
    $('#idProcess').empty();
    $('#idMachine').empty();
    $('.cardFormProgramming2').hide(800);
    selectProduct = false;
    
    loadProducts(num_order);
  });

  $(document).on('change', '.selects', function (e) {
    e.preventDefault();

    sessionStorage.removeItem('minDate');
    checkData(1, this.id);
  });

  $(document).on("blur", "#quantity", function () {
    sessionStorage.removeItem("minDate");
    checkData(2, this.id);
  });

  $(document).on('click', '#minDate', function () {
    if (sessionStorage.getItem('minDate') && $('#btnCreateProgramming').html() == 'Crear') {
      $('#minDate').val('');
      $('#maxDate').val('');
      document.getElementById('minDate').readOnly = false;
      document.getElementById('minDate').type = 'date';
      $('#btnCreateProgramming').hide(800);
    }
  });

  const checkData = async (op, id) => {
    let inputs = document.getElementsByClassName('input');
    let cont = 0;
    
    for (let i = 0; i < inputs.length; i++) {
      if (inputs[i].value == '' || inputs[i].value == '0')
        cont += 1;
    }

    $('#btnCreateProgramming').hide();
    
    if (dataProgramming['update'] == 0) {
      $('#minDate').val('');
      $('#maxDate').val('');

      document.getElementById('minDate').readOnly = false;
      document.getElementById('minDate').type = 'date';
      $('.date').hide();
    }

    let order = parseFloat($('#order').val());
    let product = parseFloat($('#selectNameProduct').val());
    let machine = parseFloat($('#idMachine').val());
    let quantity = parseFloat($('#quantity').val());

    if (!isNaN(quantity)) {
      let productsMaterials = allProductsMaterials.filter(item => item.id_product == product);
      productsMaterials = productsMaterials.sort((a, b) => a.quantity - b.quantity);
        
      if (productsMaterials[0].quantity < quantity) {
        toastr.error('Cantidad a programar mayor a el inventario de MP');
        return false;
      }

      $('.cardFormProgramming2').show(800);
    }
    
    if (op == 1 && !isNaN(machine)) {
      machines = [];
      let allTblData = flattenData(generalMultiArray);

      for (let i = 0; i < allTblData.length; i++) {
        if (allTblData[i].id_machine == machine && allTblData[i].id_product == product)
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

    if (cont == 0 && !isNaN(machine)) {
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

        hour = new Date(machines[machines.length - 1].max_date).getHours();
        min_date = machines[machines.length - 1].max_date;
        let date = new Date();
        let dateFormat = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;

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

        if (dataProgramming['update'] == 1) {
          date = convetFormatDateTime1($('#minDate').val());

          dataProgramming['min_date'] = date;
          calcMaxDate(date, 0, 2);
        } else if (!date) {
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

  checkProcessMachines = (data) => {
    let conteoClaves = data.reduce((conteo, obj) => {
      conteo[obj.id_product] = (conteo[obj.id_product] || 0) + 1;
      return conteo;
    }, {});

    // Inicializar un array para almacenar los subarrays
    let subarrays = [];

    // Iterar sobre el objeto de conteo y dividir el array original
    for (let id_product in conteoClaves) {
      let subarray = data.filter(obj => obj.id_product == id_product);
      subarrays.push(subarray);
    }

    for (let i = 0; i < subarrays.length; i++) {
      let process = allProcess.filter(item => item.id_product == subarrays[i][0].id_product);
      process.sort((a, b) => b.route1 - a.route1);

      if (subarrays[i][subarrays[i].length - 1].route > process[0].route1) {
        allOrdersProgramming = allOrdersProgramming.filter(item => item.id_product != subarrays[i][0].id_product);
        allOrders = allOrders.filter(item => item.id_product != subarrays[i][0].id_product);
      }
    }
  }

  $('#minDate').change(function (e) {
    e.preventDefault();

    let date = this.value;

    if (!date) {
      toastr.error('Ingrese fecha inicial');
      return false;
    }

    if (date.includes('T')) {
      if (dataProgramming['update'] == 0) {
        date = date.split('T')[0];
        
        min_date = convetFormatDate(date);
      } else
        min_date = convetFormatDateTime1(date);
      
    } else
      min_date = convetFormatDate(date);

    sessionStorage.setItem('minDate', min_date);
    dataProgramming['min_date'] = min_date;
    calcMaxDate(min_date, 0, 2);
  })

  const calcMaxDate = async (min_date, last_hour, op) => {
    try {
      // let id_order = parseFloat($('#order').val());
      let num_order = $('#order :selected').text().trim();
      let product = parseFloat($('#selectNameProduct').val());
      let machine = parseFloat($('#idMachine').val());
      let quantity = parseInt($('#quantity').val());
      let order = allOrders.find(item => item.id_product == product && item.num_order == num_order);

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
        if (dataProgramming['update'] == 0) {
          if (Number.isInteger(planningMachine.hour_start)) {
            min_date = new Date(`${min_date} ${planningMachine.hour_start}:00:00`);
          } else {
            const hoursInteger = Math.floor(planningMachine.hour_start);
            const minutes = Math.round((planningMachine.hour_start % 1) * 60);
            const formattedMinutes = minutes < 10 ? `0${minutes}` : `${minutes}`;
            min_date = new Date(min_date + 'T00:00:00');
            min_date.setHours(hoursInteger, formattedMinutes, 0);
          }
        } else {
          min_date = new Date(min_date);
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
      let minutes1 = (Math.floor(days) * 1440) + (Math.floor(hours) * 60) + parseInt(minutes);

      final_date.setMinutes(final_date.getMinutes() + Math.floor(minutes));
      final_date.setHours(final_date.getHours() + Math.floor(hours));

      // Checkear si la hora de la fecha final calculada es mayor a la hora de finalizacion de la maquina
      let hour_check = parseFloat(`${final_date.getHours()}.${final_date.getMinutes()}`);
      
      if (hour_check > planningMachine.hour_end) {
        hours = Math.floor(planningMachine.hour_start);
        minutes = parseInt((planningMachine.hour_start).toFixed(2).toString().split(".")[1]);

        isNaN(minutes) ? minutes = 0 : minutes;

        final_date.setMinutes(Math.floor(minutes));
        final_date.setHours(Math.floor(hours));
        final_date.setDate(final_date.getDate() + 1);
      };
        
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
      (item.status == 'PROGRAMAR' || item.status == 'PROGRAMADO') &&
      ((item.accumulated_quantity_order == null || item.accumulated_quantity_order != 0) || item.flag_process == 0) &&
      item.flag_tbl == 1
    );

    $('#quantityOrder').val('');
    
    let $select = $(`#refProduct`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(orders, function (i, value) {
      $select.append(
        `<option value ='${value.id_product}'> ${value.reference} </option>`
      );
    });
    let $select1 = $(`#selectNameProduct`);
    $select1.empty();

    $select1.append(`<option disabled selected>Seleccionar</option>`);
    $.each(orders, function (i, value) {
      $select1.append(
        `<option value ='${value.id_product}'> ${value.product} </option>`
      );
    });

    selectProduct = true;
  };

  $('#refProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  $('#selectNameProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  $('.slctProduct').change(function (e) {
    e.preventDefault();

    if (selectProduct == true) {
      let num_order = $('#order :selected').text().trim();
      // let id_order = $('#order').val();
      productOrders = allOrders.filter(item => item.num_order == num_order &&
        (item.status == 'PROGRAMAR' || item.status == 'PROGRAMADO') &&
        ((item.accumulated_quantity_order == null || item.accumulated_quantity_order != 0) || item.flag_process == 0) &&
        item.flag_tbl == 1
      );
      
      let product = productOrders.find(item => item.id_product == this.value);

      if (product.classification === "A") badge = "badge-success";
      else if (product.classification === "B") badge = "badge-info";
      else badge = "badge-danger";

      $('#classification').html(`Clasificación<span class="badge ${badge}" style="font-size: large;">${product.classification}</span>`);

      dataProgramming = {};
      dataProgramming['reference'] = product.reference;
      dataProgramming['product'] = product.product;
      dataProgramming['update'] = 0;

      let id_product;

      for (let i = 0; i < productOrders.length; i++) {
        if (this.value == productOrders[i].id_product) {
          let process = allProcess.filter(item => item.id_product == this.value && item.num_order == num_order);
          process = process.find(item => item.route1 == process[0].route);
          id_product = this.value;
          
          let $select = $(`#idProcess`);
          $select.empty();
          $select.append(`<option value="0" disabled >Seleccionar</option>`);
           
          $select.append(
            `<option class="${process.route1}" value ='${process.id_process}'selected> ${process.process} </option>`
          );

          $('#quantityOrder').val(parseFloat(productOrders[i].original_quantity).toLocaleString());

          if (productOrders[i].accumulated_quantity == 0 || productOrders[i].accumulated_quantity == null)
            accumulated_quantity = parseFloat(productOrders[i].original_quantity).toLocaleString();
          else
            accumulated_quantity = parseFloat(productOrders[i].accumulated_quantity).toLocaleString();

          $('#quantityMissing').val(accumulated_quantity);

          let productsMaterials = allProductsMaterials.filter(item => item.id_product == this.value);
          productsMaterials = productsMaterials.sort((a, b) => a.quantity - b.quantity);
          // let quantity = parseFloat(productsMaterials[0].quantity) / parseFloat(productOrders[i].original_quantity);
          $('#quantityMP').val(Math.floor(productsMaterials[0].quantity).toLocaleString('es-CO', { maximumFractionDigits: 0 }));

          dataProgramming['id_order'] = productOrders[i].id_order;
          dataProgramming['num_order'] = num_order;

          break;
        }
      }
      
      selectProcess = true;
      checkData(2, this.id);
      $(`#idProcess`).change();
    }
  });

  $('#idProcess').change(function (e) {
    e.preventDefault();

    if (selectProcess == true) {
      // Obtener el classname de la opción seleccionada
      var route = parseInt($(this).find('option:selected').attr('class'));
      dataProgramming['route'] = route + 1;
      let id_product = parseInt($('#selectNameProduct').val());

      let arr = allCiclesMachines.find(item => item.id_product == id_product && item.id_process == this.value && item.route == route);
            
      let $select = $(`#idMachine`);
      $select.empty();
      $select.append(`<option disabled value=''>Seleccionar</option>`);
      // $select.append(`<option value="0"> PROCESO MANUAL </option>`);
       
      // $.each(ciclesMachine, function (i, value) {
      $select.append(`<option value ='${arr.id_machine}' selected> ${arr.machine} </option>`);
      // });
      $(`#idMachine`).change();
    }
  });

  $('#idMachine').change(function (e) {
    e.preventDefault();
    
    let allTblData = flattenData(generalMultiArray);

    if (allTblData.length > 0) {
      let id_product = $('#selectNameProduct').val();
      let machine = parseFloat($('#idMachine').val());
      let id_order = $('#order').val();
      // let num_order = $('#order :selected').text().trim();

      let data = allTblData.filter(item => item.id_product == id_product);

      if (data.length > 0) {
        data.sort((a, b) => a.id_machine - b.id_machine);

        if (data[data.length - 1].id_machine != this.value) {
          let arr = data.filter(item => item.id_machine == machine);
          let arrOM = arr.filter(item => item.id_order == id_order);
          let min_date, max_date;

          if (arr.length > 0 && arrOM.length > 0) {
            let date = allTblData[0].max_date;
            date = getFirstText(date);
            planningMachine = allPlanningMachines.find(item => item.id_machine == machine);
            max_date = `${date} ${planningMachine.hour_start < 10 ? `0${planningMachine.hour_start}` : planningMachine.hour_start}:00:00`;
          } else { 
            let minProgramming = data.reduce((total, arr) => total + arr.min_programming, 0);

            min_date = new Date(data[0].min_date);

            max_date = new Date(data[0].min_date);
            max_date.setMinutes(min_date.getMinutes() + Math.floor(minProgramming));

            max_date =
              max_date.getFullYear() + "-" +
              ("00" + (max_date.getMonth() + 1)).slice(-2) + "-" +
              ("00" + max_date.getDate()).slice(-2) + " " + ("00" + max_date.getHours()).slice(-2) + ':' + ("00" + max_date.getMinutes()).slice(-2) + ':' + '00';
          }
          dataProgramming['update'] = 1;
          document.getElementById('minDate').type = 'datetime-local';
          let minDate = document.getElementById('minDate');

          minDate.value = max_date;
        }
      } else {
        data = allTblData.filter(item => item.id_machine == machine);
        
        if (data.length > 0) { 
          let minProgramming = data.reduce((total, arr) => total + arr.min_programming, 0);

          min_date = new Date(data[0].min_date);

          max_date = new Date(data[0].min_date);
          max_date.setMinutes(min_date.getMinutes() + Math.floor(minProgramming));

          max_date =
            max_date.getFullYear() + "-" +
            ("00" + (max_date.getMonth() + 1)).slice(-2) + "-" +
            ("00" + max_date.getDate()).slice(-2) + " " + ("00" + max_date.getHours()).slice(-2) + ':' + ("00" + max_date.getMinutes()).slice(-2) + ':' + '00';
          dataProgramming['update'] = 1;
          document.getElementById('minDate').type = 'datetime-local';
          let minDate = document.getElementById('minDate');

          minDate.value = max_date;
        }
      }
    }
  });
});
