$(document).ready(function () {
  /* Ocultar panel crear producto */

  $(".cardAddRequisitions").hide();

  /* Abrir panel crear producto */

  $("#btnNewRequisition").click(function (e) {
    e.preventDefault();

    $(".cardImportRequisitions, .cardDescription, .cardRequired").hide(800);
    $(".cardTableConfigMaterials, .cardSelect").show(800);
    $(".cardAddRequisitions").toggle(800);
    $("#btnAddRequisition").text("Asignar");
    document.getElementById("requestedQuantity").readOnly = false;

    sessionStorage.removeItem("id_requisition");

    $("#formAddRequisition").trigger("reset");
  });

  /* Adicionar nueva materia prima */

  $("#btnAddRequisition").click(function (e) {
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

    $("#client option").removeAttr("selected");
    $(`#client option[value='0']`).prop("selected", true);
    $("#rMQuantity").val("");
    $("#rMAverage").val("");

    let dataStock = JSON.parse(sessionStorage.getItem("stock"));
    let arr = dataStock.filter((item) => item.id_material == this.value);

    setInputClient(arr);

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
    $(`#client option[value=${item.id_provider}]`).prop("selected", true);
    $("#rMQuantity").val(`${item.min_quantity} ${item.abbreviation}`);
    $("#rMAverage").val(
      isMultiple
        ? parseFloat(item.max_term) - parseFloat(item.min_term)
        : item.average
    );
  }

  $("#refMaterial").change(handleMaterialChange);
  $("#material").change(handleMaterialChange);

  $("#client").change(function (e) {
    e.preventDefault();
    let id_material = $("#material").val();

    let dataStock = JSON.parse(sessionStorage.getItem("stock"));
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

  $(document).on("click", ".updateRequisition", async function (e) {
    $(".cardImportRequisitions").hide(800);
    $(".cardAddRequisitions, .cardRequired, .cardDescription").show(800);
    $("#btnAddRequisition").text("Actualizar");
    $(".cardSelect").hide();

    //Obtener data
    const row = $(this).closest("tr")[0];
    const data = tblRequisitions.fnGetData(row);

    sessionStorage.setItem("id_requisition", data.id_requisition);

    // Seleccionar los valores correspondientes en los campos
    $(`#refMaterial, #material`).val(data.id_material).prop("selected", true);
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
    /* if (data.application_date != "0000-00-00" && data.application_date)
      $("#applicationDate").val(data.application_date);
    if (data.delivery_date != "0000-00-00" && data.delivery_date)
      $("#deliveryDate").val(data.delivery_date);
    if (data.purchase_order != "0000-00-00" && data.purchase_order)
      $("#purchaseOrder").val(data.purchase_order); */

    let dataStock = JSON.parse(sessionStorage.getItem("stock"));
    let arr = dataStock.filter((item) => item.id_material == data.id_material);

    setInputClient(arr);

    // Selección del cliente
    $(`#client option[value=${data.id_provider}]`).prop("selected", true);

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
        ? Math.floor(data.quantity_required)
        : data.quantity_required;

    //Asignacion
    $("#requiredQuantity").val(`${quantity_required} ${data.abbreviation}`);

    let quantity_requested = data.quantity_requested;

    // Formateo de cantidad solicitada
    quantity_requested =
      data.abbreviation === "UND"
        ? Math.floor(data.quantity_requested)
        : data.quantity_requested;

    //Asignacion
    $("#requestedQuantity").val(quantity_requested);

    // Animación de desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataRequisition = async (url, idRequisition) => {
    const material = $("#material").val();
    const provider = $("#client").val();
    const applicationDate = $("#applicationDate").val();
    const deliveryDate = $("#deliveryDate").val();
    // const quan = $('#requiredQuantity').val();
    const r_quan = $("#requestedQuantity").val();

    const data = r_quan * material * provider;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (applicationDate > deliveryDate) {
      toastr.error(
        "Ingrese una fecha de solicitud menor a la fecha de entrega"
      );
      return false;
    }

    let dataRequisition = new FormData(formAddRequisition);
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
    dataRequisition["idRequisition"] = data.id_requisition;
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

  $(document).on("click", ".changeDate", function (e) {
    e.preventDefault();

    const date = new Date().toISOString().split("T")[0];

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblRequisitions.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Ingreso!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date" max="${date}"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
      buttons: {
        confirm: {
          label: "Agregar",
          className: "btn-success",
        },
        cancel: {
          label: "Cancelar",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          let date = $("#date").val();

          if (!date) {
            toastr.error("Ingrese los campos");
            return false;
          }

          let form = new FormData();
          form.append("idRequisition", data.id_requisition);
          form.append("idMaterial", data.id_material);
          form.append("date", date);

          $.ajax({
            type: "POST",
            url: "/api/saveAdmissionDate",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              message(resp);
            },
          });
        }
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportRequisitions, .cardAddRequisitions").hide(800);
      $("#formImportRequisitions, #formAddRequisition").trigger("reset");

      loadAllData(null, null, null);
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
