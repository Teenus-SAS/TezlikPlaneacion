$(document).ready(function () {  
  $(".selectNavigation").click(function (e) {
    e.preventDefault();
    $(
      ".cardProgramming, .cardDashboardProgramming, .cardCreateProgramming"
    ).hide();

    const option = this.id;

    const sections = {
      "link-table": ".cardProgramming",
      "link-dashboard": ".cardDashboardProgramming",
    };

    // Mostrar la sección correspondiente según la opción seleccionada
    $(sections[option] || "").show();
  });

  let processProgramming = [];
  /* Ocultar panel crear programa de producción */
  $(
    ".cardCreateProgramming, .cardFormProgramming2, .cardBottons, .cardSaveBottons"
  ).hide();

  /* Abrir panel crear programa de producción */

  $("#btnNewProgramming").click(async function (e) {
    e.preventDefault();
    $("#btnCreateProgramming").hide();

    let resp = await loadOrdersProgramming(allOrdersProgramming);

    sessionStorage.removeItem("minDate");
    sessionStorage.removeItem("id_programming");

    if (resp == 1) {
      toastr.error("Sin pedidos para programar");
      return false;
    }

    $('#quantityMP').html('');
    $(".date, .cardFormProgramming2").hide();
    $("#refProduct, #selectNameProduct, #idMachine, #idProcess").empty();
    $(".cardCreateProgramming").toggle(800);
    $("#btnCreateProgramming").text("Crear");
    $("#formCreateProgramming").trigger("reset");
  });

  //Limpiar value MP Disponible
  $("#order").change(function (e) {
    e.preventDefault();
    $("#quantityMP").text("");
  });

  /* Crear nuevo programa de produccion */
  $("#btnCreateProgramming").click(function (e) {
    e.preventDefault();

    let id_order = parseInt($("#order").val());
    let id_product = parseInt($("#selectNameProduct").val());
    let quantityProgramming = parseInt($("#quantity").val());
    let id_process = parseInt($("#idProcess").val());
    let id_machine = parseInt($("#idMachine").val());
    let min_date = $("#minDate").val();
    let max_date = $("#maxDate").val();

    let checkDT1 =
      id_order * id_product * quantityProgramming * id_process * id_machine;

      if (min_date == "" || max_date == "") {
        $(".cardFormProgramming2").show(800);
      }
      
    if (flag_type_program == 0) {
      if (max_date == "") {
        toastr.error("Ingrese todos los campos");
        return false;
      }
    }

    if (isNaN(checkDT1) || checkDT1 <= 0 || min_date == "") {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let idProgramming = sessionStorage.getItem("id_programming");
    let allTblData = flattenData(generalMultiArray);

    if (idProgramming == "" || idProgramming == null) {
      dataProgramming["id_programming"] = allTblData.length;
      dataProgramming["bd_status"] = 0;
      checkdataProgramming(idProgramming);
    } else {
      dataProgramming["id_programming"] = idProgramming;
      checkdataProgramming(idProgramming);
    }
  });

  /* Actualizar programa de produccion 
  $(document).on("click", ".updateProgramming", async function (e) {
    $(".cardCreateProgramming").show(800);
    $('.cardFormProgramming2').show(800);
    $("#btnCreateProgramming").text("Actualizar");
    let allTblData = flattenData(generalMultiArray);

    let data = allTblData.find((item) => item.id_programming == this.id);
    sessionStorage.removeItem("minDate");

    sessionStorage.setItem("id_programming", data.id_programming);
    $("#order").empty();
    $("#order").append(`<option disabled>Seleccionar</option>`);
    $("#order").append(
      `<option value ='${data.id_order}' selected> ${data.num_order} </option>`
    );
    $("#refProduct").empty();
    $("#refProduct").append(`<option disabled>Seleccionar</option>`);
    $("#refProduct").append(
      `<option value ='${data.id_product}' selected> ${data.product} </option>`
    );
    $("#selectNameProduct").empty();
    $("#selectNameProduct").append(`<option disabled>Seleccionar</option>`);
    $("#selectNameProduct").append(
      `<option value ='${data.id_product}' selected> ${data.product} </option>`
    );
    $("#quantityOrder").val(data.quantity_order.toLocaleString());
    !data.accumulated_quantity
      ? (accumulated_quantity = 0)
      : (accumulated_quantity = data.accumulated_quantity);
    $("#client").val(data.client);

    $("#quantityMissing").val(accumulated_quantity.toLocaleString());
    let productsMaterials = allProductsMaterials.filter(
      (item) => item.id_product == data.id_product
    );
    productsMaterials = productsMaterials.sort(
      (a, b) => a.quantity - b.quantity
    );
    $("#quantityMP").html(
      Math.floor(productsMaterials[0].quantity).toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })
    );

    let $select = $(`#idMachine`);
    $select.empty();
    $select.append(`<option value="0" disabled>Seleccionar</option>`);

    $select.append(
      `<option value ='${data.id_machine}' selected> ${data.machine} </option>`
    );
    let $select1 = $(`#idProcess`);
    $select1.empty();
    $select1.append(`<option value="0" disabled>Seleccionar</option>`);

    $select1.append(
      `<option value ='${data.id_process}' selected> ${data.process} </option>`
    );

    $("#quantity").val(data.quantity_programming);

    $("#minDate").val(data.min_date);

    dataProgramming = {};

    if (flag_type_program == 0) {
      document.getElementById("minDate").type = "datetime-local";
      document.getElementById("minDate").readOnly = false;

      max_date = convetFormatDateTime(data.max_date);
      min_date = convetFormatDateTime(data.min_date);
      $("#minDate").val(min_date);
      $("#maxDate").val(max_date);
    }

    $(".date").show(800);
    $("#btnCreateProgramming").show(800);

    // dataProgramming = {};
    dataProgramming["id_order"] = data.id_order;
    dataProgramming["num_order"] = data.num_order;
    dataProgramming["client"] = data.client;
    dataProgramming["reference"] = data.reference;
    dataProgramming["product"] = data.product;
    dataProgramming["min_date"] = data.min_date;
    dataProgramming["max_date"] = data.max_date;
    dataProgramming["min_programming"] = data.min_programming;
    dataProgramming["update"] = 1;

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  }); */

  $(document).on("click", ".updateProgramming", async function () {
    // Mostrar elementos relevantes
    $(".cardCreateProgramming, .cardFormProgramming2, .date, #btnCreateProgramming").show(800);
    $("#btnCreateProgramming").text("Actualizar");

    // Obtener datos relevantes
    const allTblData = flattenData(generalMultiArray);
    const data = allTblData.find(item => item.id_programming == this.id);
    const accumulatedQuantity = data.accumulated_quantity || 0;

    // Almacenar y preparar datos en SessionStorage
    sessionStorage.removeItem("minDate");
    sessionStorage.setItem("id_programming", data.id_programming);

    // Actualizar elementos seleccionables
    const populateSelect = (selector, value, text) => {
      const $select = $(selector).empty();
      $select.append(`<option value="0" disabled>Seleccionar</option>`);
      $select.append(`<option value="${value}" selected>${text}</option>`);
    };

    populateSelect("#order", data.id_order, data.num_order);
    populateSelect("#refProduct", data.id_product, data.product);
    populateSelect("#selectNameProduct", data.id_product, data.product);
    populateSelect("#idMachine", data.id_machine, data.machine);
    populateSelect("#idProcess", data.id_process, data.process);

    // Actualizar valores directos
    $("#quantityOrder").val(data.quantity_order.toLocaleString());
    $("#quantityMissing").val(accumulatedQuantity.toLocaleString());
    $("#client").val(data.client);
    $("#quantity").val(data.quantity_programming);
    $("#minDate").val(data.min_date);

    // Manejo de materiales
    const productsMaterials = allProductsMaterials
      .filter(item => item.id_product == data.id_product)
      .sort((a, b) => a.quantity - b.quantity);

    if (productsMaterials.length > 0) {
      $("#quantityMP").html(
        Math.floor(productsMaterials[0].quantity).toLocaleString("es-CO", {
          maximumFractionDigits: 0,
        })
      );
    }

    // Configuración de fechas y tipo de programación
    if (flag_type_program === 0) {
      const minDateFormatted = convetFormatDateTime(data.min_date);
      const maxDateFormatted = convetFormatDateTime(data.max_date);

      const $minDate = $("#minDate").prop({ type: "datetime-local", readOnly: false });
      $minDate.val(minDateFormatted);
      $("#maxDate").val(maxDateFormatted);
    }

    // Actualizar el objeto `dataProgramming`
    dataProgramming = {
      id_order: data.id_order,
      num_order: data.num_order,
      client: data.client,
      reference: data.reference,
      product: data.product,
      min_date: data.min_date,
      max_date: data.max_date,
      min_programming: data.min_programming,
      update: 1,
    };

    // Animación al inicio de la página
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  /* Revision data programa de produccion */
  const checkdataProgramming = async (idProgramming) => {
    let id_order = $('#order').val();
    let id_product = parseInt($("#selectNameProduct").val());
    let quantityMissing = parseInt(
      $("#quantityMissing").val().replace(".", "")
    );
    let quantityProgramming = parseInt($("#quantity").val());
    let quantityOrder = parseInt($("#quantityOrder").val().replace(".", ""));
    let machine = $("#idMachine :selected").text().trim();
    let id_machine = $("#idMachine").val();
    let id_process = $("#idProcess").val();
    let process = $("#idProcess :selected").text().trim();
    let min_date = $('#minDate').val();

    let ciclesMachine = allCiclesMachines.filter(
      (item) => item.id_product == id_product
    );
    let order = allOrders.find(
      (item) => item.id_product == id_product && item.id_order == id_order
    );
    let productsMaterials = allProductsMaterials.filter(
      (item) => item.id_product == id_product
    );
    productsMaterials = productsMaterials.sort(
      (a, b) => a.quantity - b.quantity
    );
    let quantityFTM =
      Math.floor(productsMaterials[0].quantity) - quantityProgramming;

    if (ciclesMachine.length == 1) {
      for (let i = 0; i < productsMaterials.length; i++) {
        productsMaterials[i].quantity -= quantityProgramming;
      }
    }

    dataProgramming["machine"] = machine;
    dataProgramming["id_process"] = id_process;
    dataProgramming["process"] = process;
    dataProgramming["client"] = order.client;
    dataProgramming["id_product"] = id_product;
    dataProgramming["id_machine"] = id_machine;
    dataProgramming["min_date"] = min_date;
    dataProgramming["quantity_order"] = quantityOrder;
    dataProgramming["quantity_programming"] = quantityProgramming;
    dataProgramming["status"] = "PROGRAMADO";

    if (flag_type_program == 1) {
      dataProgramming["max_date"] = '';
      dataProgramming["min_programming"] = '';
    }

    if (flag_type_program == 0) {
      process = allProcess.filter((item) => item.id_product == id_product);

      // Recorre allProcess para actualizar la ruta
      for (let i = 0; i < allProcess.length; i++) {
        if (
          (quantityMissing == 0 || quantityFTM == 0) &&
          allProcess[i].id_product == id_product
        ) {
          allProcess[i].route += 1;
          if (process[process.length - 1].route >= allProcess[i].route)
            allProcess[i].status = 1;
        }
      }
    }
    // Recorre allOrders en sentido inverso para evitar problemas con la actualización de índices
    for (let i = allOrders.length - 1; i >= 0; i--) {
      if (
        allOrders[i].id_product == id_product &&
        quantityMissing == 0 &&
        process.length === 1
      ) {
        allOrders[i].flag_tbl = 0;

        if (allProcess[0].status == 1) allOrders[i].flag_process = 0;
        else allOrders[i].flag_process = 1;
      }
    }

    // Recorre allOrdersProgramming en sentido inverso
    for (let i = allOrdersProgramming.length - 1; i >= 0; i--) {
      if (
        allOrdersProgramming[i].id_product == id_product &&
        quantityMissing == 0 &&
        process.length === 1
      ) {
        allOrdersProgramming[i].flag_tbl = 0;
      }
    }    

    function updateOrder(order, quantityProgramming, ciclesMachine) {
      order.status = "PROGRAMADO";
      let quantity = 0;

      if (quantityProgramming < order.original_quantity) {
        quantity = order.original_quantity - quantityProgramming;
        if (!order.accumulated_quantity) {
          order.quantity_programming = quantity;
          order.accumulated_quantity_order = quantity;
          order.accumulated_quantity = (ciclesMachine.length === 1)
            ? quantity
            : order.original_quantity;
        } else {
          order.accumulated_quantity_order = Math.max(
            order.accumulated_quantity - quantityProgramming, 0
          );

          flag_type_program == 0
            ?
            order.accumulated_quantity = (ciclesMachine.length === 1)
              ? Math.max(order.accumulated_quantity - quantityProgramming, 0)
              : order.original_quantity
            :
            order.accumulated_quantity = Math.max(order.accumulated_quantity - quantityProgramming, 0);
          
          order.quantity_programming = Math.max(
            order.accumulated_quantity - quantityProgramming, 0
          );
        }
      } else {
        order.accumulated_quantity_order = quantity;
        order.accumulated_quantity = quantity;
        
        if (quantity == 0) {
          order.flag_tbl = 0;
        }
        
        if (flag_type_program == 0)
          order.accumulated_quantity = (ciclesMachine.length === 1)
            ? quantity
            : order.original_quantity;
      }

      quantityMissing = quantity;
    }

    // Actualiza todas las órdenes
    allOrders.forEach(order => {
      if (order.id_order == id_order) {
        updateOrder(order, quantityProgramming, ciclesMachine);
      }
    });

    allOrdersProgramming.forEach(order => {
      if (order.id_order == id_order) {
        updateOrder(order, quantityProgramming, ciclesMachine);
      }
    });

    // Actualizar proceso y órdenes si el type_program es 0
    if (flag_type_program == 0) {
      process = allProcess.filter(item => item.id_product == id_product);

      allProcess.forEach(proc => {
        if ((quantityMissing == 0 || quantityFTM == 0) && proc.id_product == id_product) {
          proc.route += 1;
          if (process[process.length - 1].route >= proc.route) {
            proc.status = 1;
          }
        }
      });

      allOrders.slice().reverse().forEach(order => {
        if (order.id_product == id_product && quantityMissing == 0 && process.length === 1) {
          order.flag_tbl = 0;
          order.flag_process = (allProcess[0].status == 1) ? 0 : 1;
        }
      });

      allOrdersProgramming.slice().reverse().forEach(order => {
        if (order.id_product == id_product && quantityMissing == 0 && process.length === 1) {
          order.flag_tbl = 0;
        }
      });
    }
    
    let allTblData = flattenData(generalMultiArray);
    let sim = $("#simulationType").val();

    quantityMissing < 0 ? (quantityMissing = 0) : quantityMissing;

    dataProgramming["accumulated_quantity"] = quantityMissing;
    dataProgramming["accumulated_quantity_order"] = quantityMissing;
    dataProgramming["key"] = allTblData.length;

    if (quantityMissing - quantityProgramming > 0)
      dataProgramming["route"] = allProcess[0].route;
    dataProgramming["sim"] = sim;

    hideCardAndResetForm();

    if (idProgramming == null) {
      for (let i = 0; i < allTblData.length; i++) {
        for (let j = 0; j < allTblData.length; j++) {
          if (allTblData[i].id_programming === allTblData[j].id_programming) {
            let arr = allTblData.filter(
              (item) => item.id_programming === allTblData[i].id_programming
            );

            if (arr.length > 1) allTblData.splice(i, 1);
          }
        }
      }

      allTblData.push(dataProgramming);

      processProgramming.push({
        id_process: id_process,
        id_machine: id_machine,
        quantity_order: quantityOrder,
        quantity_programming: quantityProgramming,
      });

      let key;

      sim == 1 ? (key = 0) : (key = 1);
      // Encontrar el objeto correspondiente en multiarray
      let targetArray = generalMultiArray[key][`sim_${sim}`];

      if (targetArray) {
        for (let i = 0; i < targetArray.length; i++) {
          // Verificar si existe el process con el id_process
          if (targetArray[i][`process-${id_process}`]) {
            // Verificar si dentro del process existe la machine con el id_machine
            if (
              targetArray[i][`process-${id_process}`][`machine-${id_machine}`]
            ) {
              // Agregar dataProgramming a la máquina correspondiente
              targetArray[i][`process-${id_process}`][
                `machine-${id_machine}`
              ].push(dataProgramming);
              break; // Salir del bucle después de encontrar y actualizar el proceso
            }
          }
        }
      }

      changeStatus(
        order.id_order,
        4,
        "Programa de producción creado correctamente"
      );
    } else {
      for (let i = 0; i < allTblData.length; i++) {
        if (allTblData[i].id_programming == idProgramming) {
          allTblData[i].accumulated_quantity = quantityMissing;
          allTblData[i].accumulated_quantity_order = quantityMissing;
          allTblData[i].quantity_programming = quantityProgramming;
          allTblData[i].min_date = dataProgramming["min_date"];
          allTblData[i].max_date = dataProgramming["max_date"];
          allTblData[i].min_programming = dataProgramming["min_programming"];
        }
      }

      let sim = $("#simulationType").val();
      let key;

      sim == 1 ? (key = 0) : (key = 1);

      for (let i = 0; i < generalMultiArray[key][`sim_${sim}`].length; i++) {
        if (generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`]) {
          // Agregar verificación para la máquina
          let processMachines =
            generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`];

          if (processMachines[`machine-${id_machine}`]) {
            let machineArray = processMachines[`machine-${id_machine}`];

            for (let j = 0; j < machineArray.length; j++) {
              if (machineArray[j].id_programming == idProgramming) {
                machineArray[j].accumulated_quantity = quantityMissing;
                machineArray[j].accumulated_quantity_order = quantityMissing;
                machineArray[j].quantity_programming = quantityProgramming;
                machineArray[j].min_date = dataProgramming["min_date"];
                machineArray[j].max_date = dataProgramming["max_date"];
                machineArray[j].min_programming =
                  dataProgramming["min_programming"];
                break;
              }
            }
          }
        }
      }

      toastr.success("Programa de producción modificado correctamente");
    }

    let uniqueIdsSet = new Set(); // Conjunto para almacenar IDs únicas
    let uniqueArray = []; // Array para almacenar elementos únicos

    allTblData.forEach((item) => {
      // Verificamos si el ID ya existe en el conjunto
      if (!uniqueIdsSet.has(item.id_machine)) {
        uniqueIdsSet.add(item.id_machine); // Si no existe, lo agregamos al conjunto
        uniqueArray.push(item); // También agregamos el elemento al array de elementos únicos
      }
    });

    sessionStorage.setItem('allOrders', JSON.stringify(allOrders));
    sessionStorage.setItem('allOrdersProgramming', JSON.stringify(allOrdersProgramming));
    sessionStorage.setItem('allProcess', JSON.stringify(allProcess));
    sessionStorage.setItem('allCiclesMachines', JSON.stringify(allCiclesMachines));
    sessionStorage.setItem('allPlanningMachines', JSON.stringify(allPlanningMachines));
    sessionStorage.setItem('allProductsMaterials', JSON.stringify(allProductsMaterials));

    // Cargar selects de maquinas por pedidos programados
    $('#quantityMP').html('');
    loadDataMachinesProgramming(uniqueArray);
    
    // Cargar selects de pedidos que esten por programar
    loadOrdersProgramming(allOrdersProgramming);
    if (flag_type_program == 0) {
      checkProcessMachines(allTblData);
    }

    loadTblProgramming(allTblData, 1);

    dataProgramming = [];
  };

  /* Eliminar programa de produccion */

  deleteFunction = (id, bd_status) => {
    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este programa de produccion? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          if (bd_status == 0) {
            let allTblData = flattenData(generalMultiArray);

            const idProduct = allTblData[id].id_product;
            const quantityProgramming = allTblData[id].quantity_programming;
            const quantityOrder = allTblData[id].quantity_order;
            const accumulatedQuantity = allTblData[id].accumulated_quantity;

            let ciclesMachine = allCiclesMachines.filter(
              (item) => item.id_product == idProduct
            );
            if (ciclesMachine.length == 1) {
              let productsMaterials = allProductsMaterials.filter(
                (item) => item.id_product == idProduct
              );
              productsMaterials = productsMaterials.sort(
                (a, b) => a.quantity - b.quantity
              );
              for (let i = 0; i < productsMaterials.length; i++) {
                productsMaterials[i].quantity -= quantityProgramming;
              }
            }

            for (const orderList of [allOrders, allOrdersProgramming]) {
              for (let i = 0; i < orderList.length; i++) {
                const order = orderList[i];
                if (
                  order.id_product == idProduct &&
                  order.id_order == allTblData[id].id_order
                ) {
                  order.flag_tbl = 1;
                  let quantity =
                    quantityProgramming > quantityOrder
                      ? quantityOrder
                      : quantityProgramming;

                  order.accumulated_quantity_order += quantity;

                  if (
                    order.hasOwnProperty("quantity_programming") &&
                    (quantity == 0 ||
                      quantity == quantityOrder ||
                      order.accumulated_quantity == quantityOrder)
                  ) {
                    delete order["quantity_programming"];
                  } else {
                    order.quantity_programming += quantity;
                  }
                }
              }
            }

            let arr = allTblData.filter(
              (item) => item.id_order == allTblData[id].id_order
            );
            if (arr.length == 1)
              changeStatus(
                allTblData[id].id_order,
                1,
                "Programa de producción eliminado correctamente"
              );

            // Encontrar el objeto correspondiente en multiarray
            let sim = allTblData[id].sim;
            let id_process = allTblData[id].id_process;
            let id_machine = allTblData[id].id_machine;
            sim == 1 ? (key = 0) : (key = 1);

            let targetArray = generalMultiArray[key][`sim_${sim}`];

            if (targetArray) {
              for (let i = 0; i < targetArray.length; i++) {
                // Verificar si existe el process con el id_process
                if (targetArray[i][`process-${id_process}`]) {
                  // Verificar si dentro del process existe la machine con el id_machine
                  if (
                    targetArray[i][`process-${id_process}`][`machine-${id_machine}`]
                  ) {
                    // Agregar dataProgramming a la máquina correspondiente
                    targetArray[i][`process-${id_process}`][
                      `machine-${id_machine}`
                    ].splice(id, 1);
                    break; // Salir del bucle después de encontrar y actualizar el proceso
                  }
                }
              }
            }
            allTblData.splice(id, 1); 
            loadTblProgramming(allTblData, 1);            
          } else {
            let data = allTblData.find((item) => item.id_programming == id);
            let dataProgramming = new FormData();
            dataProgramming.append("idProgramming", data.id_programming);
            dataProgramming.append("order", data.id_order);
            dataProgramming.append("id_order", data.id_order);
            dataProgramming.append("accumulated_quantity_order", "");

            for (let i = 0; i < allTblData.length; i++) {
              if (allTblData[i].id_programming == id) {
                allTblData.splice(i, 1);
              }
            }

            // Encontrar el objeto correspondiente en multiarray
            let sim = data.sim;
            let id_process = data.id_process;
            let id_machine = data.id_machine;
            sim == 1 ? (key = 0) : (key = 1);

            let targetArray = generalMultiArray[key][`sim_${sim}`];

            if (targetArray) {
              for (let i = 0; i < targetArray.length; i++) {
                // Verificar si existe el process con el id_process
                if (targetArray[i][`process-${id_process}`]) {
                  // Verificar si dentro del process existe la machine con el id_machine
                  if (
                    targetArray[i][`process-${id_process}`][`machine-${id_machine}`]
                  ) {
                    for (let i = 0; i < targetArray[i][`process-${id_process}`][`machine-${id_machine}`].length; i++) {
                      if (targetArray[i][`process-${id_process}`][`machine-${id_machine}`][i].id_programming == id) {
                        targetArray[i][`process-${id_process}`][
                          `machine-${id_machine}`
                        ].splice(i, 1);
                        break;
                      }
                    }
                  }
                }
              }
            }

            $.ajax({
              type: "POST",
              url: "/api/deleteProgramming",
              data: dataProgramming,
              contentType: false,
              cache: false,
              processData: false,
              success: function (resp) {
                message(resp);
              },
            });
          }
        }
      },
    });
  };

  const changeStatus = async (id_order, status, message) => {
    let resp = await searchData(`/api/statusOrder/${id_order}/${status}`);

    try {
      if (resp.success) {
        toastr.success(message);
      } else if (resp.error) {
        toastr.error(resp.message);
      } else if (resp.info) {
        toastr.info(resp.message);
      }
    } catch (error) {
      console.error("Error in message function:", error);
    }
  };

  /* Cambiar estado */
  $(document).on("click", "#btnAddOP", function () { 
    bootbox.confirm({
      title: "Orden de Producción",
      message:
        "Desea crear la Orden de Produccion? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {

          $('.cardBottonsGeneral').hide();

          let form = document.getElementById('divBottons');
          form.insertAdjacentHTML(
            'beforeend',
            `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
              <div class="spinner-grow text-dark" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
            </div>`
          );

          $.ajax({
            type: "POST",
            url: "/api/changeStatusProgramming",
            data: { data: allProgramming },
            success: function (data) { 
              setTimeout(() => {
                $('.cardLoading').remove();
                $('.cardBottonsGeneral').show(400);                
                $(".cardAddOP").hide(800);
              }, 4000);

              generalMultiArray = [];

              message(data);
            },
          });
        }
      },
    });
  });

  $(document).on("click", "#btnChangeStatus", function () {
    $("#tblStatusProgramming").empty();

    let tblStatusProgramming = document.getElementById("tblStatusProgramming");

    tblStatusProgramming.insertAdjacentHTML(
      "beforeend",
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
      <tbody id="tblStatusProgrammingBody"></tbody>`
    );

    setTblStatusProgramming();
  });

  $("#btnSavePrograming").click(function (e) {
    e.preventDefault();

    bootbox.confirm({
      title: "Planeación",
      message: "Desea guardar la planeación? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $('.cardBottonsGeneral').hide();

          let form = document.getElementById('divBottons');
          form.insertAdjacentHTML(
            'beforeend',
            `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
              <div class="spinner-grow text-dark" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
            </div>`
          );

          let allTblData = flattenData(generalMultiArray);

          if (allTblData.length == 0) {
            toastr.error("Ingrese una programacion");
            return false;
          }
          let sim = $("#simulationType").val();

          allTblData = allTblData.map((item) => ({ ...item, sim: sim }));

          $.ajax({
            type: "POST",
            url: "/api/saveProgramming",
            data: { data: allTblData },
            success: function (resp) {
              $('.cardLoading').remove();
              generalMultiArray = []; 
              sessionStorage.clear();
              
              setTimeout(() => {
                $('.cardBottonsGeneral').show(400);
              }, 5000);

              message(resp);
            },
          });
        }
      },
    });
  });

  /* Mensaje de exito */
  const message = async (data) => {
    try {
      if (data.success) { 
        hideCardAndResetForm();
        toastr.success(data.message);
        await loadAllDataProgramming();
      } else if (data.error) {
        toastr.error(data.message);
      } else if (data.info) {
        toastr.info(data.message);
      }
    } catch (error) {
      console.error("Error in message function:", error);
    }
  };

  // Función auxiliar para ocultar la tarjeta y reiniciar el formulario
  const hideCardAndResetForm = () => {
    $(".cardCreateProgramming").hide(800);
    $("#formCreateProgramming").trigger("reset");
  }; 

  // Función para aplanar el array considerando id_machine
  flattenData = (data) => {
    const flattened = [];

    data.forEach((sim) => {
      Object.values(sim).forEach((processes) => {
        processes.forEach((process) => {
          // Iterar sobre cada máquina dentro del proceso
          Object.values(process).forEach((machines) => {
            Object.values(machines).forEach((items) => {
              flattened.push(...items); // Agregar los objetos aplanados al array
            });
          });
        });
      });
    });

    return flattened;
  };
});
