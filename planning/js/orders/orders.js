$(document).ready(function () {
  $(".cardAddOrders").hide();

  // Abrir card crear pedidos
  $("#btnNewOrder").click(function (e) {
    e.preventDefault();

    $(".cardImportOrder").hide(800);
    $(".cardAddOrders").toggle(800);
    $("#btnCreatePlanMachine").text("Crear");

    sessionStorage.removeItem("id_order");

    $("#formCreateOrder").trigger("reset");
    $("#btnCreateOrder").text("Crear");

    // Obtener la fecha actual
    const today = new Date();

    // Formatear la fecha al formato "YYYY-MM-DD"
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0"); // Enero es 0!
    const dd = String(today.getDate()).padStart(2, "0");
    const formattedDate = yyyy + "-" + mm + "-" + dd;

    // Asignar la fecha formateada al input
    document.getElementById("dateOrder").value = formattedDate;
  });

  $("#btnCreateOrder").click(function (e) {
    e.preventDefault();

    const idOrder = sessionStorage.getItem("id_order");
    const apiUrl = idOrder ? "/api/updateOrder" : "/api/addOrder";

    checkDataOrder(apiUrl, idOrder);
  });

  $(document).on("click", ".updateOrder", function () {
    $(".cardImportOrder").hide(800);
    $(".cardAddOrders").show(800);
    $("#btnCreateOrder").text("Actualizar");

    const row = $(this).closest("tr")[0];
    let data = tblOrder.fnGetData(row);

    sessionStorage.setItem("id_order", data.id_order);

    $("#dateOrder").val(data.date_order);
    $("#minDate").val(data.min_date);
    $("#maxDate").val(data.max_date);
    $(`#refProduct option[value=${data.id_product}]`).prop("selected", true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop(
      "selected",
      true
    );
    $(`#seller option[value=${data.id_seller}]`).prop("selected", true);
    $(`#client option[value=${data.id_client}]`).prop("selected", true);

    const dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
    const arr = dataProducts.find((item) => item.id_product == data.id_product);
    $("#inptQuantity").val(arr.accumulated_quantity.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }));

    $("#originalQuantity").val(data.original_quantity);

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataOrder = async (url, idOrder) => {
    let dateOrder = $("#dateOrder").val();
    let minDate = $("#minDate").val();
    let maxDate = $("#maxDate").val();
    let idProduct = parseFloat($("#refProduct").val());
    let idClient = parseFloat($("#client").val());
    let seller = parseFloat($("#seller").val());
    // let accumulated_quantity = parseFloat($('#inptQuantity').val().replace(".", ""));
    let originalQuantity = parseFloat($("#originalQuantity").val());

    let data = idProduct * idClient * originalQuantity * seller;

    if (
      isNaN(data) ||
      data <= 0 ||
      !dateOrder ||
      dateOrder == "" ||
      !minDate ||
      minDate == "" ||
      !maxDate ||
      maxDate == ""
    ) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (dateOrder > minDate) {
      toastr.error("Fecha de pedido mayor a la fecha minima");
      return false;
    }

    if (minDate > maxDate) {
      toastr.error("Fecha minima mayor a la fecha maxima");
      return false;
    }

    //let date = new Date().toISOString().split("T")[0];
    let date = new Date().toLocaleDateString("en-CA");

    if (minDate < date || maxDate < date) {
      toastr.error("Fecha por debajo de la fecha actual");
      return false;
    }

    if (idOrder) {
      if (dateOrder < date) {
        toastr.error("Fecha por debajo de la fecha actual");
        return false;
      }
    }

    // if (originalQuantity > accumulated_quantity) {
    //   toastr.error("Inventario de producto por debajo de");
    //   return false;
    // }

    let dataOrder = new FormData(formCreateOrder);

    if (idOrder) {
      dataOrder.append("idOrder", idOrder);
    }

    let resp = await sendDataPOST(url, dataOrder);

    message(resp);
  };

  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblOrder.fnGetData(row);

    let dataOrder = {};

    dataOrder["idOrder"] = data.id_order;
    dataOrder["idProduct"] = data.id_product;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este pedido? Esta acción no se puede reversar.",
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
            "/api/deleteOrder",
            dataOrder,
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
      $(".cardImportOrder, .cardAddOrders").hide(800);
      $("#formImportOrder, #formCreateOrder").trigger("reset");
      loadAllData();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
