$(document).ready(function () {
  /* Ocultar panel crear producto */

  $(".cardAddRequisitionsMaterials").hide();
  $(".cardAddRequisitionsProducts").hide();

  /* Abrir panel crear producto */
  // Materiales
  $("#btnNewRequisition").click(function (e) {
    e.preventDefault();

    $(".cardImportRequisitions, .cardDescription, .cardRequired").hide(800);
    $(".cardTableConfigMaterials, .cardSelect").show(800);
    $(".cardAddRequisitionsMaterials").toggle(800);
    $("#btnAddRequisitionMP").text("Asignar");
    document.getElementById("requestedQuantityMP").readOnly = false;

    sessionStorage.removeItem("id_requisition");

    $("#formAddRequisitionMaterials").trigger("reset");
  });

  /* Adicionar nueva materia prima */

  $("#btnAddRequisitionMP").click(function (e) {
    e.preventDefault();

    const idRequisition = sessionStorage.getItem("id_requisition") || null;
    const apiUrl = idRequisition
      ? "/api/updateRequisition"
      : "/api/addRequisition";

    checkDataRequisition(apiUrl, idRequisition);
  });

  $(".cardSearchDate").hide();

  $("#btnOpenSearchDate").click(function (e) {
    e.preventDefault();

    $(".cardSearchDate").toggle(800);
    $("#formSearchDate").trigger("reset");
    let date = new Date().toISOString().split("T")[0];

    $("#lastDate").val(date);

    let maxDate = document.getElementById("lastDate");
    let minDate = document.getElementById("firtsDate");

    maxDate.setAttribute("max", date);
    minDate.setAttribute("max", date);
  });

  $("#btnSearchDate").click(async function (e) {
    e.preventDefault();

    let firtsDate = $("#firtsDate").val();
    let lastDate = $("#lastDate").val();

    if (!firtsDate || !lastDate) {
      toastr.error("Ingrese los campos");
      return false;
    }

    loadAllData(3, firtsDate, lastDate);
  });

  function handleMaterialChange(event) {
    event.preventDefault();

    $("#clientMP option").removeAttr("selected");
    $(`#clientMP option[value='0']`).prop("selected", true);
    $("#rMQuantity").val("");
    $("#rMAverage").val("");

    let dataStock = JSON.parse(sessionStorage.getItem("MPStock"));
    let arr = dataStock.filter((item) => item.id_material == this.value);

    setInputClient(arr, 'clientMP');

    if (arr.length == 1) updateClientSelection(arr[0]);
    else if (arr.length > 1) {
      arr.sort((a, b) => a.average - b.average);

      // Verificar si todos los tiempos promedio son iguales
      const firstValue = arr[0]["average"];
      const allSame = arr.every((item) => item["average"] === firstValue);

      if (allSame) arr.sort((a, b) => a.min_quantity - b.min_quantity);

      updateClientSelection(arr[0], true);
    }
  }

  function updateClientSelection(item, isMultiple = false) {
    $(`#clientMP option[value=${item.id_provider}]`).prop("selected", true);
    $("#rMQuantity").val(`${item.min_quantity} ${item.abbreviation}`);
    $("#rMAverage").val(
      isMultiple
        ? parseFloat(item.max_term) - parseFloat(item.min_term)
        : item.average
    );
  }

  $("#refMaterial").change(handleMaterialChange);
  $("#material").change(handleMaterialChange);

  $("#clientMP").change(function (e) {
    e.preventDefault();
    let id_material = $("#material").val();

    let dataStock = JSON.parse(sessionStorage.getItem("MPStock"));
    let arr = dataStock.find(
      (item) =>
        item.id_material == id_material && item.id_provider == this.value
    );

    if (arr) {
      $("#rMQuantity").val(`${arr.min_quantity} ${arr.abbreviation}`);
      $("#rMAverage").val(arr.average);
    }
  });

  /* Actualizar productos materials */

  $(document).on("click", ".updateRequisitionMaterial", async function (e) {
    $(".cardImportRequisitions, .cardSelect, .cardAddRequisitionsProducts").hide(800);
    $(".cardAddRequisitionsMaterials, .cardRequired, .cardDescription").show(800);
    $("#btnAddRequisitionMP").text("Actualizar");

    //Obtener data
    const row = $(this).closest("tr")[0];
    const data = tblRequisitions.fnGetData(row);

    sessionStorage.setItem("id_requisition", data.id_requisition_material);

    // Seleccionar los valores correspondientes en los campos
    $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
    $(`#material option[value=${data.id_material}]`).prop("selected", true);
 
    $("#referenceMName").val(data.reference);
    $("#materialName").val(data.material);

    // Validación y asignación de fechas
    ["application_date", "delivery_date", "purchase_order"].forEach((field) => {
      if (data[field] && data[field] !== "0000-00-00") {
        $(
          `#${field
            .split("_")
            .map((word, index) =>
              index === 0 ? word : word[0].toUpperCase() + word.slice(1)
            )
            .join("")}`
        ).val(data[field]);
      }
    });

    let dataStock = JSON.parse(sessionStorage.getItem("MPStock"));
    let arr = dataStock.filter((item) => item.id_material == data.id_material);

    setInputClient(arr, 'clientMP');

    // Selección del cliente
    $(`#clientMP option[value=${data.id_provider}]`).prop("selected", true);

    // Lógica para cantidades mínimas y promedio
    if (arr.length == 1 && data.id_provider != 0) {
      $("#rMQuantity").val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
      $("#rMAverage").val(arr[0].average);
    } else if (arr.length > 1) {
      arr = arr.sort((a, b) => a.average - b.average);

      // Verificar si todos los tiempos promedio son iguales
      const firstValue = arr[0]["average"];
      const allSame = arr.every((item) => item["average"] === firstValue);

      if (allSame) arr = arr.sort((a, b) => a.min_quantity - b.min_quantity);

      $("#rMQuantity").val(`${arr[0].min_quantity} ${arr[0].abbreviation}`);
      $("#rMAverage").val(
        parseFloat(arr[0].max_term) - parseFloat(arr[0].min_term)
      );
    }

    // Formateo de cantidad requerida
    let quantity_required = parseFloat(data.quantity_required).toLocaleString(
      "es-CO",
      { minimumFractionDigits: 2, maximumFractionDigits: 2 }
    );

    // Formateo de cantidad requerida
    quantity_required =
      data.abbreviation === "UND"
        ? data.quantity_required = data.quantity_required.toLocaleString("es-CO", {
          maximumFractionDigits: 0,
        })
        : data.quantity_required = data.quantity_required.toLocaleString("es-CO", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });

    //Asignacion
    $("#requiredQuantityMP").val(`${quantity_required} ${data.abbreviation}`);

    let quantity_requested = data.quantity_requested;

    // Formateo de cantidad solicitada
    quantity_requested =
      data.abbreviation === "UND"
        ? Math.floor(data.quantity_requested)
        : data.quantity_requested;

    //Asignacion
    $("#requestedQuantityMP").val(quantity_requested);

    // Animación de desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataRequisition = async (url, idRequisition) => {
    const material = parseInt($("#material").val());
    const provider = parseInt($("#clientMP").val());
    const applicationDate = $("#applicationDateMP").val();
    const deliveryDate = $("#deliveryDateMP").val();
    // const quan = $('#requiredQuantity').val();
    const r_quan = parseFloat($("#requestedQuantityMP").val());

    const data = r_quan * material * provider;

    if (isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (!applicationDate && !deliveryDate) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (applicationDate > deliveryDate) {
      toastr.error(
        "Ingrese una fecha de solicitud menor a la fecha de entrega"
      );
      return false;
    }

    let dataRequisition = new FormData(formAddRequisitionMaterials);
    if (idRequisition) dataRequisition.append("idRequisition", idRequisition);
    let resp = await sendDataPOST(url, dataRequisition);

    message(resp);
  };
  
  /* Eliminar materia prima */
  deleteFunction = (op) => {
    //obtener data
    const row = $(this.activeElement).closest("tr")[0];
    const data = tblRequisitions.fnGetData(row);

    let dataRequisition = {};
 
    dataRequisition["idRequisition"] = data.id_requisition_material;
    dataRequisition["idMaterial"] = data.id_material;

    dataRequisition["op"] = op;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta requisicion? Esta acción no se puede reversar.",
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
            "/api/deleteRequisition",
            dataRequisition,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Cancelar Compra */
  cancelRQFunction = () => {
    //obtener data
    const row = $(this.activeElement).closest("tr")[0];
    const data = tblRequisitions.fnGetData(row);

    let dataRequisition = {};
     
    dataRequisition["idRequisition"] = data.id_requisition_material;
    dataRequisition["idMaterial"] = data.id_material;

    dataRequisition["idUser"] = 0;
    dataRequisition["idProvider"] = data.id_provider;
    dataRequisition["applicationDate"] = '0000-00-00';
    dataRequisition["deliveryDate"] = '0000-00-00';
    dataRequisition["requestedQuantity"] = 0;
    dataRequisition["purchaseOrder"] = '';

    bootbox.confirm({
      title: "Cancelar",
      message:
        "Está seguro de cancelar esta compra? Esta acción no se puede reversar.",
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
            "/api/updateRequisition",
            dataRequisition,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportRequisitions, .cardAddRequisitionsMaterials, .cardAddRequisitionsProducts").hide(800);
      $("#formImportRequisitions, #formAddRequisitionMaterials, #formAddRequisitionProducts").trigger("reset");

      loadAllData(null, null, null);
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
