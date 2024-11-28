$(document).ready(function () {
  // let dataPTOP, allStore, allMaterialsAccept, id_programming;
  // loadAllDataPO = async () => {
  //   id_programming = sessionStorage.getItem("id_programming");

  //   const [dataOP, dataFTMaterials, dataStore, materialsCM] = await Promise.all(
  //     [
  //       searchData("/api/productionOrder"),
  //       searchData("/api/allProductsMaterials"),
  //       searchData("/api/allStore"),
  //       searchData("/api/materialsComponents"),
  //     ]
  //   ); 
  //   sessionStorage.setItem("dataFTMaterials", JSON.stringify(dataFTMaterials)); 
  //   allStore = dataStore;

  //   let data = dataOP.find((item) => item.id_programming == id_programming);
  //   dataPTOP = data;
  //   let dataFT = dataFTMaterials.filter(
  //     (item) => item.id_product == data.id_product
  //   );
  //   allMaterialsAccept = materialsCM.filter(
  //     (item) => item.id_programming == id_programming
  //   );

  //   let flag_op = data.flag_op;

  //   if (flag_op == 1) {
  //     $("#formAddOPPArtial").hide();
  //     $("#formAddOPMP").hide();
  //     $(".cardCloseOP").hide();
  //     $("#thActions").hide();
  //   }

  //   if (data.origin == 1) {
  //     $(".cardMeasure").hide();
  //   }

  //   if (data.flag_cancel == 1) $(".cardExcOP").hide();
  //   else $(".cardExcOP").show();

  //   $("#imgClient").empty();

  //   if (data.img) $("#imgClient").html(`<img src="${data.img}" width="150px">`);
  //   // Orden Produccion
  //   $("#txtNumOP").html(data.num_production);
  //   $("#txtNumOrder").html(data.num_order);

  //   let date_order = moment(data.date_order).format("DD/MM/YYYY");
  //   let min_date =
  //     moment(data.min_date_order).format("DD/MM/YYYY") == "Invalid date"
  //       ? "0000-00-00"
  //       : moment(data.min_date_order).format("DD/MM/YYYY");
  //   let max_date =
  //     moment(data.max_date_order).format("DD/MM/YYYY") == "Invalid date"
  //       ? "0000-00-00"
  //       : moment(data.max_date_order).format("DD/MM/YYYY");

  //   $("#txtEDate").html(
  //     `<p><b class="font-weight-bold text-dark">Fecha de Emisión:</b>  ${date_order}</p>`
  //   );

  //   $("#txtMinDate").val(min_date);
  //   $("#txtMaxDate").val(max_date);
  //   $("#txtQuantityP").val(data.quantity_programming);
  //   $("#nameClient").val(data.client);

  //   // Info Producto
  //   $("#txtReferenceP").val(data.reference);
  //   $("#txtNameP").val(data.product);
  //   $("#width").val(data.width);
  //   $("#high").val(data.high);
  //   $("#length").val(data.length);
  //   $("#usefulLength").val(data.useful_length);
  //   $("#totalWidth").val(data.total_width);
  //   $("#window").val(data.window);

  //   // Datatable Materiales
  //   $("#tblPOMaterialsBody").empty();
  //   let body = document.getElementById("tblPOMaterialsBody");

  //   //Inicializa variables para costeo total
  //   let totalCostFtm = 0;
  //   let totalCostTotal = 0;
  //   let totalCostLb = 0;

  //   // Iterar sobre los datos para generar el body
  //   for (let i = 0; i < dataFT.length; i++) {
  //     let quantity_ftm = formatQuantity(
  //       dataFT[i].quantity_ftm,
  //       dataFT[i].abbreviation
  //     );
  //     let quantity_total =
  //       parseFloat(dataFT[i].quantity_ftm) *
  //       parseFloat(data.quantity_programming);

  //     let cost_ftm = dataFT[i].quantity_ftm * dataFT[i].cost;
  //     let cost_total = quantity_total * dataFT[i].cost;

  //     // Acumular los costos
  //     totalCostFtm += cost_ftm;
  //     totalCostTotal += cost_total;

  //     quantity_total = formatQuantity(quantity_total, dataFT[i].abbreviation);

  //     let store = allStore.filter(
  //       (item) =>
  //         item.id_programming == id_programming &&
  //         item.id_material == dataFT[i].id_material
  //     );

  //     let recieve = 0;
  //     let for_recieve = 0;
  //     let pending = 0;

  //     store.forEach((item) => {
  //       recieve += parseFloat(item.quantity_component_user);
  //       for_recieve += parseFloat(item.delivery_store) - parseFloat(item.quantity_component_user);
  //       item.delivery_pending == 0
  //         ? (pending = 0)
  //         : (pending += parseFloat(item.delivery_pending));
  //     });

  //     let materialsAccept = allMaterialsAccept.filter(
  //       (item) => item.id_material == dataFT[i].id_material
  //     );

  //     let accept = 0;
  //     materialsAccept.forEach((item) => {
  //       accept += parseFloat(item.quantity);
  //     });

  //     pending < 0 ? (pending = 0) : pending;

  //     let action = "";
  //     // let value = for_recieve - accept;

  //     if (for_recieve > 0 || recieve > 0) {
  //       if (for_recieve > 0) {
  //         action = `<button class="btn btn-info acceptMaterial" id="accept-${dataFT[i].id_material}">Aceptar MP</button>`;
  //       } else if (recieve > 0) {
  //         action = `<a href="javascript:;">
  //                           <i class="mdi mdi-playlist-check seeAcceptMP programming-${id_programming} material-${dataFT[i].id_material}" data-toggle="tooltip" title="Ver Usuarios" style="font-size: 30px;color:black"></i>
  //                         </a>`;
  //       }
  //     }

  //     body.insertAdjacentHTML(
  //       "beforeend",
  //       `<tr>
  //           <td>${dataFT[i].reference_material}</td>
  //           <td>${dataFT[i].material}</td>
  //           <td>${quantity_ftm} ${dataFT[i].abbreviation}</td>
  //           <td>${quantity_total} ${dataFT[i].abbreviation}</td>
  //           <td>$${cost_ftm.toLocaleString("es-CO", {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 2,
  //           })}</td>
  //           <td>$${cost_total.toLocaleString("es-CO", {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 0,
  //           })}</td>
  //           <td>${recieve.toLocaleString("es-CO", {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 2,
  //           })}</td>
  //           <td>${for_recieve.toLocaleString("es-CO", {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 2,
  //           })}</td>
  //           <td>${pending.toLocaleString("es-CO", {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 2,
  //           })}</td> 
  //           ${flag_op == 0 ? `<td>${action}</td>` : ""}
  //        </tr>`
  //     );
  //   }

  //   // Crear el tfoot con las sumas totales
  //   $("#tblPOMaterialsFoot").empty();
  //   let foot = document.getElementById("tblPOMaterialsFoot");

  //   foot.insertAdjacentHTML(
  //     "beforeend",
  //     `<tr>
  //         <td colspan="4"><strong>Total</strong></td>
  //         <td class="costMaterialsUnit"><strong>$${totalCostFtm.toLocaleString(
  //           "es-CO",
  //           {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 2,
  //           }
  //         )}</strong></td>
  //         <td class="costMaterials"><strong>$${totalCostTotal.toLocaleString(
  //           "es-CO",
  //           {
  //             minimumFractionDigits: 0,
  //             maximumFractionDigits: 0,
  //           }
  //         )}</strong></td>
  //         <td colspan="3"></td>
  //       </tr>`
  //   );

  //   // Procesos
  //   $("#tblPOProcessBody").empty();
  //   body = document.getElementById("tblPOProcessBody");

  //   let dataPOProcess = [];

  //   if (flag_type_program == 0) {
  //     dataPOProcess.push(data);
  //   } else {
  //     dataPOProcess = await searchData(
  //       `/api/productionOrder/${data.id_order}/${data.id_product}`
  //     );
  //   }
 
  //   dataPOProcess.forEach((process) => {
  //     const { max_date_programming, min_date_programming, cost_payroll, cost_machine, process: processName, machine, id_programming, close_op } = process;

  //     let minDate = min_date_programming
  //       ? moment(min_date_programming).format(flag_type_program == 0 ? "DD/MM/YYYY hh:mm A" : "DD/MM/YYYY")
  //       : "";

  //     !minDate || minDate == "Invalid date" ? minDate = '' : minDate;
      
  //     let maxDate = flag_type_program == 0
  //       ? moment(max_date_programming).format("DD/MM/YYYY hh:mm A")
  //       : "";
      
  //     !maxDate || maxDate == "Invalid date" ? maxDate = '' : maxDate;

  //     const payrollCost = parseFloat(cost_payroll).toLocaleString("es-CO", {
  //       minimumFractionDigits: 0,
  //       maximumFractionDigits: 0,
  //     });

  //     const machineCost = parseFloat(cost_machine).toLocaleString("es-CO", {
  //       minimumFractionDigits: 0,
  //       maximumFractionDigits: 0,
  //     });

  //     const statusBadge =
  //       id_programming == 0
  //         ? `<i class="bi bi-shield-fill-x" data-toggle="tooltip" style="font-size:25px; color:#ee2020;"></i>`
  //         : close_op == 0
  //           ? `<span class="badge badge-warning" style="font-size:100%">En proceso</span>`
  //           : `<span class="badge badge-success" style="font-size:100%">Finalizado</span>`;

  //     const trPC = `
  //       <tr>
  //         <td>1</td>
  //         <td>${processName}</td>
  //         <td>${machine}</td>
  //         <td>${minDate}</td>
  //           ${flag_type_program == 0 ?
  //         `<td>${maxDate}</td>` : ''}
  //         <td>$${payrollCost}</td>
  //         <td>$${machineCost}</td>
  //         ${flag_type_program == 1 ?
  //         `<td>${statusBadge}</td>` : ''} 
  //       </tr>
  //     `;

  //     body.insertAdjacentHTML("beforeend", trPC);
  //   });

  //   if (data.flag_cancel == 0) { 
  //     loadTblPartialsDelivery(id_programming);
  //     loadTblOPMaterial(id_programming);
  //   }
  // };
 
  let dataPTOP, allStore, allMaterialsAccept, id_programming;

  const loadAllDataPO = async () => {
    id_programming = sessionStorage.getItem("id_programming");

    // Carga datos en paralelo
    const [dataOP, dataFTMaterials, dataStore, materialsCM] = await Promise.all([
      searchData("/api/productionOrder"),
      searchData("/api/allProductsMaterials"),
      searchData("/api/allStore"),
      searchData("/api/materialsComponents"),
    ]);

    sessionStorage.setItem("dataFTMaterials", JSON.stringify(dataFTMaterials));
    allStore = dataStore;

    // Filtra y procesa datos necesarios
    const data = dataOP.find((item) => item.id_programming == id_programming);
    dataPTOP = data;
    const dataFT = dataFTMaterials.filter(
      (item) => item.id_product == data.id_product
    );
    allMaterialsAccept = materialsCM.filter(
      (item) => item.id_programming == id_programming
    );

    const flag_op = data.flag_op;

    // Control de visibilidad
    toggleVisibility(flag_op, data.origin, data.flag_cancel);

    // Carga información básica
    loadClientImage(data.img);
    loadOrderDetails(data);

    // Genera contenido dinámico
    loadMaterialTable(dataFT, data.quantity_programming, flag_op);
    loadProcessTable(data);

    if (data.flag_cancel == 0) {
      loadTblPartialsDelivery(id_programming);
      loadTblOPMaterial(id_programming);
    }
  };

  // Controla la visibilidad de elementos del DOM
  const toggleVisibility = (flag_op, origin, flag_cancel) => {
    if (flag_op == 1) {
      $("#formAddOPPArtial, #formAddOPMP, .cardCloseOP, #thActions").hide();
    }
    if (origin == 1) $(".cardMeasure").hide();
    flag_cancel == 1 ? $(".cardExcOP").hide() : $(".cardExcOP").show();
  };

  // Carga imagen del cliente
  const loadClientImage = (img) => {
    $("#imgClient").empty();
    if (img) {
      $("#imgClient").html(`<img src="${img}" width="150px">`);
    }
  };

  // Carga detalles de la orden
  const loadOrderDetails = (data) => {
    $("#txtNumOP").html(data.num_production);
    $("#txtNumOrder").html(data.num_order);

    const formatDate = (date) =>
      moment(date).format("DD/MM/YYYY") || "0000-00-00";

    $("#txtEDate").html(
      `<p><b class="font-weight-bold text-dark">Fecha de Emisión:</b> ${formatDate(
        data.date_order
      )}</p>`
    );

    $("#txtMinDate").val(formatDate(data.min_date_order));
    $("#txtMaxDate").val(formatDate(data.max_date_order));
    $("#txtQuantityP").val(data.quantity_programming);
    $("#nameClient").val(data.client);

    $("#txtReferenceP").val(data.reference);
    $("#txtNameP").val(data.product);
    $("#width").val(data.width);
    $("#high").val(data.high);
    $("#length").val(data.length);
    $("#usefulLength").val(data.useful_length);
    $("#totalWidth").val(data.total_width);
    $("#window").val(data.window);
  };

  // Genera tabla de materiales
  const loadMaterialTable = (dataFT, quantity_programming, flag_op) => {
    const body = document.getElementById("tblPOMaterialsBody");
    $("#tblPOMaterialsBody").empty();

    let totalCostFtm = 0;
    let totalCostTotal = 0;

    dataFT.forEach((material) => {
      const quantity_ftm = formatQuantity(material.quantity_ftm, material.abbreviation);
      const quantity_total = material.quantity_ftm * quantity_programming;

      const cost_ftm = material.quantity_ftm * material.cost;
      const cost_total = quantity_total * material.cost;

      totalCostFtm += cost_ftm;
      totalCostTotal += cost_total;

      const storeData = getStoreData(material.id_material, id_programming);
      const materialsAccept = allMaterialsAccept.filter(
        (item) => item.id_material == material.id_material
      );

      const action = getMaterialAction(storeData, materialsAccept, material.id_material);

      body.insertAdjacentHTML(
        "beforeend",
        generateMaterialRow(
          material,
          quantity_ftm,
          quantity_total,
          cost_ftm,
          cost_total,
          storeData,
          action,
          flag_op
        )
      );
    });

    // Footer
    $("#tblPOMaterialsFoot").html(generateMaterialFooter(totalCostFtm, totalCostTotal));
  };

  // Genera filas de la tabla de materiales
  const generateMaterialRow = (
    material,
    quantity_ftm,
    quantity_total,
    cost_ftm,
    cost_total,
    storeData,
    action,
    flag_op
  ) => {
    return `
    <tr>
      <td>${material.reference_material}</td>
      <td>${material.material}</td>
      <td>${quantity_ftm} ${material.abbreviation}</td>
      <td>${quantity_total} ${material.abbreviation}</td>
      <td>$${cost_ftm.toLocaleString("es-CO", { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
      <td>$${cost_total.toLocaleString("es-CO", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
      <td>${storeData.recieve}</td>
      <td>${storeData.for_recieve}</td>
      <td>${storeData.pending}</td>
      ${flag_op == 0 ? `<td>${action}</td>` : ""}
    </tr>
  `;
  };

  // Obtiene datos del almacén
  const getStoreData = (id_material, id_programming) => {
    const store = allStore.filter(
      (item) =>
        item.id_programming == id_programming && item.id_material == id_material
    );

    let recieve = 0,
      for_recieve = 0,
      pending = 0;

    store.forEach((item) => {
      recieve += parseFloat(item.quantity_component_user);
      for_recieve +=
        parseFloat(item.delivery_store) - parseFloat(item.quantity_component_user);
      if (item.delivery_pending > 0) {
        pending += parseFloat(item.delivery_pending);
      }
    });

    return { recieve, for_recieve, pending };
  };

  // Obtiene acción para materiales
  const getMaterialAction = (storeData, materialsAccept, id_material) => {
    const { recieve, for_recieve } = storeData;
    let action = "";

    if (for_recieve > 0) {
      action = `<button class="btn btn-info acceptMaterial" id="accept-${id_material}">Aceptar MP</button>`;
    } else if (recieve > 0) {
      action = `<a href="javascript:;">
                <i class="mdi mdi-playlist-check seeAcceptMP" data-toggle="tooltip" title="Ver Usuarios" style="font-size: 30px;color:black"></i>
              </a>`;
    }

    return action;
  };

  // Genera el footer de la tabla de materiales
  const generateMaterialFooter = (totalCostFtm, totalCostTotal) => {
    return `
    <tr>
      <td colspan="4"><strong>Total</strong></td>
      <td><strong>$${totalCostFtm.toLocaleString("es-CO", { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</strong></td>
      <td><strong>$${totalCostTotal.toLocaleString("es-CO", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</strong></td>
      <td colspan="3"></td>
    </tr>
  `;
  };

  // Genera tabla de procesos
  const loadProcessTable = async (data) => {
    // Similar lógica para los procesos que para materiales
    $("#tblPOProcessBody").empty();
    let body = document.getElementById("tblPOProcessBody");

    let dataPOProcess = [];

    if (flag_type_program == 0) {
      dataPOProcess.push(data);
    } else {
      dataPOProcess = await searchData(
        `/api/productionOrder/${data.id_order}/${data.id_product}`
      );
    }
 
    dataPOProcess.forEach((process) => {
      const { max_date_programming, min_date_programming, cost_payroll, cost_machine, process: processName, machine, id_programming, close_op } = process;

      let minDate = min_date_programming
        ? moment(min_date_programming).format(flag_type_program == 0 ? "DD/MM/YYYY hh:mm A" : "DD/MM/YYYY")
        : "";

      !minDate || minDate == "Invalid date" ? minDate = '' : minDate;
      
      let maxDate = flag_type_program == 0
        ? moment(max_date_programming).format("DD/MM/YYYY hh:mm A")
        : "";
      
      !maxDate || maxDate == "Invalid date" ? maxDate = '' : maxDate;

      const payrollCost = parseFloat(cost_payroll).toLocaleString("es-CO", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      const machineCost = parseFloat(cost_machine).toLocaleString("es-CO", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      const statusBadge =
        id_programming == 0
          ? `<i class="bi bi-shield-fill-x" data-toggle="tooltip" style="font-size:25px; color:#ee2020;"></i>`
          : close_op == 0
            ? `<span class="badge badge-warning" style="font-size:100%">En proceso</span>`
            : `<span class="badge badge-success" style="font-size:100%">Finalizado</span>`;

      const trPC = `
        <tr>
          <td>1</td>
          <td>${processName}</td>
          <td>${machine}</td>
          <td>${minDate}</td>
            ${flag_type_program == 0 ?
          `<td>${maxDate}</td>` : ''}
          <td>$${payrollCost}</td>
          <td>$${machineCost}</td>
          ${flag_type_program == 1 ?
          `<td>${statusBadge}</td>` : ''} 
        </tr>
      `;

      body.insertAdjacentHTML("beforeend", trPC);
    });

    if (data.flag_cancel == 0) {
      loadTblPartialsDelivery(id_programming);
      loadTblOPMaterial(id_programming);
    }
  };

  // Aceptar Material
  // $(document).on("click", ".acceptMaterial", function () {
  //   const idMaterial = $(this).attr("id").split("-")[1];

  //   bootbox.confirm({
  //     title: "Aceptar Materia Prima!",
  //     message: "¿Desea aceptar la cantidad recibida de este material?",
  //     buttons: {
  //       confirm: { label: "Si", className: "btn-success" },
  //       cancel: { label: "No", className: "btn-danger" }
  //     },
  //     callback: (result) => {
  //       if (!result) return;

  //       const totalReceived = allStore
  //         .filter(item => item.id_programming == id_programming && item.id_material == idMaterial)
  //         .reduce((sum, item) => sum + (parseFloat(item.delivery_store) - parseFloat(item.quantity_component_user)), 0);

  //       // const totalAccepted = allMaterialsAccept
  //       //   .filter(item => item.id_material == idMaterial)
  //       //   .reduce((sum, item) => sum + parseFloat(item.quantity), 0);

  //       const form = new FormData();
  //       form.append("idProgramming", id_programming);
  //       form.append("idMaterial", idMaterial);
  //       form.append("quantity", totalReceived);

  //       $.ajax({
  //         type: "POST",
  //         url: "/api/acceptMaterialReceive",
  //         data: form,
  //         contentType: false,
  //         cache: false,
  //         processData: false,
  //         success: function (resp) {
  //           messagePOD(resp)
  //         }
  //       });
  //     }
  //   });
  // });

  $(document).on("click", ".acceptMaterial", function () {
    const idMaterial = $(this).attr("id").split("-")[1];

    bootbox.confirm({
      title: "Aceptar Materia Prima!",
      message: "¿Desea aceptar la cantidad recibida de este material?",
      buttons: {
        confirm: { label: "Sí", className: "btn-success" },
        cancel: { label: "No", className: "btn-danger" },
      },
      callback: (result) => {
        if (!result) return;

        // Calcular cantidad total recibida
        const totalReceived = allStore
          .filter(
            (item) =>
              item.id_programming == id_programming &&
              item.id_material == idMaterial
          )
          .reduce(
            (sum, item) =>
              sum + (parseFloat(item.delivery_store) - parseFloat(item.quantity_component_user)),
            0
          );

        if (totalReceived <= 0) {
          bootbox.alert("No hay material para aceptar.");
          return;
        }

        const form = new FormData();
        form.append("idProgramming", id_programming);
        form.append("idMaterial", idMaterial);
        form.append("quantity", totalReceived);

        $.ajax({
          type: "POST",
          url: "/api/acceptMaterialReceive",
          data: form,
          contentType: false,
          cache: false,
          processData: false,
          success: function (resp) {
            messagePOD(resp);
          },
          error: function (xhr, status, error) {
            bootbox.alert(`Ocurrió un error: ${xhr.responseText || error}`);
          },
        });
      },
    });
  });

  const formatQuantity = (quantity, abbreviation) => {
    quantity = parseFloat(quantity);

    if (Math.abs(quantity) < 0.01)
      return quantity.toLocaleString("es-CO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 9,
      });

    if (Math.abs(quantity) > 1)
      return quantity.toLocaleString("es-CO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 9,
      });

    if (abbreviation == "UND")
      quantity = Math.floor(quantity).toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      });
    else
      quantity = quantity.toLocaleString("es-CO", {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
      });

    return quantity;
  };

  // Cerrar OP
  // $("#btnCloseOP").click(function (e) {
  //   e.preventDefault();

  //   let id_programming = sessionStorage.getItem("id_programming");

  //   if (op_to_store == "1") {
  //     let dataOPP = tblPartialsDelivery.DataTable().rows().data().toArray();

  //     if (dataOPP.length == 0) {
  //       toastr.error(
  //         "Ejecucion produccion o devolucion de materiales sin datos"
  //       );
  //       return false;
  //     }

  //     let recieve = 0;
  //     let for_recieve = 0;
  //     let pending = 0;
      
  //     let dataFTMaterials = JSON.parse(
  //       sessionStorage.getItem("dataFTMaterials")
  //     );
  //     let dataFT = dataFTMaterials.filter(
  //       (item) => item.id_product == dataPTOP.id_product
  //     );

  //     for (let i = 0; i < dataFT.length; i++) {
  //       let store = allStore.filter(
  //         (item) =>
  //           item.id_programming == id_programming &&
  //           item.id_material == dataFT[i].id_material
  //       );

  //       store.forEach((item) => {
  //         recieve += parseFloat(item.quantity_component_user);
  //         for_recieve += parseFloat(item.delivery_store) - parseFloat(item.quantity_component_user);
  //         item.delivery_pending == 0
  //           ? (pending = 0)
  //           : (pending += parseFloat(item.delivery_pending));
  //       });
  //     }

  //     if (pending > 0) {
  //       toastr.error("Materiales y Componentes no ejecutados");
  //       return false;
  //     }
  //   }

  //   let dataOP = {};
  //   dataOP["id_programming"] = id_programming;
  //   dataOP["numOP"] = dataPTOP.num_production;
  //   dataOP["route"] = parseInt(dataPTOP.route_programming) + 1;
  //   dataOP["status"] = 1;
  //   dataOP["id_order"] = dataPTOP.id_order;
  //   dataOP["id_product"] = dataPTOP.id_product;
  //   dataOP["id_machine"] = dataPTOP.id_machine;
  //   dataOP["quantity_programming"] = dataPTOP.quantity_programming;
  //   dataOP["min_date"] = dataPTOP.min_date_programming;
  //   dataOP["max_date"] = "";
  //   dataOP["min_programming"] = 0;
  //   dataOP["sim"] = 1;
  //   dataOP["new_programming"] = 1;

  //   bootbox.confirm({
  //     title: "Orden de Producción",
  //     message: `¿Está seguro de cerrar esta orden de produccion? Esta acción no se puede reversar`,
  //     buttons: {
  //       confirm: {
  //         label: "Si",
  //         className: "btn-success",
  //       },
  //       cancel: {
  //         label: "No",
  //         className: "btn-danger",
  //       },
  //     },
  //     callback: function (result) {
  //       if (result == true) {
  //         $.post(
  //           "/api/changeFlagOP",
  //           dataOP,
  //           function (resp, textStatus, jqXHR) {
  //             if (resp.success)
  //               sessionStorage.setItem("id_programming", resp.id_programming);

  //             messagePOD(resp);
  //           }
  //         );
  //       }
  //     },
  //   });
  // });

  $("#btnCloseOP").click(function (e) {
    e.preventDefault();

    let id_programming = sessionStorage.getItem("id_programming");

    if (op_to_store == "1") {
      let dataOPP = tblPartialsDelivery.DataTable().rows().data().toArray();

      if (dataOPP.length == 0) {
        toastr.error("Ejecución de producción o devolución de materiales sin datos");
        return false;
      }

      let recieve = 0,
        for_recieve = 0,
        pending = 0;

      let dataFTMaterials = JSON.parse(sessionStorage.getItem("dataFTMaterials"));
      if (!dataFTMaterials || dataFTMaterials.length === 0) {
        toastr.error("No hay datos de materiales disponibles.");
        return false;
      }

      let dataFT = dataFTMaterials.filter(
        (item) => item.id_product == dataPTOP.id_product
      );

      allStore
        .filter(
          (item) =>
            item.id_programming == id_programming &&
            dataFT.some((ft) => ft.id_material == item.id_material)
        )
        .forEach((item) => {
          recieve += parseFloat(item.quantity_component_user);
          for_recieve +=
            parseFloat(item.delivery_store) -
            parseFloat(item.quantity_component_user);
          pending += item.delivery_pending ? parseFloat(item.delivery_pending) : 0;
        });

      if (pending > 0) {
        toastr.error("Materiales y componentes no ejecutados");
        return false;
      }
    }

    let dataOP = {
      id_programming: id_programming,
      numOP: dataPTOP.num_production,
      route: parseInt(dataPTOP.route_programming) + 1,
      status: 1,
      id_order: dataPTOP.id_order,
      id_product: dataPTOP.id_product,
      id_machine: dataPTOP.id_machine,
      quantity_programming: dataPTOP.quantity_programming,
      min_date: dataPTOP.min_date_programming,
      max_date: "",
      min_programming: 0,
      sim: 1,
      new_programming: 1,
    };

    bootbox.confirm({
      title: "Orden de Producción",
      message: `¿Está seguro de cerrar esta orden de producción? Esta acción no se puede reversar.`,
      buttons: {
        confirm: { label: "Sí", className: "btn-success" },
        cancel: { label: "No", className: "btn-danger" },
      },
      callback: function (result) {
        if (result) {
          $.post(
            "/api/changeFlagOP",
            dataOP,
            function (resp) {
              if (resp.success) {
                sessionStorage.setItem("id_programming", resp.id_programming);
                toastr.success("Orden de Producción cerrada con éxito.");
              } else {
                toastr.error("Error al cerrar la Orden de Producción.");
              }
              messagePOD(resp);
            }
          ).fail(function (xhr, status, error) {
            toastr.error(`Error en la solicitud: ${xhr.responseText || error}`);
          });
        }
      },
    });
  });

  loadAllDataPO();

  messagePOD = async (data) => {
    const { success, error, info, message } = data;
    if (success) {
      loadAllDataPO();
      toastr.success(message);
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  // Materiales aceptados
  // $(document).on("click", ".seeAcceptMP", async function (e) {
  //   e.preventDefault();
  //   // Obtiene el elemento que fue clickeado
  //   const element = $(this)[0];

  //   // Obtiene todas las clases del elemento
  //   const classList = Array.from(element.classList);

  //   // Busca las clases que contienen 'programming-' y 'material-'
  //   const id_programming = classList
  //     .find((cls) => cls.startsWith("programming-"))
  //     .split("-")[1];
  //   const id_material = classList
  //     .find((cls) => cls.startsWith("material-"))
  //     .split("-")[1];

  //   let users = await searchData(
  //     `/api/materialsComponents/${id_programming}/${id_material}`
  //   );
  //   let rows = "";

  //   for (let i = 0; i < users.length; i++) {
  //     rows += `<tr>
  //                   <td>${i + 1}</td>
  //                   <td>${users[i].firstname}</td>
  //                   <td>${users[i].lastname}</td>
  //                   <td>${users[i].email}</td>
  //                   <td>
  //                       ${parseFloat(users[i].quantity).toLocaleString(
  //       "es-CO",
  //       {
  //         minimumFractionDigits: 0,
  //         maximumFractionDigits: 2,
  //       }
  //     )}
  //                   </td>
  //               </tr>`;
  //   }

  //   // Mostramos el mensaje con Bootbox
  //   bootbox.alert({
  //     title: "Usuarios",
  //     message: `
  //           <div class="container">
  //             <div class="col-12">
  //               <div class="table-responsive">
  //                 <table class="fixed-table-loading table table-hover">
  //                   <thead>
  //                     <tr>
  //                       <th>No</th>
  //                       <th>Nombre</th>
  //                       <th>Apellido</th>
  //                       <th>Email</th>
  //                       <th>Cantidad Aceptada</th>
  //                     </tr>
  //                   </thead>
  //                   <tbody>
  //                     ${rows}
  //                   </tbody>
  //                 </table>
  //               </div>
  //             </div> 
  //           </div>`,
  //     size: "large",
  //     backdrop: true,
  //   });
  //   return false;
  // });

  // // Descargar PDF
  // $(document).on("click", ".downloadPlaneProduct", function () {
  //   let key = this.id;
  //   let pdfUrl = dataPTOP[key];

  //   const link = document.createElement("a");
  //   link.href = pdfUrl;
  //   link.download = "plano.pdf"; // Nombre del archivo para descargar
  //   document.body.appendChild(link);
  //   link.click();
  //   document.body.removeChild(link);
  // });

  $(document).on("click", ".seeAcceptMP", async function (e) {
    e.preventDefault();

    const element = $(this)[0];
    const classList = Array.from(element.classList);

    const id_programming = classList
      .find((cls) => cls.startsWith("programming-"))
      ?.split("-")[1];
    const id_material = classList
      .find((cls) => cls.startsWith("material-"))
      ?.split("-")[1];

    if (!id_programming || !id_material) {
      toastr.error("Error al obtener la información del material.");
      return;
    }

    let users = [];
    try {
      users = await searchData(
        `/api/materialsComponents/${id_programming}/${id_material}`
      );
    } catch (error) {
      toastr.error("Error al obtener los datos. Por favor intente de nuevo.");
      return;
    }

    if (!users.length) {
      bootbox.alert({
        title: "Usuarios",
        message: "<p class='text-center'>No se encontraron datos para mostrar.</p>",
        size: "large",
        backdrop: true,
      });
      return;
    }

    const rows = generateTableRows(users);

    bootbox.alert({
      title: "Usuarios",
      message: `
      <div class="container">
        <div class="col-12">
          <div class="table-responsive">
            <table class="fixed-table-loading table table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Email</th>
                  <th>Cantidad Aceptada</th>
                </tr>
              </thead>
              <tbody>
                ${rows}
              </tbody>
            </table>
          </div>
        </div> 
      </div>`,
      size: "large",
      backdrop: true,
    });

    return false;
  });

  // Generar filas para la tabla
  function generateTableRows(users) {
    return users
      .map(
        (user, index) => `
      <tr>
        <td>${index + 1}</td>
        <td>${user.firstname}</td>
        <td>${user.lastname}</td>
        <td>${user.email}</td>
        <td>${parseFloat(user.quantity).toLocaleString("es-CO", {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2,
        })}</td>
      </tr>`
      )
      .join("");
  }

  // Descargar PDF
  $(document).on("click", ".downloadPlaneProduct", function () {
    let key = this.id;
    if (!dataPTOP[key]) {
      toastr.error("No se encontró el archivo para descargar.");
      return;
    }

    let pdfUrl = dataPTOP[key];

    const link = document.createElement("a");
    link.href = pdfUrl;
    link.download = "plano.pdf"; // Nombre del archivo para descargar
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });

});
