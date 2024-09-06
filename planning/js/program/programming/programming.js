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

    /* switch (option) {
      case "link-table":
        $(".cardProgramming").show();
        break;
      case "link-dashboard":
        $(".cardDashboardProgramming").show();
        break;
    } */
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

    $(".date, .cardFormProgramming2").hide();
    $("#refProduct, #selectNameProduct, #idMachine, #idProcess").empty();
    $(".cardCreateProgramming").toggle(800);
    $("#btnCreateProgramming").text("Crear");
    $("#formCreateProgramming").trigger("reset");
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

    if (isNaN(checkDT1) || checkDT1 <= 0 || min_date == "" || max_date == "") {
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

  /* Actualizar programa de produccion */

  $(document).on("click", ".updateProgramming", async function (e) {
    $(".cardCreateProgramming").show(800);
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

    $("#quantityMissing").val(accumulated_quantity.toLocaleString());
    let productsMaterials = allProductsMaterials.filter(
      (item) => item.id_product == data.id_product
    );
    productsMaterials = productsMaterials.sort(
      (a, b) => a.quantity - b.quantity
    );
    $("#quantityMP").val(
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

    document.getElementById("minDate").type = "datetime-local";
    document.getElementById("minDate").readOnly = false;
    $(".date").show(800);
    $("#btnCreateProgramming").show(800);

    max_date = convetFormatDateTime(data.max_date);
    min_date = convetFormatDateTime(data.min_date);

    $("#minDate").val(min_date);
    $("#maxDate").val(max_date);

    dataProgramming = {};
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
  });

  /* Revision data programa de produccion */
  const checkdataProgramming = async (idProgramming) => {
    // let id_order = $('#order').val();
    let num_order = $("#order :selected").text().trim();
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

    let ciclesMachine = allCiclesMachines.filter(
      (item) => item.id_product == id_product
    );
    let order = allOrders.find(
      (item) => item.id_product == id_product && item.num_order == num_order
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
    dataProgramming["quantity_order"] = quantityOrder;
    dataProgramming["quantity_programming"] = quantityProgramming;
    dataProgramming["status"] = "PROGRAMADO";

    for (let i = 0; i < allOrders.length; i++) {
      if (allOrders[i].id_order == order.id_order) {
        allOrders[i].status = "PROGRAMADO";

        let quantity = 0;

        if (
          quantityProgramming <
          allOrders[i]
            .original_quantity /*&& productsMaterials[0].quantity > allOrders[i].original_quantity*/
        ) {
          quantity = allOrders[i].original_quantity - quantityProgramming;
          if (
            allOrders[i].accumulated_quantity == 0 ||
            allOrders[i].accumulated_quantity == null
          ) {
            allOrders[i].quantity_programming = quantity;
            allOrders[i].accumulated_quantity_order = quantity;
            allOrders[i].accumulated_quantity =
              ciclesMachine.length == 1
                ? quantity
                : allOrders[i].original_quantity;
          } else {
            allOrders[i].accumulated_quantity_order =
              allOrders[i].accumulated_quantity - quantityProgramming < 0
                ? 0
                : allOrders[i].accumulated_quantity - quantityProgramming;
            allOrders[i].accumulated_quantity =
              ciclesMachine.length == 1
                ? allOrders[i].accumulated_quantity - quantityProgramming < 0
                  ? 0
                  : allOrders[i].accumulated_quantity - quantityProgramming
                : allOrders[i].original_quantity;
            allOrders[i].quantity_programming =
              allOrders[i].accumulated_quantity - quantityProgramming < 0
                ? 0
                : allOrders[i].accumulated_quantity - quantityProgramming;
          }
        } else {
          allOrders[i].accumulated_quantity_order = quantity;
          allOrders[i].accumulated_quantity =
            ciclesMachine.length == 1
              ? quantity
              : allOrders[i].original_quantity;
        }
      }
    }

    for (let i = 0; i < allOrdersProgramming.length; i++) {
      if (allOrdersProgramming[i].id_order == order.id_order) {
        allOrdersProgramming[i].status = "PROGRAMADO";

        quantityMissing = 0;

        if (
          quantityProgramming <
          allOrdersProgramming[i]
            .original_quantity /*&& productsMaterials[0].quantity > allOrders[i].original_quantity*/
        ) {
          quantityMissing =
            allOrdersProgramming[i].original_quantity - quantityProgramming;
          if (
            allOrdersProgramming[i].accumulated_quantity == 0 ||
            allOrdersProgramming[i].accumulated_quantity == null
          ) {
            allOrdersProgramming[i].quantity_programming = quantityMissing;
            allOrdersProgramming[i].accumulated_quantity =
              ciclesMachine.length == 1
                ? quantityMissing
                : allOrdersProgramming[i].original_quantity;
            allOrdersProgramming[i].accumulated_quantity_order =
              quantityMissing;
          } else {
            allOrdersProgramming[i].accumulated_quantity_order =
              allOrdersProgramming[i].accumulated_quantity -
                quantityProgramming <
              0
                ? 0
                : allOrdersProgramming[i].accumulated_quantity -
                  quantityProgramming;
            allOrdersProgramming[i].accumulated_quantity =
              ciclesMachine.length == 1
                ? allOrdersProgramming[i].accumulated_quantity -
                    quantityProgramming <
                  0
                  ? 0
                  : allOrdersProgramming[i].accumulated_quantity -
                    quantityProgramming
                : allOrdersProgramming[i].original_quantity;
            allOrdersProgramming[i].quantity_programming =
              allOrdersProgramming[i].accumulated_quantity -
                quantityProgramming <
              0
                ? 0
                : allOrdersProgramming[i].accumulated_quantity -
                  quantityProgramming;
            quantityMissing =
              allOrdersProgramming[i].accumulated_quantity_order;
          }
        } else {
          allOrdersProgramming[i].accumulated_quantity_order = quantityMissing;
        }
      }
    }

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
    
    let allTblData = flattenData(generalMultiArray);

    quantityMissing < 0 ? (quantityMissing = 0) : quantityMissing;

    dataProgramming["accumulated_quantity"] = quantityMissing;
    dataProgramming["accumulated_quantity_order"] = quantityMissing;
    dataProgramming["key"] = allTblData.length;

    if (quantityMissing - quantityProgramming > 0)
      dataProgramming["route"] = allProcess[0].route;

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

      let sim = $("#simulationType").val();
      let key;

      sim == 1 ? (key = 0) : (key = 1); 
      // Encontrar el objeto correspondiente en multiarray
      let targetArray = generalMultiArray[key][`sim_${sim}`];

      if (targetArray) {
        for (let i = 0; i < targetArray.length; i++) {
          if (targetArray[i][`process-${id_process}`]) {
            targetArray[i][`process-${id_process}`].push(dataProgramming);
            break; // Salir del bucle después de encontrar y actualizar el proceso
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
          for (
            let j = 0;
            j <
            generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`]
              .length;
            j++
          ) {
            if (
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].id_programming == idProgramming
            ) {
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].accumulated_quantity = quantityMissing;
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].accumulated_quantity_order = quantityMissing;
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].quantity_programming = quantityProgramming;
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].min_date = dataProgramming["min_date"];
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].max_date = dataProgramming["max_date"];
              generalMultiArray[key][`sim_${sim}`][i][`process-${id_process}`][
                j
              ].min_programming = dataProgramming["min_programming"];
              break;
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

    // Cargar selects de maquinas por pedidos programados
    loadDataMachinesProgramming(uniqueArray);
    checkProcessMachines(allTblData);
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
                  order.id_product === idProduct &&
                  order.id_order === allTblData[id].id_order
                ) {
                  order.flag_tbl = 1;
                  let quantity =
                    quantityProgramming > quantityOrder
                      ? quantityOrder
                      : quantityProgramming;

                  order.accumulated_quantity_order += quantity;

                  if (
                    order.hasOwnProperty("quantity_programming") &&
                    (quantity === 0 ||
                      quantity === quantityOrder ||
                      order.accumulated_quantity === quantityOrder)
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
              if (allTblData[i].id_programming === id) {
                allTblData.splice(i, 1);
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
          $.ajax({
            type: "POST",
            url: "/api/changeStatusProgramming",
            data: { data: allProgramming },
            success: function (data) {
              allTblData = [];
              $(".cardAddOP").hide(800);

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
              allTblData = [];
              $(".cardAddOP").show(800);
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
        let allTblData = flattenData(generalMultiArray);

        sessionStorage.setItem("dataProgramming", JSON.stringify(allTblData));

        hideCardAndResetForm();
        toastr.success(message);
        await loadAllDataProgramming();
      } else if (data.error) {
        toastr.error(message);
      } else if (data.info) {
        toastr.info(message);
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

  // Función para aplanar el array
  flattenData = (data) => {
    const flattened = [];

    data.forEach((sim) => {
      Object.values(sim).forEach((processes) => {
        processes.forEach((process) => {
          Object.values(process).forEach((items) => {
            flattened.push(...items);
          });
        });
      });
    });

    return flattened;
  };
});
