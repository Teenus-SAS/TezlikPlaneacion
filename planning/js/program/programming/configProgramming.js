$(document).ready(function () {
  data = {};
  allOrdersProgramming = [];
  allProcess = [];
  let machines = [];
  allCiclesMachines = [];
  allPlanningMachines = [];
  allOrders = [];
  allProgramming = [];
  allProductsMaterials = [];
  let allTblData = [];
  generalMultiArray = [];

  let selectProduct = false;
  let selectProcess = false;

  loadAllDataProgramming = async () => {
    try {
      // $('#btnNewProgramming').hide();

      // let storOrders = sessionStorage.getItem("allOrders");
      // let storOrdersProgramming = sessionStorage.getItem(
      //   "allOrdersProgramming"
      // );
      // let storProcess = sessionStorage.getItem("allProcess");
      // let storCiclesMachines = sessionStorage.getItem("allCiclesMachines");
      // let storPlanningMachines = sessionStorage.getItem("allPlanningMachines");
      // let storProductsMaterials = sessionStorage.getItem(
      //   "allProductsMaterials"
      // );

      // const [
      //   ordersProgramming,
      //   process,
      //   machines,
      //   ciclesMachines,
      //   planningMachines,
      //   orders,
      //   programming,
      //   productsMaterials,
      //   compositeProducts,
      // ] = await Promise.all([
      //   !storOrdersProgramming
      //     ? searchData("/api/ordersProgramming")
      //     : JSON.parse(storOrdersProgramming),
      //   !storProcess
      //     ? searchData("/api/processProgramming")
      //     : JSON.parse(storProcess),
      //   searchData("/api/machines"),
      //   !storCiclesMachines
      //     ? searchData("/api/planCiclesMachine")
      //     : JSON.parse(storCiclesMachines),
      //   !storPlanningMachines
      //     ? searchData("/api/planningMachines")
      //     : JSON.parse(storPlanningMachines),
      //   !storOrders ? searchData("/api/orders") : JSON.parse(storOrders),
      //   searchData("/api/programming"),
      //   !storProductsMaterials ? searchData("/api/allProductsMaterials") : [],
      //   !storProductsMaterials ? searchData("/api/allCompositeProducts") : [],
      // ]);
      
      // let data = [];

      // if (!storOrdersProgramming && !storOrders) {
      //   allOrdersProgramming = ordersProgramming.map((item) => ({
      //     ...item,
      //     flag_tbl: 1,
      //   }));
      //   allOrders = orders.map((item) => ({
      //     ...item,
      //     flag_tbl: 1,
      //     flag_process: 0,
      //   }));
      // }     

      // allProcess = process;
      // allMachines = machines;
      // allCiclesMachines = ciclesMachines;
      // allPlanningMachines = planningMachines;

      // allProducts = products;
      // allProgramming = programming;

      // if (!storProductsMaterials) {
      //   allProductsMaterials = [...productsMaterials, ...compositeProducts];
      // } else {
      //   allProductsMaterials = JSON.parse(storProductsMaterials);
      // }
 
      // Ocultar botón
      $('#btnNewProgramming').hide();

      // Obtener datos almacenados en sessionStorage
      const storData = {
        orders: sessionStorage.getItem("allOrders"),
        ordersProgramming: sessionStorage.getItem("allOrdersProgramming"),
        process: sessionStorage.getItem("allProcess"),
        ciclesMachines: sessionStorage.getItem("allCiclesMachines"),
        planningMachines: sessionStorage.getItem("allPlanningMachines"),
        productsMaterials: sessionStorage.getItem("allProductsMaterials"),
      };

      // Función para obtener datos de sessionStorage o realizar una solicitud API
      const getData = async (key, apiUrl) =>
        storData[key] ? JSON.parse(storData[key]) : await searchData(apiUrl);

      // Obtener todos los datos de forma concurrente
      const [
        ordersProgramming,
        process,
        machines,
        ciclesMachines,
        planningMachines,
        orders,
        programming,
        productsMaterials,
        compositeProducts,
      ] = await Promise.all([
        getData("ordersProgramming", "/api/ordersProgramming"),
        getData("process", "/api/processProgramming"),
        searchData("/api/machines"), // Siempre consulta la API
        getData("ciclesMachines", "/api/planCiclesMachine"),
        getData("planningMachines", "/api/planningMachines"),
        getData("orders", "/api/orders"),
        searchData("/api/programming"), // Siempre consulta la API
        getData("productsMaterials", "/api/allProductsMaterials"),
        getData("productsMaterials", "/api/allCompositeProducts"),
      ]);

      // Mapear y preparar datos
      if (!storOrdersProgramming && !storOrders) {
        allOrdersProgramming = ordersProgramming.map((item) => ({
          ...item,
          flag_tbl: 1,
        }));
        allOrders = orders.map((item) => ({
          ...item,
          flag_tbl: 1,
          flag_process: 0,
        }));
      } 
      
      allProductsMaterials = storData.productsMaterials
        ? JSON.parse(storData.productsMaterials)
        : [...productsMaterials, ...compositeProducts];

      // Asignar datos globales
      allProcess = process;
      allMachines = machines;
      allCiclesMachines = ciclesMachines;
      allPlanningMachines = planningMachines;
      allProgramming = programming;

      data = programming;

      if (
        !sessionStorage.getItem("dataProgramming") ||
        sessionStorage.getItem("dataProgramming").includes("[object Object]")
      ) {
        sessionStorage.setItem('allOrders', JSON.stringify(allOrders));
        sessionStorage.setItem('allOrdersProgramming', JSON.stringify(allOrdersProgramming));
        sessionStorage.setItem('allProcess', JSON.stringify(allProcess));
        sessionStorage.setItem('allCiclesMachines', JSON.stringify(allCiclesMachines));
        sessionStorage.setItem('allPlanningMachines', JSON.stringify(allPlanningMachines));
        sessionStorage.setItem('allProductsMaterials', JSON.stringify(allProductsMaterials));

        // Ordenar los ciclos de las máquinas por `id_process`
        ciclesMachines.sort((a, b) => a.id_process - b.id_process);

        // Crear el mapa único de procesos y máquinas
        const uniquePCMMap = new Map();

        ciclesMachines.forEach(({ id_process, id_machine }) => {
          // Obtener o inicializar el proceso en el mapa
          const processKey = `process-${id_process}`;
          const machineKey = `machine-${id_machine}`;

          if (!uniquePCMMap.has(id_process)) {
            uniquePCMMap.set(id_process, { [processKey]: { [machineKey]: [] } });
          } else {
            uniquePCMMap.get(id_process)[processKey][machineKey] = [];
          }
        });

        // Convertir el mapa en un array y duplicarlo para sim_2
        const uniqueArrayPCM = Array.from(uniquePCMMap.values());
        const uniqueArrayPCM2 = JSON.parse(JSON.stringify(uniqueArrayPCM));

        // Agregar las simulaciones al array principal
        generalMultiArray.push({ sim_1: uniqueArrayPCM }, { sim_2: uniqueArrayPCM2 });

      } else {
        // Recuperar datos de sessionStorage si ya existen
        generalMultiArray = JSON.parse(sessionStorage.getItem("dataProgramming"));
      }

      allTblData = flattenData(generalMultiArray);

      $(".cardBottons").show(800);
      if (flag_type_program == 0)
        checkProcessMachines(allTblData);

      allTblData = allTblData.concat(data);
      data = data.concat(allTblData);

      for (let i = 0; i < data.length; i++) {
        for (let j = 0; j < allTblData.length; j++) {
          if (data[i].id_programming == allTblData[j].id_programming) {
            let arr = data.filter(
              (item) => item.id_programming == data[i].id_programming
            );

            if (arr.length > 1) data.splice(i, 1);
          }
        }
      }

      let uniqueIdsSet = new Set(); // Conjunto para almacenar IDs únicas
      let uniqueArray = []; // Array para almacenar elementos únicos

      data.forEach((item) => {
        // Verificamos si el ID ya existe en el conjunto
        if (!uniqueIdsSet.has(item.id_machine)) {
          uniqueIdsSet.add(item.id_machine); // Si no existe, lo agregamos al conjunto
          uniqueArray.push(item); // También agregamos el elemento al array de elementos únicos
        }
      });

      // Cargar selects de maquinas por pedidos programados
      await loadDataMachinesProgramming(uniqueArray);
      // Cargar selects de pedidos que esten por programar
      await loadOrdersProgramming(allOrdersProgramming);
      // Cargar DataTable con los pedidos programados
      await loadTblProgramming(data, 1);

      $('#btnNewProgramming').show();
    } catch (error) {
      console.error("Error loading data:", error);
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
    // Filtrar datos por `flag_tbl`
    let filteredData = data
      .filter(item => item.flag_tbl == 1)
      .reduce((acc, current) => {
        // Solo agregar órdenes únicas por `num_order`
        if (!acc.find(item => item.num_order == current.num_order)) {
          acc.push(current);
        }
        return acc;
      }, []);

    if (filteredData.length == 0) return 1;

    const $select = $('#order');
    $select.empty().append('<option disabled selected>Seleccionar</option>');

    // Crear las opciones del select
    filteredData.forEach(({ id_order, num_order }) => {
      $select.append(`<option value="${id_order}">${num_order}</option>`);
    });
  }; 

  loadAllDataProgramming();

  $(document).on("change", "#order", function (e) {
    e.preventDefault();

    let value = this.value;

    let num_order = $("#order :selected").text().trim();
    $("#formCreateProgramming").trigger("reset");
    $(`#order option[value=${value}]`).prop("selected", true);
    $("#refProduct").empty();
    $("#selectNameProduct").empty();
    $("#classification").empty();
    $("#idProcess").empty();
    $("#idMachine").empty();
    $(".cardFormProgramming2").hide(800);
    selectProduct = false;

    loadProducts(num_order);
  });

  $(document).on("change", ".selects", function (e) {
    e.preventDefault();

    sessionStorage.removeItem("minDate");
    checkData(1, this.id);
  });

  $(document).on("blur", "#quantity", function () {
    sessionStorage.removeItem("minDate");
    checkData(2, this.id);
  });

  if (flag_type_program == 0) {
    $(document).on("click", "#minDate", function () {
      if (
        sessionStorage.getItem("minDate") &&
        $("#btnCreateProgramming").html() == "Crear"
      ) {
        $("#minDate").val("");
        $("#maxDate").val("");
        document.getElementById("minDate").readOnly = false;
        document.getElementById("minDate").type = "date";
        $("#btnCreateProgramming").hide(800);
      }
    });
  }

  const checkData = async (op, id) => {
    const inputs = document.getElementsByClassName("input");
    let cont = Array.from(inputs).filter(input => input.value == "" || input.value == 0).length;

    $("#btnCreateProgramming").hide();

    if (cont < 5) {
      if (dataProgramming.update == 0) {
        $("#minDate, #maxDate").val("").prop("readOnly", false);
        $("#minDate").prop("type", "date");
        $(".date").hide();
      }

      const order = parseFloat($("#order").val());
      const product = parseFloat($("#selectNameProduct").val());
      const machine = parseFloat($("#idMachine").val());
      const quantity = parseFloat($("#quantity").val());

      if (!isNaN(quantity)) {
        const productsMaterials = allProductsMaterials
          .filter(item => item.id_product == product)
          .sort((a, b) => a.quantity - b.quantity);

        if (productsMaterials.length && productsMaterials[0].quantity < quantity) {
          toastr.error("Cantidad a programar mayor a el inventario de MP");
          return false;
        }

        $(".cardFormProgramming2").show(800);
      }

      if (op == 1 && !isNaN(machine)) {
        machines = flattenData(generalMultiArray).filter(
          item => item.id_machine == machine && item.id_product == product
        );

        const planningMachine = allPlanningMachines.some(
          item => item.id_machine == machine
        );

        if (!planningMachine) {
          toastr.error("Programación de máquina no existe");
          return false;
        }
      }

      if (cont == 0 && !isNaN(machine)) {
        let productMaterial = allProductsMaterials.some(
          item => item.id_product == product && item.quantity > 0
        );

        if (!productMaterial) {
          toastr.error("Materia prima no existente o sin cantidad disponible");
          return false;
        }

        if (id == "quantity" && allProductsMaterials.some(
          item => item.id_product == product && item.quantity < quantity
        )) {
          toastr.error("Materia prima sin cantidad disponible");
          return false;
        }

        const data = order * product * machine * quantity;
        if (isNaN(data) || data <= 0) {
          toastr.error("Ingrese todos los campos");
          return false;
        }

        if (flag_type_program == 0) {
          if (machines.length > 0) {
            dataProgramming.min_date = machines.at(-1).max_date;
            const hour = new Date(dataProgramming.min_date).getHours();
            const minDate = dataProgramming.min_date;
            const today = new Date().toISOString().split("T")[0];

            if (minDate < today) {
              $("#minDate, #maxDate").val("").prop("readOnly", false).prop("type", "date");
              $(".date").show();
              return false;
            }

            sessionStorage.setItem("minDate", minDate);
            calcMaxDate(minDate, hour, 1);
          } else {
            const storedMinDate = sessionStorage.getItem("minDate");

            if (dataProgramming.update == 1) {
              const newDate = convetFormatDateTime1($("#minDate").val());
              dataProgramming.min_date = newDate;
              calcMaxDate(newDate, 0, 2);
            } else if (!storedMinDate) {
              $(".date").show(800);
              $("#minDate").prop("readOnly", false).prop("type", "date");
            } else {
              $("#minDate").prop("readOnly", true);
              dataProgramming.min_date = storedMinDate;
              calcMaxDate(storedMinDate, 0, 2);
            }
          }
        }
      }

      if (flag_type_program == 1 && cont == 0) {
        $("#btnCreateProgramming").show();
      } 
    }
  };
  
  if (flag_type_program == 0) {
    checkProcessMachines = (data) => {
      // Conteo de productos basado en id_product
      const conteoClaves = data.reduce((conteo, obj) => {
        conteo[obj.id_product] = (conteo[obj.id_product] || 0) + 1;
        return conteo;
      }, {});

      // Crear subarrays de productos
      const subarrays = Object.keys(conteoClaves).map(id_product =>
        data.filter(obj => obj.id_product == id_product)
      );

      // Validar y filtrar órdenes según el proceso y ruta
      subarrays.forEach(subarray => {
        const process = allProcess
          .filter(item => item.id_product == subarray[0].id_product)
          .sort((a, b) => b.route1 - a.route1);

        if (subarray.at(-1).route > process[0]?.route1) {
          const { id_product } = subarray[0];
          allOrdersProgramming = allOrdersProgramming.filter(
            item => item.id_product != id_product
          );
          allOrders = allOrders.filter(
            item => item.id_product != id_product
          );
        }
      });
    };

    $("#minDate").change(function (e) {
      e.preventDefault();

      let date = this.value;
      if (!date) {
        toastr.error("Ingrese fecha inicial");
        return;
      }

      // Formatear la fecha según si contiene "T" y el valor de dataProgramming["update"]
      min_date = date.includes("T")
        ? dataProgramming["update"] == 0
          ? convetFormatDate(date.split("T")[0])
          : convetFormatDateTime1(date)
        : convetFormatDate(date);

      // Guardar la fecha y calcular la fecha máxima
      sessionStorage.setItem("minDate", min_date);
      dataProgramming["min_date"] = min_date;
      calcMaxDate(min_date, 0, 2);
    });

    calcMaxDate = async (min_date, last_hour, op) => {
      try {
        let num_order = $("#order :selected").text().trim();
        let product = parseFloat($("#selectNameProduct").val());
        let machine = parseFloat($("#idMachine").val());
        let quantity = parseInt($("#quantity").val());
        let order = allOrders.find(
          (item) => item.id_product == product && item.num_order == num_order
        );

        for (let i = 0; i < allCiclesMachines.length; i++) {
          if (
            allCiclesMachines[i].id_machine == machine &&
            allCiclesMachines[i].id_product == product
          ) {
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
          if (dataProgramming["update"] == 0) {
            if (Number.isInteger(planningMachine.hour_start)) {
              min_date = new Date(
                `${min_date} ${planningMachine.hour_start}:00:00`
              );
            } else {
              const hoursInteger = Math.floor(planningMachine.hour_start);
              const minutes = Math.round((planningMachine.hour_start % 1) * 60);
              const formattedMinutes =
                minutes < 10 ? `0${minutes}` : `${minutes}`;
              min_date = new Date(min_date + "T00:00:00");
              min_date.setHours(hoursInteger, formattedMinutes, 0);
            }
          } else {
            min_date = new Date(min_date);
          }

          min_date =
            min_date.getFullYear() +
            "-" +
            ("00" + (min_date.getMonth() + 1)).slice(-2) +
            "-" +
            ("00" + min_date.getDate()).slice(-2) +
            " " +
            ("00" + min_date.getHours()).slice(-2) +
            ":" +
            ("00" + min_date.getMinutes()).slice(-2) +
            ":" +
            "00";
        }

        let final_date = new Date(min_date);

        let days =
          quantity / ciclesMachine.cicles_hour / planningMachine.hours_day;

        if (days >= 1) {
          final_date.setDate(final_date.getDate() + Math.floor(days));
        }

        let sobDays = days % 1;
        let hours = sobDays * planningMachine.hours_day;

        let sobHours = hours % 1;
        let minutes = sobHours * 60;
        let minutes1 =
          Math.floor(days) * 1440 + Math.floor(hours) * 60 + parseInt(minutes);

        final_date.setMinutes(final_date.getMinutes() + Math.floor(minutes));
        final_date.setHours(final_date.getHours() + Math.floor(hours));

        // Checkear si la hora de la fecha final calculada es mayor a la hora de finalizacion de la maquina
        let hour_check = parseFloat(
          `${final_date.getHours()}.${final_date.getMinutes()}`
        );

        if (hour_check > planningMachine.hour_end || hour_check < 6) {
          hours = Math.floor(planningMachine.hour_start);
          minutes = parseInt(
            planningMachine.hour_start.toFixed(2).toString().split(".")[1]
          );

          isNaN(minutes) ? (minutes = 0) : minutes;

          final_date.setMinutes(Math.floor(minutes));
          final_date.setHours(Math.floor(hours));
          final_date.setDate(final_date.getDate() + 1);
        }

        final_date =
          final_date.getFullYear() +
          "-" +
          ("00" + (final_date.getMonth() + 1)).slice(-2) +
          "-" +
          ("00" + final_date.getDate()).slice(-2) +
          " " +
          ("00" + final_date.getHours()).slice(-2) +
          ":" +
          ("00" + final_date.getMinutes()).slice(-2) +
          ":" +
          "00";

        dataProgramming["id_product"] = product;
        dataProgramming["id_machine"] = machine;
        dataProgramming["quantity_programming"] = quantity;
        dataProgramming["min_date"] = min_date;
        dataProgramming["client"] = order.client;
        dataProgramming["max_date"] = final_date;
        dataProgramming["min_programming"] = minutes1;

        final_date = convetFormatDateTime(final_date);
        min_date = convetFormatDateTime(min_date);

        let maxDate = document.getElementById("maxDate");
        document.getElementById("minDate").type = "datetime-local";
        let minDate = document.getElementById("minDate");

        maxDate.value = final_date;
        minDate.value = min_date;

        $(".date").show(800);
        $("#btnCreateProgramming").show(800);
      } catch (error) {
        console.log(error);
      }
    }; 
  }
      
  const loadProducts = (num_order) => {
    const orders = allOrders.filter(item =>
      item.num_order == num_order &&
      ["PROGRAMAR", "PROGRAMADO"].includes(item.status) &&
      (item.accumulated_quantity_order == null || item.accumulated_quantity_order !== 0 || item.flag_process == 0) &&
      item.flag_tbl == 1
    );

    $("#quantityOrder").val("");

    const createOptions = (selector, key) => {
      const $select = $(selector);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      orders.forEach(order => {
        $select.append(`<option value='${order.id_product}'>${order[key]}</option>`);
      });
    };

    createOptions("#refProduct", "reference");
    createOptions("#selectNameProduct", "product");

    selectProduct = true;
  };

  $("#refProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#selectNameProduct option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $("#selectNameProduct").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $("#refProduct option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $(".slctProduct").change(function (e) {
    e.preventDefault();

    if (!selectProduct) return;

    const num_order = $("#order :selected").text().trim();
    const productOrders = allOrders.filter(item =>
      item.num_order == num_order &&
      ["PROGRAMAR", "PROGRAMADO"].includes(item.status) &&
      (item.accumulated_quantity_order == null ||
        item.accumulated_quantity_order !== 0 ||
        item.flag_process == 0) &&
      item.flag_tbl == 1
    );

    const product = productOrders.find(item => item.id_product == this.value);

    const badge = product.classification == "A"
      ? "badge-success"
      : product.classification == "B"
        ? "badge-info"
        : "badge-danger";

    $("#classification").html(
      `Clasificación<span class="badge ${badge}" style="font-size: large;">${product.classification}</span>`
    );

    // Creación de datos de programación general
    dataProgramming = {
      reference: product.reference,
      product: product.product,
      update: 0
    };

    for (const order of productOrders) {
      if (this.value != order.id_product) continue;

      // Filtrar procesos de producto y ordenarlos por la siguiente ruta
      const process = allProcess
        .filter(item => item.id_product == this.value && item.num_order == num_order)
        .sort((a, b) => a.route1 - b.route1)[0];

      const $select = $("#idProcess");
      $select.empty().append(`<option value="0" disabled>Seleccionar</option>`);
      $select.append(
        `<option class="${process.route1}" value ='${process.id_process}' selected>${process.process}</option>`
      );

      $("#client").val(order.client);
      $("#quantityOrder").val(parseFloat(order.original_quantity).toLocaleString());
      const accumulated_quantity = parseFloat(order.accumulated_quantity ?? order.original_quantity).toLocaleString();
      $("#quantityMissing").val(accumulated_quantity);

      // Filtrar y ordenar FTMP por producto
      const productsMaterials = allProductsMaterials
        .filter(item => item.id_product == this.value)
        .sort((a, b) => a.quantity - b.quantity);
        
      $("#quantityMP").html(
        Math.floor(productsMaterials[0].quantity).toLocaleString("es-CO", {
          maximumFractionDigits: 0
        })
      );

      dataProgramming["id_order"] = order.id_order;
      dataProgramming["num_order"] = num_order;

      break;
    }

    selectProcess = true;
    checkData(2, this.id);
    $("#idProcess").change();
  });

  $("#idProcess").change(function (e) {
    e.preventDefault();

    if (!selectProcess) return;

    // Obtener la clase de la opción seleccionada
    const route = parseInt($(this).find("option:selected").attr("class"));
    dataProgramming["route"] = route + 1;

    const id_product = parseInt($("#selectNameProduct").val());

    // Buscar el ciclo correspondiente de la máquina
    const arr = allCiclesMachines.find(
      item => item.id_product == id_product && item.id_process == this.value && item.route == route
    );

    // Vaciar y agregar nuevas opciones en el select de máquinas
    const $select = $("#idMachine");
    $select.empty().append("<option disabled value=''>Seleccionar</option>");

    const machineOption = arr.status == 0
      ? `<option value ='${arr.id_alternal_machine}' selected> ${arr.alternal_machine} </option>`
      : `<option value ='${arr.id_machine}' selected> ${arr.machine} </option>`;

    $select.append(machineOption);

    // Activar el evento de cambio para el select de máquinas
    $(`#idMachine`).change();
  });

  if (flag_type_program == 0) {
    $("#idMachine").change(function (e) {
      e.preventDefault();

      let allTblData = flattenData(generalMultiArray);

      if (allTblData.length > 0) {
        let id_product = $("#selectNameProduct").val();
        let machine = parseFloat($("#idMachine").val());
        let id_order = $("#order").val();

        let data = allTblData.filter((item) => item.id_product == id_product);

        if (data.length > 0) {
          data.sort((a, b) => a.id_machine - b.id_machine);
 
          let arr = data.filter((item) => item.id_machine == machine);
          let arrOM = arr.filter((item) => item.id_order == id_order);
          let min_date, max_date;

          if (arr.length > 0 && arrOM.length > 0) {
            let date = allTblData[0].max_date;
            date = getFirstText(date);
            planningMachine = allPlanningMachines.find(
              (item) => item.id_machine == machine
            );

           let hour_start = moment(planningMachine.hour_start.toFixed(2), "HH:mm").format("h:mm A");
            max_date = `${date} ${hour_start}`;
          } else {
            let minProgramming = data.reduce(
              (total, arr) => total + arr.min_programming,
              0
            );

            min_date = new Date(data[0].min_date);

            max_date = new Date(data[0].min_date);
            max_date.setMinutes(
              min_date.getMinutes() + Math.floor(minProgramming)
            );

            max_date =
              max_date.getFullYear() +
              "-" +
              ("00" + (max_date.getMonth() + 1)).slice(-2) +
              "-" +
              ("00" + max_date.getDate()).slice(-2) +
              " " +
              ("00" + max_date.getHours()).slice(-2) +
              ":" +
              ("00" + max_date.getMinutes()).slice(-2) +
              ":" +
              "00";
          }
          dataProgramming["update"] = 1;
          document.getElementById("minDate").type = "datetime-local";
          let minDate = document.getElementById("minDate");

          minDate.value = max_date;
        } else {
          data = allTblData.filter((item) => item.id_machine == machine);

          if (data.length > 0) {
            let minProgramming = data.reduce(
              (total, arr) => total + arr.min_programming,
              0
            );

            min_date = new Date(data[0].min_date);

            max_date = new Date(data[0].min_date);
            max_date.setMinutes(
              min_date.getMinutes() + Math.floor(minProgramming)
            );

            max_date =
              max_date.getFullYear() +
              "-" +
              ("00" + (max_date.getMonth() + 1)).slice(-2) +
              "-" +
              ("00" + max_date.getDate()).slice(-2) +
              " " +
              ("00" + max_date.getHours()).slice(-2) +
              ":" +
              ("00" + max_date.getMinutes()).slice(-2) +
              ":" +
              "00";
            dataProgramming["update"] = 1;
            document.getElementById("minDate").type = "datetime-local";
            let minDate = document.getElementById("minDate");

            minDate.value = max_date;
          }
        }
      }
    });
  }

  // Mostrar MP Disponible
  $("#quantityMP").click(function (e) {
    e.preventDefault();

    let id_product = $("#selectNameProduct").val();

    if (id_product) {
      let productsMaterials = allProductsMaterials.filter(
        (item) => item.id_product == id_product
      );
      productsMaterials = productsMaterials.sort(
        (a, b) => a.quantity - b.quantity
      );

      // Mostramos el mensaje con Bootbox
      bootbox.alert({
        title: "Materia Prima",
        message: `
            <div class="container">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="fixed-table-loading table table-hover">
                    <thead>
                      <tr>
                        <th class="text-center">Referencia</th>
                        <th class="text-center">Descripción</th>
                        <th class="text-center">Cantidad Disponible</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center">${productsMaterials[0].reference_material
          }</td>
                        <td class="text-center">${productsMaterials[0].material
          }</td>
                        <td class="text-center">${Math.floor(
            productsMaterials[0].quantity
          ).toLocaleString("es-CO", {
            maximumFractionDigits: 0,
          })} ${productsMaterials[0].abbreviation}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div> 
            </div>`,
        size: "large",
        backdrop: true,
      });
      return false;
    }
  });
});
