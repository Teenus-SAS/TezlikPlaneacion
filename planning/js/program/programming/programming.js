$(document).ready(function () {
  let programming = [];
  // Obtener referencia producto
  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).prop("selected", true);
  });

  /* Ocultar panel crear programa de producción */
  $(".cardCreateProgramming").hide();

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

    $(".date").hide();
    $("#selectNameProduct").empty();
    $("#idMachine").empty();
    $("#idProcess").empty();
    $(".cardCreateProgramming").toggle(800);
    $("#btnCreateProgramming").html("Crear");
    $("#formCreateProgramming").trigger("reset");
  });

  /* Crear nuevo programa de produccion */
  $("#btnCreateProgramming").click(function (e) {
    e.preventDefault();
    let idProgramming = sessionStorage.getItem("id_programming");

    if (idProgramming == "" || idProgramming == null) {
      dataProgramming['id_programming'] = allTblData.length;
      checkdataProgramming(idProgramming);
    } else {
      dataProgramming['id_programming'] = idProgramming;
      checkdataProgramming(idProgramming);
    }
  });

  /* Actualizar programa de produccion */

  $(document).on("click", ".updateProgramming", async function (e) {
    $(".cardCreateProgramming").show(800);
    $("#btnCreateProgramming").html("Actualizar");
    
    let data = allTblData.find(item => item.id_programming == this.id);

    sessionStorage.setItem("id_programming", data.id_programming);
    $("#order").empty();
    $("#order").append(`<option disabled>Seleccionar</option>`);
    $("#order").append(
      `<option value ='${data.id_order}' selected> ${data.num_order} </option>`
    );
    $("#selectNameProduct").empty();
    $("#selectNameProduct").append(`<option disabled>Seleccionar</option>`);
    $("#selectNameProduct").append(
      `<option value ='${data.id_product}' selected> ${data.product} </option>`
    );
    $("#quantityOrder").val(data.quantity_order.toLocaleString());
    $("#quantityMissing").val(data.accumulated_quantity.toLocaleString());
    // $("#quantityMP").val(data.accumulated_quantity.toLocaleString());
    let productsMaterials = allProductsMaterials.filter(item => item.id_product == data.id_product);
    productsMaterials = productsMaterials.sort((a, b) => a.quantity - b.quantity);
    $('#quantityMP').val(productsMaterials[0].quantity.toLocaleString('es-CO', { maximumfractiondigits: 2 }));
 
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

    document.getElementById("minDate").readOnly = false;
    $(".date").show(800);
    $("#btnCreateProgramming").show(800);

    max_date = convetFormatDateTime(data.max_date);
    min_date = convetFormatDateTime(data.min_date);

    $("#minDate").val(min_date);
    $("#maxDate").val(max_date);

    // dataProgramming = new FormData(formCreateProgramming);
    dataProgramming = [];
    dataProgramming['id_order'] = data.id_order;
    dataProgramming['num_order'] = data.num_order;
    dataProgramming['client'] = data.client;
    dataProgramming['reference'] = data.reference;
    dataProgramming['product'] = data.product;
    dataProgramming['min_date'] = data.min_date;
    dataProgramming['max_date'] = data.max_date;
    dataProgramming['min_programming'] = data.min_programming;
    
    $(document).one("click", "#minDate", function (e) {
      e.preventDefault();

      document.getElementById("minDate").type = "date";
    });

    $("#minDate").change(function (e) {
      e.preventDefault();

      if (!this.value) {
        toastr.error("Ingrese fecha inicial");
        return false;
      }

      let min_date = convetFormatDate(this.value);

      sessionStorage.setItem("minDate", min_date);
      dataProgramming['min_date'] = min_date;
      // dataProgramming.append("minDate", min_date);
      calcMaxDate(min_date, 0, 2);
    });

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $(document).on("blur", "#quantity", function () {
    sessionStorage.removeItem("minDate");
    checkData(2, this.id);
  });

  /* Revision data programa de produccion */
  checkdataProgramming = async (idProgramming) => {
    let id_order = $('#order').val();
    let id_product = parseInt($('#selectNameProduct').val());
    let quantityMissing = parseInt($('#quantityMissing').val().replace('.', ''));
    let quantityProgramming = parseInt($('#quantity').val());
    let quantityOrder = parseInt($('#quantityOrder').val());
    let machine = $('#idMachine :selected').text().trim();
    let id_process = $('#idProcess').val();
    let process = $('#idProcess :selected').text().trim();

    dataProgramming['machine'] = machine;
    dataProgramming['id_process'] = id_process;
    dataProgramming['process'] = process;
    dataProgramming['quantity_order'] = quantityOrder;
    dataProgramming['quantity_programming'] = quantityProgramming;
    dataProgramming['status'] = 'Programado';
    
    for (let i = 0; i < allOrders.length; i++) {
      if (allOrders[i].id_order == id_order) {
        allOrders[i].status = 'Programado';

        let quantity = 0;
        
        if (quantityProgramming < allOrders[i].original_quantity) {
          quantity = allOrders[i].original_quantity - quantityProgramming;
          if (!allOrders[i]['quantity_programming']) {
            allOrders[i].quantity_programming = quantity;
            allOrders[i].accumulated_quantity_order = quantity;
            allOrders[i].accumulated_quantity = quantity;
          }
          else {
            allOrders[i].accumulated_quantity_order = allOrders[i].quantity_programming - quantityProgramming;
            allOrders[i].accumulated_quantity = allOrders[i].quantity_programming - quantityProgramming;
          }
        }
      }
    }

    for (let i = 0; i < allOrdersProgramming.length; i++) {
      if (allOrdersProgramming[i].id_order == id_order) {
        allOrdersProgramming[i].status = 'Programado';

        quantityMissing = 0;
        
        if (quantityProgramming < allOrdersProgramming[i].original_quantity) {
          quantityMissing = allOrdersProgramming[i].original_quantity - quantityProgramming;
          if (!allOrdersProgramming[i]['quantity_programming'])
            allOrdersProgramming[i].quantity_programming = quantityMissing;
          else {
            allOrdersProgramming[i].accumulated_quantity_order = allOrdersProgramming[i].quantity_programming - quantityProgramming;
            quantityMissing = allOrdersProgramming[i].accumulated_quantity_order;
          }
        }
      }
    }

    for (let i = 0; i < allOrders.length; i++) {
      if (allOrders[i].id_product == id_product) {        
        if (quantityMissing == 0) {
          allOrders.splice(i, 1);
        }
      }
    }

    for (let i = 0; i < allOrdersProgramming.length; i++) {
      if (allOrdersProgramming[i].id_product == id_product) {         
        if (quantityMissing == 0) {
          allOrdersProgramming.splice(i, 1);
        }
      }
    }

    dataProgramming['accumulated_quantity'] = quantityMissing;
    
    process = allProcess.find(item => item.id_product == id_product && item.id_order == id_order);

    if (quantityMissing - quantityProgramming > 0)
      dataProgramming['route'] = `${process.route1}, ${process.route1 + 1}`;
 
    hideCardAndResetForm();

    if (idProgramming == null) {
      toastr.success('Programa de producción creado correctamente');
    } else {
      allTblData.splice(idProgramming, 1);
      toastr.success('Programa de producción modificado correctamente');
    }
    
    allTblData.push(dataProgramming);

    loadTblProgramming(allTblData);
    dataProgramming = [];
  };

  /* Eliminar programa de produccion */

  deleteFunction = (id) => {
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
          allTblData.splice(id, 1);
          loadTblProgramming(allTblData);
          toastr.success('Programa de producción eliminado correctamente');
        }
      },
    });
  };

  /* Cambiar estado */
  $(document).on("click", ".changeStatus", function () {
    let data = allProgramming.find(item => item.id_programming == this.id);

    let dataProgramming = {};
    dataProgramming["idProgramming"] = data.id_programming;
    dataProgramming["idOrder"] = data.id_order;

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
          $.post(
            `/api/changeStatusProgramming`,
            dataProgramming,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
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

  $("#btnSaveProgramming").click(function (e) {
    e.preventDefault();

    if (programming.length == 0) {
      toastr.error("No hay ningún dato para guardar");
      return false;
    }

    $.ajax({
      type: "POST",
      url: "/api/changeStatusProgramming",
      data: { data: programming },
      success: function (resp) {
        programming = [];
        $("#changeStatusProgramming").modal("hide");

        message(resp);
      },
    });
  });

  $(".btnCloseStatusProgramming").click(function (e) {
    e.preventDefault();
    programming = [];
    $("#changeStatusProgramming").modal("hide");
    $("#tblStatusProgrammingBody").empty();
  });

  /* Mensaje de exito */
  message = async (data) => {
    try {
      if (data.success) {
        hideCardAndResetForm();
        toastr.success(data.message);
        await loadAllDataProgramming(1);
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
    $("#searchMachine").val("0");
  };

  loadDataMachines(3);
});
